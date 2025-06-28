<?php
namespace App\Controllers;

use App\Models\Contact;
use App\Models\Client;
use App\Core\Router;

class ContactController
{
    public Router $router;
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

        require_once ROOT . '/src/Views/Includes/header.php';
        require ROOT . '/src/Views/Contact/list.php';
        require_once ROOT . '/src/Views/Includes/footer.php';
    }

    /**
     * Display the form to create a new contact.
     */
    public function create()
    {
        $contact = null;
        $linkedClients = [];
        $allClients = $this->clientModel->findAll();

        require_once ROOT . '/src/Views/Includes/header.php';
        require ROOT . '/src/Views/Contact/form.php';
        require_once ROOT . '/src/Views/Includes/footer.php';
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
                $this->router->redirect("contact.create");
                exit;
            }

            if ($this->contactModel->create($data)) {
                $_SESSION['success'] = "Contact successfully created.";
                $this->router->redirect("contact.index");
                exit;
            } else {
                $_SESSION['error'] = "Error while creating contact.";
                $this->router->redirect("contact.create");
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
            $this->router->redirect("contact.index");
            exit;
        }

        $linkedClients = $this->contactModel->getLinkedClients($id);
        $allClients = $this->clientModel->findAll();

        // Handle linking via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['link_client_id'])) {
                $this->contactModel->linkClient($id, $_POST['link_client_id']);
                $this->router->redirect("contact.index", ['id' => $id]);
                exit;
            }
        }

        // Handle unlinking via GET
        if (!empty($_GET['unlink_client_id'])) {
            $this->contactModel->unlinkClient($id, $_GET['unlink_client_id']);
            $this->router->redirect("contact.index", ['id' => $id]);
            exit;
        }
        

        require_once ROOT . '/src/Views/Includes/header.php';
        require ROOT . '/src/Views/Contact/form.php';
        require_once ROOT . '/src/Views/Includes/footer.php';
    }

    /**
     * Handle update form submission for a contact.
     */
    public function update($id)
{
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email'])) {
        $_SESSION['error'] = "All fields are required.";
        $this->router->redirect("contact.edit", ['id' => $id]);
        return;
    }

    $data = [
        'first_name' => trim($_POST['first_name']),
        'last_name'  => trim($_POST['last_name']),
        'email'      => trim($_POST['email'])
    ];

    if ($this->contactModel->update($id, $data)) {
        // Unlink if requested
        if (isset($_POST['unlink_client_id']) && is_numeric($_POST['unlink_client_id'])) {
            $this->contactModel->unlinkClient($id, $_POST['unlink_client_id']);
        }
        
        $manualCode = trim($_POST['client_code'] ?? '');
        $dropdownId = $_POST['link_client_id'] ?? '';

        $clientIdToLink = null;

        if (!empty($manualCode)) {
            $client = $this->clientModel->findByCode($manualCode);
            if ($client) {
                $clientIdToLink = $client['id'];
            } else {
                $_SESSION['error'] = "No client found with this client code.";
                $this->router->redirect("contact.edit", ['id' => $id]);
                return;
            }
        } elseif (!empty($dropdownId) && is_numeric($dropdownId)) {
            $clientIdToLink = $dropdownId;
        }

        if ($clientIdToLink) {
            $linkedClientIds = array_column($this->contactModel->getLinkedClients($id), 'id');
            if (in_array($clientIdToLink, $linkedClientIds)) {
                $_SESSION['error'] = "This client is already linked to the contact.";
                $this->router->redirect("contact.edit", ['id' => $id]);
                return;
            }

            $this->contactModel->linkClient($id, $clientIdToLink);
        }

        $_SESSION['success'] = "Contact successfully updated.";
        $this->router->redirect("contact.index", ['id' => $id]);
    } else {
        $_SESSION['error'] = "Error while updating contact.";
        $this->router->redirect("contact.index", ['id' => $id]);
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

        $this->router->redirect("contact.index");
        exit;
    }
}
