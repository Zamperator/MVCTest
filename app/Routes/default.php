<?php
/**
 * @var Router $router
 */

use App\Lib\Router;
use App\Controllers\IndexController;
use App\Controllers\ProfileController;

$router->get('/', [IndexController::class, 'index']);
$router->get('/about', [IndexController::class, 'about']);

$router->get('/profile/{id}-{username}', [ProfileController::class, 'index']);
$router->get('/profile/{id}-{username}/settings', [ProfileController::class, 'settings']);

// ...
