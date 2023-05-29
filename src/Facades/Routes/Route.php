<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Routes\Dependance;

class Route extends Dependance
{
    private function getRoute(): string
    {
        $request_uri = $this->server("REQUEST_URI");
        $route = strtolower(trim($request_uri, '/'));

        $this->cors(explode('/', $route)[0]);

        return explode('?', $route)[0];
    }

    private function getRouteNameAndParams(string $route): array
    {
        $segments = explode(':', trim($route, '/'));
        return [
            'route' => explode('?', $segments[0])[0],
            'params' => $segments[1] ?? null,
        ];
    }

    private function getParamFromUrl(): array
    {
        $route = $this->getRoute();
        $route = explode('/', $route);
        $param = array_pop($route);
        $route = implode('/', $route);
        return [
            'route' => explode('?', $route)[0],
            'param' => $param,
        ];
    }

    private function isActiveRoute(string $route, $param = false): bool
    {
        $route = trim($route, '/');
        if ($param) {
            $routes = $this->getParamFromUrl();
            return $routes['route'] == $route;
        }

        return $this->getRoute() == $route;
    }

    private function loadRoute(string $route, array $controller, string $method): bool
    {
        $routes = $this->getRouteNameAndParams($route);
        $params = $routes['params'] ?? null;
        $active = !1;

        if ($this->isActiveRoute($routes['route'], $params)) {

            if (($this->server("REQUEST_METHOD") == $method) || $method == 'any') {
                $function = $controller[1];
                $instance = new $controller[0]();

                if ($routes['params']) {

                    $param = $this->getParamFromUrl()['param'];

                    $injectable = $this->resolveDependance($routes['params'], $param);

                    if ($injectable) {
                        $payloads = $instance->$function($injectable);

                        setPayloads($payloads);
                    }

                    if (!$injectable) {
                        $payloads = $instance->$function($param);
                        setPayloads($payloads);
                    }
                } else {
                    $payloads =
                        $instance->$function();
                    setPayloads($payloads);
                }
                $active = 1;
            }
        }
        return $active;
    }

    private function loadResoucesRoute(string $route, string $controller)
    {
        $routes = [
            ['route' => "{$route}", 'function' => 'index', "method" => 'GET'],
            ['route' => "{$route}/create", 'function' => 'create', "method" => 'GET'],
            ['route' => "{$route}/", 'function' => 'store', "method" => 'POST'],
            ['route' => "{$route}/:user", 'function' => 'show', "method" => 'GET'],
            ['route' => "{$route}/edit/:user", 'function' => 'edit', "method" => 'GET'],
            ['route' => "{$route}/:user", 'function' => 'update', "method" => 'PUT'],
            ['route' => "{$route}/:user", 'function' => 'destroy', "method" => 'DELETE']
        ];

        foreach ($routes as $key => $r) {
            $controller_arr = [$controller, $r['function']];
            $this->loadRoute($r['route'], $controller_arr, $r['method']);

            $utils = new RouteUtils($r['route'], $controller_arr);
            $utils->name($route . $r['function']);
        }
    }

    /**
     * Set a GET route
     * @param string $route
     * @param array $controller
     * @return RouteUtils
     */
    public static function get(string $route, array $controller)
    {
        $static = new static();

        $isActive = $static->loadRoute($route, $controller, "GET");
        return new RouteUtils($route, $controller, $isActive);
    }

    /**
     * Set POST route
     */
    public static function post(string $route, array $controller)
    {
        $static = new static();

        $isActive = $static->loadRoute($route, $controller, "POST");
        return new RouteUtils($route, $controller, $isActive);
    }

    public static function any($route, $controller)
    {
        $static = new static();

        $isActive = $static->loadRoute($route, $controller, "any");
        return new RouteUtils($route, $controller, $isActive);
    }

    public static function resource(string $route, string $controller)
    {
        $static = new static();

        $static->loadResoucesRoute($route, $controller);
        return;
    }

    public static function cors(string $url)
    {
        if ($url == 'api') {
            require_once BASE_URL . DS . 'config/cors.php';
        }
    }
}