<?php

namespace App\Lib;

use ErrorException;

final class ErrorHandler
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
        $json = new Json();
        // Output the error message
        if (!defined('DEBUG')) {
            $json->error(
                'Error',
                $exception->getMessage(),
                '',
                $exception->getCode(),
                $_SERVER['REQUEST_URI']
            );
        } else {
            $json->error(
                'Error',
                $exception->getMessage(),
                [
                    'File' => $exception->getFile(),
                    'Line', $exception->getLine(),
                    'stack' => $exception->getTrace(),
                ],
                $exception->getCode(),
                $_SERVER['REQUEST_URI'],
            );
        }
    }

    /**
     * @return void
     */
    public static function shutdown(): void
    {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::exception(
                new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
            );
        }
    }
}