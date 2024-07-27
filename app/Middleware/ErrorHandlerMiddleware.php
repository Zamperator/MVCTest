<?php

namespace App\Middleware;

use App\Lib\ErrorHandler;

class ErrorHandlerMiddleware
{
    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function handle($request, $response, $next): mixed
    {
        // Register error handling
        set_error_handler([ErrorHandler::class, 'error']);
        set_exception_handler([ErrorHandler::class, 'exception']);
        register_shutdown_function([ErrorHandler::class, 'shutdown']);

        return $next($request, $response);
    }
}
