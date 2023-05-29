<?php

namespace Juste\Http\Middleware;

interface MiddlewareInterface {
    public function handle(): bool;
}