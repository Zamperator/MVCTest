<?php

namespace App\Lib;

use App\Models\UserModel;
use PDO;
use Exception;
use RuntimeException;

class Middleware
{
    protected Request $request;
    protected Response $response;
    protected PDO $dbManager;

    function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();

        $config = Registry::get('config')['db'];

        // Init database
        try {
            $this->dbManager = Database::get($config['manage']);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        Registry::set('user', new UserModel($this->request, $this->dbManager));
    }
}