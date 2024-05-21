<?php
require_once __DIR__ . '/../vendor/autoload.php';
/** @const array PROJECT_CONFIG */
require_once __DIR__ . '/../config/config.php';

use App\Lib\Router;
use App\Lib\Registry;
use App\Lib\ErrorHandler;
use App\Middleware\ErrorHandlerMiddleware;
use App\Middleware\UserHandler;

// Register error handling
set_error_handler([ErrorHandler::class, 'error']);
set_exception_handler([ErrorHandler::class, 'exception']);
register_shutdown_function([ErrorHandler::class, 'shutdown']);

// Register settings for the project
Registry::set(index: 'config', data: PROJECT_CONFIG);

// Initialize router
$router = new Router();

// Set global time constant to avoid multiple calls to time()
define('TIME_NOW', time());

// Register middleware
$router->middleware(new ErrorHandlerMiddleware());
$router->middleware(new UserHandler());

// Routes and Logic
// TODO: Add a dynamic route loader, to load the correct route for each subdomain etc.
if ($_SERVER['HTTP_HOST'] !== 'test.local') {
    require_once __DIR__ . '/../app/Routes/default.php';
} else {
    require_once __DIR__ . '/../app/Routes/project.php';
}

// Additional code e.g. for sessions and stuff

// Dispatch
$router->dispatch();
