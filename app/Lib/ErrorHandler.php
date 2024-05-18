<?php

namespace App\Lib;

use ErrorException;

class ErrorHandler
{
    /**
     * @throws ErrorException
     */
    public static function error($severity, $message, $file, $line)
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * @param $exception
     * @return void
     */
    public static function exception($exception): void
    {
        // Set the HTTP response code
        // http_response_code(500);

        // Output the error message
        echo "Error: " . $exception->getMessage() . PHP_EOL;
        echo $exception->getTraceAsString();

        // More...
    }

    /**
     * @return void
     */
    public static function shutdown(): void
    {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::exception(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }
    }
}