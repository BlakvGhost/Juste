<?php

namespace Juste\Facades\Routes;

use Closure;

trait Utilities
{
    static function group(Closure $callback): RouteUtils
    {
        $callback(new static());
        //dd($callback);
        return new RouteUtils('', []);
    }

    public static function prefix(string $prefix): RouteUtils
    {
        return new RouteUtils('', []);
    }
}