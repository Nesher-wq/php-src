<?php
// handlers/auth_handler.php - Login en logout afhandeling

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../config/connection.php';

use config\Connection;

// Start sessie als deze nog niet is gestart
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

error_log("Auth handler: Called with method: " . $_SERVER['REQUEST_METHOD']);
error_log("Auth handler: POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    error_log("Auth handler: Login attempt - Username: " . $username);
    
    if (empty($username) || empty($password)) {
        error_log("Auth handler: Empty username or password");
        $_SESSION['login_error'] = 'Gebruikersnaam en wachtwoord zijn verplicht.';
        header('Location: /Ledenadministratie/index.php');
        exit;
    }

    try {
        error_log("Auth handler: Creating database connection");
        $connection = new Connection();
        $pdo = $connection->getConnection();
        error_log("Auth handler: Database connection successful");
        
        $authController = new AuthController($pdo);
        error_log("Auth handler: AuthController created");
        
        $loginResult = $authController->login($username, $password);
        error_log("Auth handler: Login result: " . ($loginResult ? 'SUCCESS' : 'FAILED'));
        error_log("Auth handler: Session after login: " . print_r($_SESSION, true));
        
        if ($loginResult) {
            error_log("Auth handler: Login successful, redirecting");
            $_SESSION['login_success'] = 'Login succesvol!';
            header('Location: /Ledenadministratie/index.php');
            exit;
        } else {
            error_log("Auth handler: Login failed");
            $_SESSION['login_error'] = 'Ongeldige gebruikersnaam of wachtwoord.';
            header('Location: /Ledenadministratie/index.php');
            exit;
        }
    } catch (Exception $e) {
        error_log("Auth handler: Exception: " . $e->getMessage());
        $_SESSION['login_error'] = 'Er is een fout opgetreden bij het inloggen: ' . $e->getMessage();
        header('Location: /Ledenadministratie/index.php');
        exit;
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: /Ledenadministratie/index.php');
    exit;
}
?>