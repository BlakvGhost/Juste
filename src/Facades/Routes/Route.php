<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Routes\Dependance;

class Route extends Dependance
{
    use Utilities;

    private function getRoute(): string
    {
        $request_uri = $this->server("REQUEST_URI");
        $route = strtolower(trim($request_uri, '/'));

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
                global $_BRAVO;

                $params = $routes['params'];

                $param = $params ? $this->getParamFromUrl()['param'] : null;

                $injectable = $params ? $this->resolveDependance($routes['params'], $param) : null;

                $_BRAVO['activeRoute'] = [
                    'controller' => $controller,
                    'params' => $params,
                    'param' => $param,
                    'injectable' => $injectable
                ];
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

        $actives = [];

        foreach ($routes as $key => $r) {
            $controller_arr = [$controller, $r['function']];
            $active = $this->loadRoute($r['route'], $controller_arr, $r['method']);

            $actives[] = [
                'route' => $r['route'],
                'controller' => $controller_arr,
                'active' => $active
            ];

            $utils = new RouteUtils($r['route'], $controller_arr);
            $utils->name($route . '.' . $r['function']);
        }

        $active = array_filter($actives, function ($elt) {
            return $elt['active'];
        });

        return $active[0] ?? [];
    }

    /**
     * Set a GET route
     * @param string $route
     * @param array $controller
     * @return RouteUtils
     */
    public static function get(string $route, array $controller): RouteUtils
    {
        $static = new static();
        //dd("GET METHOD");
        $isActive = $static->loadRoute($route, $controller, "GET");
        return new RouteUtils($route, $controller, $isActive);
    }

    /**
     * Set POST route
     */
    public static function post(string $route, array $controller): RouteUtils
    {
        $static = new static();

        $isActive = $static->loadRoute($route, $controller, "POST");
        return new RouteUtils($route, $controller, $isActive);
    }

    public static function any($route, $controller): RouteUtils
    {
        $static = new static();

        $isActive = $static->loadRoute($route, $controller, "any");
        return new RouteUtils($route, $controller, $isActive);
    }

    public static function resource(string $route, string $controller)
    {
        $static = new static();

        $isActive = $static->loadResoucesRoute($route, $controller);

        return new RouteUtils(
            $isActive['route'] ?? $route,
            $isActive['controller'] ?? [$controller],
            $isActive['route'] ?? 0
        );
    }
}
