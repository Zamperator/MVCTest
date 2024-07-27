<?php

namespace App\Lib\Database;

use PDO;
use PDOException;

abstract class Factory
{
    protected PDO $connection;

    /**
     * @param array $config
     * @return void
     */
    public function connect(array $config): void
    {
        if (empty($config)) {
            die('Unknown connection');
        }

        $dsn = $config['dsn'];
        $databaseName = $config['database'];
        if($databaseName) {
            $dsn = preg_replace('/;?dbname=[^;]+/', '', $dsn);
            $dsn .= ';dbname=' . $databaseName;
        }

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password']
            );
        } catch (PDOException $e) {
            exit('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function get() : PDO {
        return $this->connection;
    }
}
