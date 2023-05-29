<?php

$debugInt = 0;

function env(string $name, string | int $default = null): string
{
    return $_ENV[$name] ?? $default;
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

/**
 * Retourne la route d'un url selon son alias
 * @param string $alias The alias of the Route
 */
function route(string $alias): string
{
    $routes = isset($_SESSION['routes']) ? $_SESSION['routes'] : [];

    return isset($routes[$alias]) ? '/' . $routes[$alias] : '/';
}

function setPayloads($payloadsData)
{
    $GLOBALS['payloads'] = $payloadsData;
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
    //var_dump($replaceVariables);die();

    if (file_exists($viewPath)) {
        ob_start();
        include VIEW_PATH . DS . 'index.php';;
        $output = ob_get_clean();

        $view = preg_replace_callback('/\{\{\s*\$([^}]+)\s*\}\}/', $replaceVariables, $output);

        echo $view;
    } else {
        echo 'Erreur : la vue ' . $viewPath . ' n\'existe pas.';
    }
}
