<?php
// Database configuratie
// Pas deze waarden aan indien nodig voor jouw Ledenadministratie database

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');
define('DB_NAME', 'ledenadministratie');
define('DB_CHARSET', 'utf8mb4');

class Connection {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $charset = DB_CHARSET;
    
    public function getConnection() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $opts = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, $this->user, $this->pass, $opts);
            return $pdo;
        } catch (PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
    }
}
