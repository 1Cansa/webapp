<?php
// app/Models/Client.php

namespace App\Models;

use PDO;

/**
 * Class Client
 *
 * Handles all database operations related to the 'clients' table,
 * including CRUD operations and contact associations.
 */
class Client extends BaseModel
{
    protected $table = 'clients';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new client with a generated unique client code.
     *
     * @param array $data Client data including 'name'
     * @return bool True on success, false otherwise
     */
    public function create(array $data)
    {
        $name = $data['name'];
        $baseCode = strtoupper(substr($name, 0, 3)); // First 3 characters of the name
        if (strlen($baseCode) < 3) {
            $baseCode = str_pad($baseCode, 3, 'X', STR_PAD_RIGHT); // Pad with X if less than 3 chars
        }

        $uniqueNum = 1;
        $clientCode = $baseCode . str_pad($uniqueNum, 3, '0', STR_PAD_LEFT);

        // Ensure the generated client code is unique
        while ($this->findByCode($clientCode)) {
            $uniqueNum++;
            $clientCode = $baseCode . str_pad($uniqueNum, 3, '0', STR_PAD_LEFT);
        }

        $data['client_code'] = $clientCode;

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, client_code) VALUES (:name, :client_code)");
        return $stmt->execute([
            'name' => $data['name'],
            'client_code' => $data['client_code']
        ]);
    }

    /**
     * Update an existing client's name.
     *
     * @param int $id Client ID
     * @param array $data Client data (only 'name' is used)
     * @return bool
     */
    public function update($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET name = :name WHERE id = :id");
        return $stmt->execute([
            'name' => $data['name'],
            'id' => $id
        ]);
    }

    /**
     * Delete a client and all linked associations with contacts.
     *
     * @param int $id Client ID
     * @return bool
     */
    public function delete($id)
    {
        // First, remove all associations with contacts
        $stmt = $this->db->prepare("DELETE FROM client_contacts WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $id]);

        // Then, delete the client
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Find a client by its unique client code.
     *
     * @param string $code
     * @return array|false
     */
    public function findByCode($code)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE client_code = :code");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all contacts linked to a specific client.
     *
     * @param int $clientId
     * @return array
     */
    public function getLinkedContacts($clientId)
    {
        $stmt = $this->db->prepare("
            SELECT c.id, c.first_name, c.last_name, c.email
            FROM contacts c
            JOIN client_contacts cc ON c.id = cc.contact_id
            WHERE cc.client_id = :client_id
            ORDER BY c.last_name ASC, c.first_name ASC
        ");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count the number of contacts linked to a client.
     *
     * @param int $clientId
     * @return int
     */
    public function countLinkedContacts($clientId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM client_contacts WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchColumn();
    }

    /**
     * Link a contact to a client if not already linked.
     *
     * @param int $clientId
     * @param int $contactId
     * @return bool
     */
    public function linkContact($clientId, $contactId)
    {
        // Prevent duplicate links
        $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM client_contacts WHERE client_id = :client_id AND contact_id = :contact_id");
        $checkStmt->execute(['client_id' => $clientId, 'contact_id' => $contactId]);
        if ($checkStmt->fetchColumn() > 0) {
            return false; // Already linked
        }

        $stmt = $this->db->prepare("INSERT INTO client_contacts (client_id, contact_id) VALUES (:client_id, :contact_id)");
        return $stmt->execute(['client_id' => $clientId, 'contact_id' => $contactId]);
    }

    /**
     * Unlink a contact from a client.
     *
     * @param int $clientId
     * @param int $contactId
     * @return bool
     */
    public function unlinkContact($clientId, $contactId)
    {
        $stmt = $this->db->prepare("DELETE FROM client_contacts WHERE client_id = :client_id AND contact_id = :contact_id");
        return $stmt->execute(['client_id' => $clientId, 'contact_id' => $contactId]);
    }
}
