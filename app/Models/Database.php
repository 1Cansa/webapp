<?php
namespace App\Models;

use PDO;
use PDOException;

/**
 * Database connection singleton class.
 */
class Database
{
    private static $instance = null;
    private $connection;

    /**
     * Private constructor to prevent direct instantiation.
     * Initializes the PDO database connection.
     */
    private function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // You can log this error instead of showing it in production
            die("Database connection error: " . $e->getMessage());
        }
    }

    /**
     * Returns the singleton instance of the Database.
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Returns the active PDO connection.
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
