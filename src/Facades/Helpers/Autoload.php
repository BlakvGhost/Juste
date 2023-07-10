<?php

$debugInt = 0;

function env(string $name, string | int $default = null): string
{
    return $_ENV[$name] ?? $default;
}

/**
 * make var_dump() and exit() in pre tag
 */
function dd($value, ...$args)
{
    echo "<pre>";
    var_dump($value, ...$args);
    echo "</pre>";
    die();
}

/**
 * Prends un nom de session et renvois le message contenu dans la clée au niveau de la variable globale SESSION puis vide cette variable
 */
function session($name, $del = true)
{
    $message = isset($_SESSION[$name]) ? $_SESSION[$name] : null;

    if ($del) unset($_SESSION[$name]);

    return $message;
}

function getBravo(string $key = null): string | array
{
    global $_BRAVO;

    return $key ? $_BRAVO[$key] ?? [] : $_BRAVO;
}

function setBravo(string $key, mixed $data): string | array
{
    global $_BRAVO;

    return $_BRAVO[$key] = $data;
}

function updateBravo(string $key, array $data): array
{
    global $_BRAVO;

    $_key = $_BRAVO[$key] ?? [];

    return $_BRAVO[$key] = array_merge($_key, $data);
}

/**
 * Retourne la route d'un url selon son alias
 * @param string $alias The alias of the Route
 */
function route(string $alias): string
{
    $routes = getBravo('routes');

    return isset($routes[$alias]) ? '/' . $routes[$alias] : '/';
}

function debugWithInt(): string
{
    global $debugInt;

    return ($debugInt += 1) . '<br/>';
}

function loadView(array $context)
{
    $viewPath = $context['view_path'];
    $data = $context['context'];
    $pageTitle = $context['title'];

    extract($data);
    array_push($data, $pageTitle);

    $replaceVariables = function ($matches) use ($data) {
        $variableName = trim($matches[1]);
        return isset($data[$variableName]) ? $data[$variableName] : '';
    };

    if (file_exists($viewPath)) {
        ob_start();
        $base = VIEW_PATH . DS . 'index.php';
        
        if (file_exists($base)) {
            include($base);
        } else {
            include($viewPath);
        }
        $output = ob_get_clean();

        $view = preg_replace_callback('/\{\{\s*\$([^}]+)\s*\}\}/', $replaceVariables, $output);

        echo $view;
    } else {
        echo 'Erreur : la vue ' . $viewPath . ' n\'existe pas.';
    }
}

function loadRoute()
{
    if ($activeRoute = getBravo('activeRoute')) {
        $controller = $activeRoute['controller'];

        $middlewares = $activeRoute['middlewares'] ?? [];

        foreach ($middlewares as $middleware) {
            $instance = new $middleware();
            $middleware = $instance->handle();

            if (!$middleware) {
                return;
            }
        }

        $function = $controller[1];
        $instance = new $controller[0]();

        $params = $activeRoute['params'];

        if ($params) {

            $param = $activeRoute['param'];

            $injectable = $activeRoute['injectable'];

            if ($injectable) {
                $payloads = $instance->$function($injectable);
            }

            if (!$injectable) {
                $payloads = $instance->$function($param);
            }
        } else {
            $payloads = $instance->$function();
        }

        setBravo('payloads', $payloads);
    }
}
