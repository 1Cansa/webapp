<?php
namespace App\Controllers;

use App\Models\Contact;
use App\Models\Client;

class ContactController
{
    protected $contactModel;
    protected $clientModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
        $this->clientModel = new Client();
    }

    /**
     * Display the list of all contacts.
     */
    public function index()
    {
        $contactsRaw = $this->contactModel->findAll();
        $contacts = [];

        // Add the number of linked clients to each contact
        foreach ($contactsRaw as $contact) {
            $contact['num_linked_clients'] = $this->contactModel->countLinkedClients($contact['id']);
            $contacts[] = $contact;
        }

        require_once APP_ROOT . '/app/Views/Includes/header.php';
        require APP_ROOT . '/app/Views/Contact/list.php';
        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }

    /**
     * Display the form to create a new contact.
     */
    public function create()
    {
        $contact = null;
        $linkedClients = [];
        $allClients = $this->clientModel->findAll();

        require_once APP_ROOT . '/app/Views/Includes/header.php';
        require APP_ROOT . '/app/Views/Contact/form.php';
        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }

    /**
     * Handle contact creation form submission.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name'  => trim($_POST['last_name'] ?? ''),
                'email'      => trim($_POST['email'] ?? '')
            ];

            // Validate required fields
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: " . BASE_URL . "/contacts/create");
                exit;
            }

            if ($this->contactModel->create($data)) {
                $_SESSION['success'] = "Contact successfully created.";
                header("Location: " . BASE_URL . "/contacts");
                exit;
            } else {
                $_SESSION['error'] = "Error while creating contact.";
                header("Location: " . BASE_URL . "/contacts/create");
                exit;
            }
        }
    }

    /**
     * Display the form to edit an existing contact.
     * Also handles client linking/unlinking.
     */
    public function edit($id)
    {
        $contact = $this->contactModel->find($id);
        if (!$contact) {
            $_SESSION['error'] = "Contact not found.";
            header("Location: " . BASE_URL . "/contacts");
            exit;
        }

        $linkedClients = $this->contactModel->getLinkedClients($id);
        $allClients = $this->clientModel->findAll();

        // Handle linking via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['link_client_id'])) {
                $this->contactModel->linkClient($id, $_POST['link_client_id']);
                header("Location: " . BASE_URL . "/contacts/edit/" . $id);
                exit;
            }
        }

        // Handle unlinking via GET
        if (!empty($_GET['unlink_client_id'])) {
            $this->contactModel->unlinkClient($id, $_GET['unlink_client_id']);
            header("Location: " . BASE_URL . "/contacts/edit/" . $id);
            exit;
        }

        require_once APP_ROOT . '/app/Views/Includes/header.php';
        require APP_ROOT . '/app/Views/Contact/form.php';
        require_once APP_ROOT . '/app/Views/Includes/footer.php';
    }

    /**
     * Handle update form submission for a contact.
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name'  => trim($_POST['last_name'] ?? ''),
                'email'      => trim($_POST['email'] ?? '')
            ];

            // Validate required fields
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: " . BASE_URL . "/contacts/edit/" . $id);
                exit;
            }

            if ($this->contactModel->update($id, $data)) {
                $_SESSION['success'] = "Contact successfully updated.";
                header("Location: " . BASE_URL . "/contacts");
                exit;
            } else {
                $_SESSION['error'] = "Error while updating contact.";
                header("Location: " . BASE_URL . "/contacts/edit/" . $id);
                exit;
            }
        }
    }

    /**
     * Delete a contact.
     */
    public function delete($id)
    {
        if ($this->contactModel->delete($id)) {
            $_SESSION['success'] = "Contact successfully deleted.";
        } else {
            $_SESSION['error'] = "Error while deleting contact.";
        }

        header("Location: " . BASE_URL . "/contacts");
        exit;
    }
}
