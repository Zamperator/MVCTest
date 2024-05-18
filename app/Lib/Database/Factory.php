<?php

namespace App\Lib\Database;

use App\Lib\Registry;
use PDO;
use PDOException;

abstract class Factory
{
    protected PDO $connection;

    /**
     * @param string $connectionName
     * @return void
     */
    public function connect(string $connectionName = 'mysql'): void
    {
        $config = Registry::get('config');

        if (!isset($config['db']['connection'])) {
            return;
        }

        if (!isset($config['db']['connection'][$connectionName])) {
            die('Unknown connection');
        }

        $dsn = $config['db']['connection'][$connectionName]['dsn'];
        $databaseName = $config['db']['connection'][$connectionName]['database'];
        if($databaseName) {
            $dsn = preg_replace('/;?dbname=[^;]+/', '', $dsn);
            $dsn .= ';dbname=' . $databaseName;
        }

        try {
            $this->connection = new PDO(
                $dsn,
                $config['db']['connection'][$connectionName]['username'],
                $config['db']['connection'][$connectionName]['password']
            );
        } catch (PDOException $e) {
            exit('Connection failed: ' . $e->getMessage());
        }
    }
}
