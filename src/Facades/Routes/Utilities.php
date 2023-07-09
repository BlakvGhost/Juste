<?php

namespace Juste\Facades\Routes;

use Closure;

trait Utilities
{
    public static function group(Closure $callback): RouteUtils
    {
        $callbackIns = $callback();

        // foreach ($callbackIns as $ins) {
        //     dd($ins);
        // }

        return new RouteUtils('', []);
    }

    public static function prefix(string $prefix): RouteUtils
    {
        return new RouteUtils('', []);
    }
}