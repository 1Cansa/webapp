<?php
namespace App\Controllers;

use App\Core\Router;

/**
 * HomeController
 * 
 * Handles the homepage of the application.
 */
class HomeController
{
    public Router $router;

    /**
     * Display the homepage with navigation links.
     */
    public function index()
    {
        require_once ROOT . '/src/Views/Includes/header.php';

        echo "<h1>Welcome to the Client/Contact Management Application</h1>";
        echo "<p><a href='/clients'>View Clients</a></p>";
        echo "<p><a href='/contacts'>View Contacts</a></p>";

        require_once ROOT . '/src/Views/Includes/footer.php';
    }
}
