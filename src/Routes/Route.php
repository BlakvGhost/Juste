<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Helpers\Common;

class Route extends Common
{
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
        if ($param) {
            $routes = $this->getParamFromUrl();
            return $routes['route'] == $route;
        }
        return $this->getRoute() == $route;
    }

    private function loadRoute(string $route, array $controller, string $method)
    {
        $routes = $this->getRouteNameAndParams($route);

        if ($this->isActiveRoute($routes['route'])) {

            if (($this->server("REQUEST_METHOD") == $method) || $method == 'any') {
                $function = $controller[1];
                $instance = new $controller[0]();

                if ($routes['params']) {

                    $param = $this->getParamFromUrl()['param'];

                    $payloads = $instance->$function($param);
                    setPayloads($payloads);
                } else {
                    $payloads =
                        $instance->$function();
                    setPayloads($payloads);
                }
            }
        }
        return null;
    }

    private function loadResoucesRoute(string $route, string $controller)
    {
        $route = $this->getRouteNameAndParams($route);

        $routeName = $route['route'];
        $routes = [
            ['route' => "{$routeName}", 'function' => 'index', "param" => null],
            ['route' => "{$routeName}/create", 'function' => 'create', "param" => null],
            ['route' => "{$routeName}/store", 'function' => 'store', "param" => null],
            ['route' => "{$routeName}", 'function' => 'show', "param" => 'pk'],
            ['route' => "{$routeName}/edit", 'function' => 'edit', "param" => 'pk'],
            ['route' => "{$routeName}/update", 'function' => 'update', "param" => 'pk'],
            ['route' => "{$routeName}/delete", 'function' => 'destroy', "param" => 'pk']
        ];

        foreach ($routes as $key => $r) {
            //$this->dd($r['route'], $r['param']);
            if ($this->isActiveRoute($r['route'], $r['param'])) {
                $function = $r['function'];
                $instance = new $controller();

                if ($r['param']) {

                    $param = $this->getParamFromUrl()['param'];

                    $payloads = $instance->$function($param);
                    setPayloads($payloads);
                } else {
                    $payloads =
                        $instance->$function();
                    setPayloads($payloads);
                }
            }
        }

        // if ($this->isActiveRoute('')) {

        //     $instance = new $controller[0]();

        //     switch ($this->server("REQUEST_METHOD")) {
        //         case 'GET':
        //             $function = 'index';
        //             break;
        //         case 'POST':
        //             $function = 'store';
        //             break;
        //         case 'PUT':
        //             $function = 'update';
        //             break;
        //         case 'DELETE':
        //             $function = 'destroy';
        //             break;
        //         default:
        //             $function = 'index';
        //             break;
        //     }

        //     $instance->$function();
        // }
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

        $static->loadRoute($route, $controller, "GET");
        return new RouteUtils($route, $controller);
    }

    /**
     * Set POST route
     */
    public static function post(string $route, array $controller)
    {
        $static = new static();

        $static->loadRoute($route, $controller, "POST");
        return new RouteUtils($route, $controller);
    }

    public static function any($route, $controller)
    {
        $static = new static();

        $static->loadRoute($route, $controller, "any");
        return new RouteUtils($route, $controller);
    }

    public static function resource(string $route, string $controller)
    {
        $static = new static();

        $static->loadResoucesRoute($route, $controller);
        return new RouteUtils($route, [$controller]);
    }
}
