<?php

namespace App\Lib;

use Exception;

/**
 * @uses Cache
 * @package App\Lib
 * @version 1.0
 * @since 1.0
 */
class Database
{
    /**
     * @param $type
     * @return mixed
     * @throws Exception
     */
    public static function get($type): mixed
    {
        $databaseClass = 'App\Lib\Database\\' . ucfirst($type);

        if (!class_exists($databaseClass)) {
            throw new Exception('Class ' . $databaseClass . ' not found');
        }

        $database = new $databaseClass();
        $database->connect($type);

        return $database;
    }
}
