<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', '/webapp/public');


require_once APP_ROOT . '/config/database.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_ROOT . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;

session_start();

$router = new Router();

$router->get('/', 'HomeController@index');

$router->get('/clients', 'ClientController@index');
$router->get('/clients/create', 'ClientController@create');
$router->post('/clients/store', 'ClientController@store');
$router->get('/clients/edit/{id}', 'ClientController@edit');
$router->post('/clients/update/{id}', 'ClientController@update');
$router->get('/clients/delete/{id}', 'ClientController@delete');

$router->get('/contacts', 'ContactController@index');
$router->get('/contacts/create', 'ContactController@create');
$router->post('/contacts/store', 'ContactController@store');
$router->get('/contacts/edit/{id}', 'ContactController@edit');
$router->post('/contacts/update/{id}', 'ContactController@update');
$router->get('/contacts/delete/{id}', 'ContactController@delete');

$router->dispatch();
