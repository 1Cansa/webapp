<?php
// app/Controllers/ClientController.php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Contact; // For linking contacts to clients

class ClientController
{
    protected $clientModel;
    protected $contactModel;

    public function __construct()
    {
        $this->clientModel = new Client();
        $this->contactModel = new Contact();
    }

    /**
     * Display the list of all clients.
     */
    public function index()
    {
        $clients = $this->clientModel->findAll();

        // For each client, retrieve the number of linked contacts
        foreach ($clients as &$client) {
            $client['num_linked_contacts'] = $this->clientModel->countLinkedContacts($client['id']);
        }

        require_once APP_ROOT . '/app/Views/Includes/header.php';
        require_once APP_ROOT . '/app/Views/Client/list.php';
        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }

    /**
     * Show the form to create a new client.
     */
    public function create()
    {
        $client = null; // No existing client for creation
        $allContacts = $this->contactModel->findAll(); // For contact linking

        require_once APP_ROOT . '/app/Views/Includes/header.php';
        require_once APP_ROOT . '/app/Views/Client/form.php';
        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }

    /**
     * Handle the submission of the client creation form.
     */
    public function store()
    {
        if (empty($_POST['name'])) {
            $_SESSION['error'] = "Client name is required.";
            header('Location: /clients/create');
            return;
        }

        if ($this->clientModel->create($_POST)) {
            header('Location: /clients');
        } else {
            $_SESSION['error'] = "Error while creating client.";
            header('Location: /clients/create');
        }
    }

    /**
     * Show the form to edit an existing client.
     */
    public function edit($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            header("HTTP/1.0 404 Not Found");
            echo "Client not found.";
            return;
        }

        $linkedContacts = $this->clientModel->getLinkedContacts($id);
        $allContacts = $this->contactModel->findAll();

        require_once APP_ROOT . '/app/Views/Includes/header.php';
        require_once APP_ROOT . '/app/Views/Client/form.php';
        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }

    /**
     * Handle the submission of the client update form.
     */
    public function update($id)
    {
        if (empty($_POST['name'])) {
            $_SESSION['error'] = "Client name is required.";
            header("Location: /clients/edit/{$id}");
            return;
        }

        if ($this->clientModel->update($id, $_POST)) {
            // Manage contact linking/unlinking
            if (isset($_POST['link_contact_id']) && is_numeric($_POST['link_contact_id'])) {
                $this->clientModel->linkContact($id, $_POST['link_contact_id']);
            }

            if (isset($_POST['unlink_contact_id']) && is_numeric($_POST['unlink_contact_id'])) {
                $this->clientModel->unlinkContact($id, $_POST['unlink_contact_id']);
            }

            header('Location: /clients');
        } else {
            $_SESSION['error'] = "Error while updating client.";
            header("Location: /clients/edit/{$id}");
        }
    }

    /**
     * Delete a client.
     */
    public function delete($id)
    {
        if ($this->clientModel->delete($id)) {
            header('Location: /clients');
        } else {
            $_SESSION['error'] = "Error while deleting client.";
            header('Location: /clients');
        }
    }
}
