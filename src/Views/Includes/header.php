<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Clients/Contacts Management</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/clients">Clients</a></li>
                <li><a href="/contacts">Contacts</a></li>
            </ul>
        </nav>
    </header>
    <main>
<?php
// Start session if not already started to handle flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Display error message if set, then clear it
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Display success message if set, then clear it
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
?>