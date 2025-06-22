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
        $clientsRaw = $this->clientModel->findAll();
        $clients = [];

        // Add the number of linked clients to each client
        foreach ($clientsRaw as $client) {
            $client['num_linked_contacts'] = $this->clientModel->countLinkedContacts($client['id']);
            $clients[] = $client;
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? '')
            ];

            // Validate required fields
            if (empty($data['name'])) {
                $_SESSION['error'] = "Client name is required.";
                header("Location: " . BASE_URL . "/clients/create");
                exit;
            }

            if ($this->clientModel->create($data)) {
                $_SESSION['success'] = "Client successfully created.";
                header("Location: " . BASE_URL . "/clients");
                exit;
            } else {
                $_SESSION['error'] = "Error while creating client.";
                header("Location: " . BASE_URL . "/clients/create");
                exit;
            }
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

        // Handle unlinking via GET (contact to be unlinked from client)
        if (!empty($_GET['unlink_contact_id']) && is_numeric($_GET['unlink_contact_id'])) {
            $this->clientModel->unlinkContact($id, $_GET['unlink_contact_id']);
            header("Location: " . BASE_URL . "/clients/edit/" . $id);
            exit;
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
            header("Location: " . BASE_URL . "/clients/edit/{$id}");
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

            $_SESSION['success'] = "Client successfully updated.";
            header("Location: " . BASE_URL . "/clients/edit/{$id}");
            exit;
        } else {
            $_SESSION['error'] = "Error while updating client.";
            header("Location: " . BASE_URL . "/clients/edit/{$id}");
            exit;
        }
    }


    /**
     * Delete a client.
     */
    public function delete($id)
    {
        if ($this->clientModel->delete($id)) {
            header("Location: " . BASE_URL . "/clients");
        } else {
            $_SESSION['error'] = "Error while deleting client.";
            header("Location: " . BASE_URL . "/clients");
        }
    }
}
