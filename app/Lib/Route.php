<?php

namespace App\Lib;

final class Route
{
    public array $middleware = [];
    public array $callback;

    public function __construct(array $callback)
    {
        $this->callback = $callback;
    }

    public function middleware($middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }
}