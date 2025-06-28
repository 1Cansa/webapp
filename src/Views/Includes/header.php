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
        <div id="customConfirmModal" class="modal">
        <div class="modal-content">
            <p id="confirmMessage">Are you sure you want to proceed?</p>
            <div class="modal-buttons">
            <button id="confirmYes" class="btn btn-danger">Yes</button>
            <button id="confirmNo" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
        </div>

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