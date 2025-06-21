<?php
namespace App\Controllers;

/**
 * HomeController
 * 
 * Handles the homepage of the application.
 */
class HomeController
{
    /**
     * Display the homepage with navigation links.
     */
    public function index()
    {
        require_once APP_ROOT . '/app/Views/Includes/header.php';

        echo "<h1>Welcome to the Client/Contact Management Application</h1>";
        echo "<p><a href='" . BASE_URL . "/clients'>View Clients</a></p>";
        echo "<p><a href='" . BASE_URL . "/contacts'>View Contacts</a></p>";

        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }
}
