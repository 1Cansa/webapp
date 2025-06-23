<?php
// app/Models/Contact.php

namespace App\Models;

use PDO;

class Contact extends BaseModel
{
    protected $table = 'contacts';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new contact record.
     *
     * @param array $data Contact data (first_name, last_name, email)
     * @return bool True on success, false on failure
     */
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (first_name, last_name, email) VALUES (:first_name, :last_name, :email)");
        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email']
        ]);
    }

    /**
     * Update an existing contact record.
     *
     * @param int $id Contact ID
     * @param array $data Contact data to update
     * @return bool True on success, false on failure
     */
    public function update($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id");
        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'id' => $id
        ]);
    }

    /**
     * Delete a contact and all its client links.
     *
     * @param int $id Contact ID
     * @return bool True on success, false on failure
     */
    public function delete($id)
    {
        // First remove links in client_contacts table
        $stmt = $this->db->prepare("DELETE FROM client_contacts WHERE contact_id = :contact_id");
        $stmt->execute(['contact_id' => $id]);

        // Then delete the contact record
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get all clients linked to a contact.
     *
     * @param int $contactId Contact ID
     * @return array List of linked clients
     */
    public function getLinkedClients($contactId)
    {
        $stmt = $this->db->prepare("
            SELECT cl.id, cl.name, cl.client_code
            FROM clients cl
            JOIN client_contacts cc ON cl.id = cc.client_id
            WHERE cc.contact_id = :contact_id
            ORDER BY cl.name ASC
        ");
        $stmt->execute(['contact_id' => $contactId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count how many clients are linked to this contact.
     *
     * @param int $contactId Contact ID
     * @return int Number of linked clients
     */
    public function countLinkedClients($contactId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM client_contacts WHERE contact_id = :contact_id");
        $stmt->execute(['contact_id' => $contactId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Link a client to a contact.
     *
     * @param int $contactId Contact ID
     * @param int $clientId Client ID
     * @return bool True on success, false if already linked or failure
     */
    public function linkClient($contactId, $clientId)
    {
        // Check if link already exists to avoid duplicates
        $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM client_contacts WHERE contact_id = :contact_id AND client_id = :client_id");
        $checkStmt->execute(['contact_id' => $contactId, 'client_id' => $clientId]);
        if ($checkStmt->fetchColumn() > 0) {
            return false; // Link already exists
        }

        $stmt = $this->db->prepare("INSERT INTO client_contacts (contact_id, client_id) VALUES (:contact_id, :client_id)");
        return $stmt->execute(['contact_id' => $contactId, 'client_id' => $clientId]);
    }

    /**
     * Unlink a client from a contact.
     *
     * @param int $contactId Contact ID
     * @param int $clientId Client ID
     * @return bool True on success, false on failure
     */
    public function unlinkClient($contactId, $clientId)
    {
        $stmt = $this->db->prepare("DELETE FROM client_contacts WHERE contact_id = :contact_id AND client_id = :client_id");
        return $stmt->execute(['contact_id' => $contactId, 'client_id' => $clientId]);
    }
}
