<?php

namespace App\Middleware;

class UserHandler
{

    protected bool $loggedIn = false;
    protected array $userData = [];

    public function handle($request, $next)
    {
        // DON'T DO THAT! This is just an example
        $this->loggedIn = isset($_SESSION['user']);

        return $next($request);
    }
}
