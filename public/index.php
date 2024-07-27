<?php
require_once __DIR__ . '/../vendor/autoload.php';
/** @const array PROJECT_CONFIG */
require_once __DIR__ . '/../config/config.php';

use App\Lib\Router;
use App\Lib\Registry;
use App\Lib\ErrorHandler;
use App\Middleware\ErrorHandlerMiddleware;
use App\Middleware\UserHandler;

// Register settings for the project
Registry::set(index: 'config', data: PROJECT_CONFIG);

// Set global time constant to avoid multiple calls to time()
define('TIME_NOW', time());

// Initialize router
$router = new Router();

// Register middleware
$router->middleware(new ErrorHandlerMiddleware());
$router->middleware(new UserHandler());

// Routes and Logic
// TODO: Add a dynamic route loader, to load the correct route for each subdomain etc.
require_once __DIR__ . '/../app/Routes/default.php';

// Additional code e.g. for sessions and stuff

// Dispatch
$router->dispatch();
