<?php

namespace App\Components;

use App\Lib\Cache;
use App\Lib\Database;
use App\Lib\PageSetup;
use App\Lib\Registry;
use App\Lib\Utils;
use Exception;

// Add all available Models here

/**
 * @package App\Components
 * @version 1.0
 * @since 1.0
 */
abstract class Controller
{
    // abstract protected function init();

    protected array $config;

    protected mixed $db;
    protected Model $model;
    protected View $view;
    protected string $controllerName;
    protected PageSetup $pageSetup;

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

        // Init database
        try {
            $this->db = Database::get($this->config['db']['connection']['use']);
        } catch (Exception $e) {
        }

        // Init caching
        new Cache();

        // Init current model
        $this->initModel();

        $this->view = new View($this->controllerName);
    }

    /**
     * @return void
     */
    private function initModel(): void
    {
        $modelName = 'App\Models\\' . $this->controllerName . 'Model';
        if (class_exists($modelName)) {
            $this->model = new $modelName();
        }
    }

    /**
     * @param string $action
     * @param array $data
     * @return void
     */
    protected function view(string $action, array $data = []): void
    {
        $this->view->setAction($action);

        $this->view->set('_requestToken', Utils::getRequestToken());
        $this->view->set('_controller', $this->controllerName);
        $this->view->set('user', false);

        unset($data['user'], $data['_accessToken'], $data['_controller']);

        $this->view->setMultiple($data);

        $this->view->render();
    }
}
