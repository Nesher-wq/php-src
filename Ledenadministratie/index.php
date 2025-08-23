<?php
// index.php - Main page for Ledenadministratie system
define('INCLUDED_FROM_INDEX', true);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include all required files
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/FamilieController.php';
require_once __DIR__ . '/controllers/FamilielidController.php';
require_once __DIR__ . '/models/Soortlid.php';

// Create database connection and controllers
use config\Connection;
$database_connection = new Connection();
$database_pdo = $database_connection->getConnection();
$user_controller = new UserController($database_pdo);
$auth_controller = new AuthController($database_pdo);
$familie_controller = new FamilieController($database_pdo);

// Load member types and set compatibility variables for views
$soortlid_model = new \models\Soortlid();
$soort_leden = $soortlid_model->getAllSoortleden();
$pdo = $database_pdo;
$userController = $user_controller;
$authController = $auth_controller;
$familieController = $familie_controller;

// Check if user is logged in and get role
$user_is_logged_in = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true);
$current_user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// If user is not logged in, show login page
if ($user_is_logged_in === false) {
    // Check for login error message
    $error_message = '';
    if (isset($_SESSION['login_error'])) {
        $error_message = $_SESSION['login_error'];
        unset($_SESSION['login_error']); // Clear the error after displaying
    }
    
    require_once __DIR__ . '/views/login.php';
    exit;
}

// Handle POST requests for logged in users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePostRequest($current_user_role, $familie_controller);
}

// Function to handle all POST requests
function handlePostRequest($user_role, $familie_controller) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $handler = isset($_POST['handler']) ? $_POST['handler'] : '';
    
    // Handle contributie (treasurer only)
    if ($handler === 'contributie' && $user_role === 'treasurer') {
        include __DIR__ . '/controllers/ContributieHandler.php';
        return;
    }
    
    // Handle password changes (all users)  
    if ($action === 'change_password') {
        include __DIR__ . '/controllers/PasswordHandler.php';
        return;
    }
    
    // Check user permissions
    $can_manage = ($user_role === 'secretary' || $user_role === 'admin');
    
    // Define action arrays
    $family_actions = array('add_family', 'edit_family', 'delete_family');
    $member_actions = array('add_familielid', 'edit_familielid', 'delete_familielid');
    $user_actions = array('add_user', 'edit_user', 'delete_user');
    
    // Handle family actions
    if (in_array($action, $family_actions) && $can_manage) {
        $result = $familie_controller->handleRequest();
        if (is_array($result) && $result['success'] && isset($result['familie'])) {
            $GLOBALS['edit_familie'] = $result['familie'];
        }
        if (isset($result['message'])) {
            $GLOBALS['message'] = $result['message'];
            $GLOBALS['message_type'] = $result['success'] ? 'success' : 'error';
        }
        return;
    }
    
    // Handle family member actions  
    if (in_array($action, $member_actions) && $can_manage) {
        include __DIR__ . '/controllers/FamilielidHandler.php';
        return;
    }
    
    // Handle user management (admin only)
    if (in_array($action, $user_actions)) {
        if ($user_role === 'admin') {
            include __DIR__ . '/controllers/UserHandler.php';
        } else {
            http_response_code(403);
            echo "Access denied.";
            exit;
        }
    }
}

// Handle change password page request (GET)
if (isset($_GET['action']) && $_GET['action'] === 'change_password') {
    include __DIR__ . '/views/change_password.php';
    exit;
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: /Ledenadministratie/views/login.php');
    exit;
}

// Show the correct dashboard based on user role
$dashboards = array(
    'admin' => '/views/dashboard_admin.php',
    'treasurer' => '/views/dashboard_treasurer.php', 
    'secretary' => '/views/dashboard_secretary.php'
);

if (isset($dashboards[$current_user_role])) {
    include __DIR__ . $dashboards[$current_user_role];
} else {
    http_response_code(403);
    echo "Access denied.";
    exit;
}
?>