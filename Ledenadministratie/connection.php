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

    /**
     * Controleert en maakt database en tabel aan indien nodig
     * @return PDO|false Database connectie object of false bij fout
     */
    public function checkAndCreateDatabase() {
        try {
            $opts = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            // Verbinden met MySQL server zonder database
            $pdo = new PDO("mysql:host={$this->host};charset={$this->charset}", $this->user, $this->pass, $opts);
            // Controleren of database bestaat
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$this->dbname]);
            if ($stmt->rowCount() == 0) {
                // Database bestaat niet, maak deze aan
                if (function_exists('logMessage')) logMessage("Database '{$this->dbname}' bestaat niet, wordt aangemaakt.");
                $stmt = $pdo->prepare("CREATE DATABASE `{$this->dbname}`");
                $stmt->execute();
                if (function_exists('logMessage')) logMessage("Database '{$this->dbname}' is succesvol aangemaakt.");
            } else {
                if (function_exists('logMessage')) logMessage("Database '{$this->dbname}' bestaat al.");
            }
            // Verbinden met de specifieke database
            $pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}", $this->user, $this->pass, $opts);
            // Controleren of users tabel bestaat
            $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                // Tabel bestaat niet, maak deze aan met createTable.sql
                $createTableSQL = file_get_contents('createTable.sql');
                if ($createTableSQL === false) {
                    throw new Exception("Kan createTable.sql niet lezen");
                }
                $stmt = $pdo->prepare($createTableSQL);
                $stmt->execute();
                // Standaard admin gebruiker aanmaken
                $admin_username = 'admin';
                $admin_password = 'password123';
                $hashed_admin_password = password_hash($admin_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
                $stmt->execute([$admin_username, $hashed_admin_password]);
                if (function_exists('logMessage')) logMessage("Tabel 'users' is aangemaakt.");
                if (function_exists('logMessage')) logMessage("Standaard admin gebruiker 'admin' aangemaakt met het standaard wachtwoord. Pas deze aan na eerste login!");
            } else {
                if (function_exists('logMessage')) logMessage("Tabel 'users' bestaat al.");
            }
            return $pdo;
        } catch(PDOException $e) {
            if (function_exists('logMessage')) logMessage("Database fout: " . $e->getMessage());
            return false;
        }
    }
}
