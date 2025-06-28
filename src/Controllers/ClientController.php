<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Contact; // For linking contacts to clients
use App\Core\Router;

class ClientController
{
    public Router $router;
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

        require_once ROOT . '/src/Views/Includes/header.php';
        require_once ROOT . '/src/Views/Client/list.php';
        require_once ROOT . '/src/Views/Includes/footer.php';
    }

    /**
     * Show the form to create a new client.
     */
    public function create()
    {
        $client = null; // No existing client for creation
        $allContacts = $this->contactModel->findAll(); // For contact linking

        require_once ROOT . '/src/Views/Includes/header.php';
        require_once ROOT . '/src/Views/Client/form.php';
        require_once ROOT . '/src/Views/Includes/footer.php';
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
                $this->router->redirect("client.create");
            }

            if ($this->clientModel->create($data)) {
                $_SESSION['success'] = "Client successfully created.";
                $this->router->redirect("client.index");
            } else {
                $_SESSION['error'] = "Error while creating client.";
                $this->router->redirect("client.create");
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
            $this->router->redirect("client.edit", ['id' => $id]);
            exit;
        }

        $linkedContacts = $this->clientModel->getLinkedContacts($id);
        $allContacts = $this->contactModel->findAll();

        require_once ROOT . '/src/Views/Includes/header.php';
        require_once ROOT . '/src/Views/Client/form.php';
        require_once ROOT . '/src/Views/Includes/footer.php';
    }

    /**
     * Handle the submission of the client update form.
     */
    public function update($id)
    {
        if (empty($_POST['name'])) {
            $_SESSION['error'] = "Client name is required.";
            $this->router->redirect("client.edit", ['id' => $id]);
            return;
        }

        $email = trim($_POST['contact_id'] ?? '');
        $dropdownId = $_POST['link_contact_id'] ?? '';

        if (!empty($email)) {
            $contact = $this->contactModel->findByEmail($email);
            if ($contact) {
                $alreadyLinked = false;
                foreach ($this->clientModel->getLinkedContacts($id) as $linked) {
                    if ($linked['id'] == $contact['id']) {
                        $alreadyLinked = true;
                        break;
                    }
                }

                if ($alreadyLinked) {
                    $_SESSION['error'] = "This contact is already linked to the client.";
                    $this->router->redirect("client.edit", ['id' => $id]);
                    return;
                }

                $this->clientModel->linkContact($id, $contact['id']);
            } else {
                $_SESSION['error'] = "No contact found with this email.";
                $this->router->redirect("client.edit", ['id' => $id]);
                return;
            }
        }

        if ($this->clientModel->update($id, $_POST)) {
    
            if (is_numeric($dropdownId)) {
                $this->clientModel->linkContact($id, $dropdownId);
            }

            if (!empty($email)) {
                $contact = $this->contactModel->findByEmail($email);
                if ($contact) {
                    $this->clientModel->linkContact($id, $contact['id']);
                } else {
                    $_SESSION['error'] = "No contact found with this email.";
                    $this->router->redirect("client.edit", ['id' => $id]);
                    return;
                }
            }

            // Unlinking remains optional (handled in edit with GET)
            $_SESSION['success'] = "Client successfully updated.";
            $this->router->redirect("client.index", ['id' => $id]);
        } else {
            $_SESSION['error'] = "Error while updating client.";
            $this->router->redirect("client.index", ['id' => $id]);
        }
    }

    /**
     * Delete a client.
     */
    public function delete($id)
    {
        if ($this->clientModel->delete($id)) {
            $_SESSION['success'] = "Client successfully deleted.";
        } else {
            $_SESSION['error'] = "Error while deleting client.";
        }

        $this->router->redirect("client.index");
        exit;
    }
}
