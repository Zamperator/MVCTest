<?php

namespace App\Middleware;

use Exception;

class ErrorHandlerMiddleware
{
    /**
     * @param $request
     * @param $next
     * @return mixed|void
     */
    public function handle($request, $next)
    {
        try {
            return $next($request);
        } catch (Exception $exception) {
            echo "Middleware Error: " . $exception->getMessage();
            exit;
        }
    }
}
