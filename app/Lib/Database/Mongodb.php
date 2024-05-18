<?php

namespace App\Lib\Database;

use App\Lib\Registry;
use MongoDB\Client;
use MongoDB\Database as MongoDBDatabase;

class Mongodb
{
    protected MongoDBDatabase $connection;

    public function connect(): void
    {
        $config = Registry::get('config')['db']['connection']['mongodb'];

        $client = new Client($config['dsn'], $config['options']);
        $this->connection = $client->selectDatabase($config['collection']);
    }
}
