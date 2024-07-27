<?php

namespace App\Lib;

use Exception;
use PDO;

/**
 * @uses Cache
 * @package App\Lib
 * @version 1.0
 * @since 1.0
 */
final class Database
{
    /**
     * @param array $connectionData
     * @return mixed
     * @throws Exception
     */
    public static function get(array $connectionData): PDO
    {
        $databaseClass = 'App\Lib\Database\\' . ucfirst($connectionData['type']);

        if (!class_exists($databaseClass)) {
            throw new Exception('Class ' . $databaseClass . ' not found');
        }

        $database = new $databaseClass();
        $database->connect($connectionData);

        return $database->get();
    }
}
