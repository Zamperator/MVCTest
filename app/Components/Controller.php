<?php

namespace App\Components;

use App\Lib\Cache;
use App\Lib\Database;
use App\Lib\Json;
use App\Lib\PageSetup;
use App\Lib\Registry;
use App\Lib\Request;
use App\Lib\Utils;
use App\Models\UserModel;
use Exception;
use PDO;

// Add all available Models here

/**
 *
 */
abstract class Controller
{
    // abstract protected function init();

    protected array $config;

    protected PDO $db;
    protected PDO $dbManager;
    protected Model $model;
    protected View $view;
    protected Json $json;
    protected Request $request;
    protected string $controllerName;
    protected PageSetup $pageSetup;
    protected UserModel $user;

    protected string $language;

    public function __construct()
    {
        // Define namespace of controller
        $controllerNamespace = str_replace('\\', DIRECTORY_SEPARATOR, static::class);

        // Extract controller name from namespace
        $this->controllerName = substr($controllerNamespace, strrpos($controllerNamespace, DIRECTORY_SEPARATOR) + 1, -strlen('Controller'));

        // Page setup
        $this->pageSetup = new PageSetup();
        Registry::set('pageSetup', $this->pageSetup);

        // Set config data
        $this->config = Registry::get('config');

        // Set language
        $this->language = Registry::get('language');

        // Init database
        try {
            $this->db = Database::get($this->config['db']['read']);
            $this->dbManager = Database::get($this->config['db']['manage']);
        } catch (Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        // Init Requests
        $this->request = new Request();

        // Init user
        $this->user = new UserModel($this->request, $this->dbManager);

        // Init caching
        new Cache();

        // Init current model
        $this->initModel();

        // Init template view
        $this->view = new View($this->controllerName);

        // Init json view
        $this->json = new Json();

        $this->init();
    }

    public function init(): void {}

    /**
     * @return void
     */
    private function initModel(): void
    {
        $modelName = 'App\Models\\' . $this->controllerName . 'Model';
        if (class_exists($modelName)) {
            $this->model = new $modelName($this->request, $this->db);
        }
    }

    protected function redirect(string $url): void {
        header('Location: '. $url);
        exit;
    }

    /**
     * @param string $action
     * @param array $data
     * @return void
     */
    protected function view(string $action, array $data = []): void
    {
        $this->view->setAction($action);

        $this->view->set('_accessToken', Utils::getClientAccessToken());
        $this->view->set('_controller', $this->controllerName);
        // $this->view->set('user', $this->user);

        unset($data['user'], $data['_controller']);

        $this->view->setMultiple($data);

        $this->view->render();
    }
}
