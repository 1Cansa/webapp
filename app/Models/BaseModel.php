<?php
namespace App\Models;

use PDO;

/**
 * Class BaseModel
 *
 * A generic base model providing basic database operations.
 * All specific models (e.g. Client, Contact) should extend this class.
 */
abstract class BaseModel
{
    /**
     * @var string The name of the database table.
     */
    protected $table;

    /**
     * @var PDO The PDO database connection instance.
     */
    protected $db;

    /**
     * BaseModel constructor.
     * Initializes the database connection.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retrieve all records from the table.
     *
     * @return array All rows as associative arrays.
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a specific record by its ID.
     *
     * @param int $id The ID of the record.
     * @return array|false The record if found, false otherwise.
     */
    public function find(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
