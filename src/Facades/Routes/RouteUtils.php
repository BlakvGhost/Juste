<?php

namespace Juste\Facades\Routes;

use Juste\Facades\Helpers\Common;

class RouteUtils extends Common
{
    private $controller = null;

    private $function = null;

    public function __construct(private string $route, array $controller, private bool $isActiveRoute = false)
    {
        $this->controller = $controller[0];
        $this->function = $controller[1] ?? 'index';
    }

    /**
     * Set alias or name for the route
     * @param string $alias The alias of the Route
     */
    public function name($alias): RouteUtils
    {
        //echo debugWithInt();
        $routes = $this->getDataOnSession('routes');

        if (!isset($routes[$alias])) {
            $routes = array_merge($routes, [$alias => $this->route]);

            $this->setDataOnSession('routes', $routes);
        }

        return $this;
    }

    public function middleware(array $middlewares): RouteUtils
    {   
        if($this->isActiveRoute) {
            
        }    
        return $this;
    }
}
