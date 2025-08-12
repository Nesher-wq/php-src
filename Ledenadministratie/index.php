<?php
// index.php - Front controller voor Ledenadministratie (MVC)
session_start();

// Initialize dependencies
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AuthController.php';

use config\Connection;

$conn = new Connection();
$pdo = $conn->getConnection();
$userController = new UserController($pdo);
$authController = new AuthController($pdo);

// Load soort leden for dropdown usage
require_once __DIR__ . '/models/Soortlid.php';
$soortlidModel = new \models\Soortlid();
$soort_leden = $soortlidModel->getAllSoortleden();

// Allowed roles (voor views/controllers)
$allowed_roles = ['treasurer', 'secretary'];

// Initialize message variables
$message = null;
$message_type = '';

// Handle authentication (login/logout)
include __DIR__ . '/handlers/auth_handler.php';

// Handle password changes (all users)
include __DIR__ . '/handlers/password_handler.php';

// Handle role-specific actions
include __DIR__ . '/handlers/family_handler.php';  // Secretary family management
include __DIR__ . '/handlers/user_handler.php';    // Admin user management

// Render dashboard based on role
if ($_SESSION['role'] === 'admin') {
    include __DIR__ . '/views/dashboard_admin.php';
} elseif ($_SESSION['role'] === 'treasurer') {
    include __DIR__ . '/views/dashboard_treasurer.php';
} elseif ($_SESSION['role'] === 'secretary') {
    include __DIR__ . '/views/dashboard_secretary.php';
} else {
    http_response_code(403);
    echo "Toegang geweigerd.";
    exit;
}