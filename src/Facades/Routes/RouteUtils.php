<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Helpers\Common;

use \App\Kernel;

class RouteUtils extends Common
{
    use Kernel;

    private $controller = null;

    private $function = null;

    public function __construct(private string $route, array $controller, private bool $isActiveRoute = false)
    {
        $this->controller = $controller[0] ?? $this::class;;
        $this->function = $controller[1] ?? 'index';
    }

    /**
     * Set alias or name for the route
     * @param string $alias The alias of the Route
     */
    public function name($alias): RouteUtils
    {
        $routes = getBravo('routes');

        if (!isset($routes[$alias])) {
            updateBravo('routes', [$alias => $this->route]);
        }

        return $this;
    }

    public function middlewares(array $middlewares): RouteUtils
    {
        if ($this->isActiveRoute) {

            foreach ($middlewares as $alias) {

                if ($middleware = $this->middlewareAliases[$alias] ?? 0) {
                    
                    $_activeRoute = getBravo('activeRoute');
                    $_middlewares = $_activeRoute['middlewares'] ?? [];

                    $routeData = [
                        ...$_activeRoute,
                        'middlewares' => [
                            ...$_middlewares,
                            $alias => $middleware,
                        ],
                    ];

                    setBravo('activeRoute', $routeData);
                }
            }
        }
        return $this;
    }
}
