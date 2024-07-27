<?php

namespace App\Components;

use App\Lib\Request;
use App\Lib\Json;
use App\Lib\Registry;
use App\Lib\L8N;
use PDO;

/**
 *
 */
abstract class Model
{
    protected PDO $db;
    protected Request $request;
    protected Json $json;
    protected L8N $l8n;

    protected array $config;

    function __construct(Request $request, PDO $db)
    {
        $this->request = $request;
        $this->db = $db;
        $this->json = new Json();
        $this->l8n = new L8N();
        $this->config = Registry::get('config');

        $this->init();
    }

    public function init(): void {}
}
