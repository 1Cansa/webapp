<?php

require '../config/constant.php';
require ROOT . '/config/database.php';
require ROOT . '/vendor/autoload.php';

use App\Core\Router;
use App\Core\Route;
use App\Controllers\HomeController;
use App\Controllers\ClientController;
use App\Controllers\ContactController;

session_start();

$router = new Router();

$router->get('/', [HomeController::class, 'index'], 'index');

$router->get('/clients', [ClientController::class, 'index'], 'client.index');
$router->get('/clients/create', [ClientController::class, 'create'], 'client.create');
$router->post('/clients/store', [ClientController::class, 'store'], 'client.store');
$router->get('/clients/edit/:id', [ClientController::class, 'edit'], 'client.edit');
$router->post('/clients/update/:id', [ClientController::class, 'update'], 'client.update');
$router->get('/clients/delete/:id', [ClientController::class, 'delete'], 'client.delete');

$router->get('/contacts', [ContactController::class, 'index'], 'contact.index');
$router->get('/contacts/create', [ContactController::class, 'create'], 'contact.create');
$router->post('/contacts/store', [ContactController::class, 'store'], 'contact.store');
$router->get('/contacts/edit/:id', [ContactController::class, 'edit'], 'contact.edit');
$router->post('/contacts/update/:id', [ContactController::class, 'update'], 'contact.update');
$router->get('/contacts/delete/:id', [ContactController::class, 'delete'], 'contact.delete');

$route = $router->run();
if ($route !== null) {
    [$controller, $method] = $route->getController();

    $instance = new $controller();
    $instance->router = $router;

    call_user_func_array([$instance, $method], $route->getMatches());
} else {
    header("HTTP/1.0 404 Not Found");
    exit;
}
