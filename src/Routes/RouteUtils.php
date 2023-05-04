<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Helpers\Common;

class RouteUtils extends Common
{
    private $route = null;

    private $controller = null;

    private $function = null;

    public function __construct(string $route, array $controller)
    {
        $this->route = $route;
        $this->controller = $controller[0];
        $this->function = $controller[1] ?? 'index';
    }


    /**
     * Set alias or name for the route
     * @param string $alias The alias of the Route
     */
    public function name($alias)
    {

        $routes = $this->getDataOnSession('routes');

        if (!isset($routes[$alias])) {
            $routes = array_merge($routes, [$alias => $this->route]);

            $this->setDataOnSession('routes', $routes);
        }

        return $this;
    }
}
