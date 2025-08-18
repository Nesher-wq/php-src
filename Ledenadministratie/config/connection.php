<?php

namespace config;

use PDO;
use PDOException;

class Connection {
    private $host = 'localhost';
    private $dbname = 'ledenadministratie';
    private $username = 'root';
    private $password = 'mysql';  // Probeer eerst 'mysql'
    private $pdo;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            error_log("Attempting database connection with DSN: " . $dsn);
            
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            error_log("Database connection successful");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            error_log("DSN used: " . $dsn);
            error_log("Username: " . $this->username);
            error_log("Error Code: " . $e->getCode());
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>