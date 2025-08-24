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
$database_connection = new config\Connection();
$database_pdo = $database_connection->getConnection();
$user_controller = new UserController($database_pdo);
$auth_controller = new AuthController($database_pdo);
$familie_controller = new FamilieController($database_pdo);

// Load member types and set compatibility variables for views
$pdo = $database_pdo;
$userController = $user_controller;
$authController = $auth_controller;

// Check if user is logged in and get role
$user_is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];

$current_user_role = '';
if (isset($_SESSION['role'])) {
    $current_user_role = $_SESSION['role'];
}

// If user is not logged in, show login page
if (!$user_is_logged_in) {
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
    $action = '';
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }
    
    $handler = '';
    if (isset($_POST['handler'])) {
        $handler = $_POST['handler'];
    }
    
    // Handle contributie (treasurer only)
    if ($user_role === 'treasurer' && $handler === 'contributie') {
        include __DIR__ . '/controllers/ContributieHandler.php';
        return;
    }
    
    // Handle password changes (all users)  
    if ($action === 'change_password') {
        include __DIR__ . '/controllers/PasswordHandler.php';
        return;
    }
    
    // Check user permissions for family management
    $can_manage_families = ($user_role === 'secretary');
    
    // Handle family actions (secretary only)
    if (($action === 'add_family' || $action === 'edit_family' || $action === 'delete_family') && $can_manage_families) {
        $result = $familie_controller->handleRequest();
        
        // Process the result and set session messages for user feedback
        if (isset($result['success'])) {
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = 'error';
            }
        }
        
        // Redirect to prevent form resubmission
        header('Location: /Ledenadministratie/index.php');
        exit;
    }
    
    // Handle family member actions (secretary only)
    if (($action === 'add_familielid' || $action === 'edit_familielid' || $action === 'delete_familielid') && $can_manage_families) {
        include __DIR__ . '/controllers/FamilielidHandler.php';
        return;
    }
    
    // Handle user management (admin only)
    if ($action === 'add_user' || $action === 'edit_user' || $action === 'delete_user') {
        if ($user_role === 'admin') {
            include __DIR__ . '/controllers/UserHandler.php';
        } else {
            http_response_code(403);
            echo "Toegang geweigerd.";
            exit;
        }
    }
}

// Handle change password page request (GET)
$get_action = '';
if (isset($_GET['action'])) {
    $get_action = $_GET['action'];
}

if ($get_action === 'change_password') {
    include __DIR__ . '/views/change_password.php';
    exit;
}

// Handle logout action
if ($get_action === 'logout') {
    session_destroy();
    header('Location: /Ledenadministratie/views/login.php');
    exit;
}

// Check for session messages and set variables for dashboard display
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}
if (isset($_SESSION['message_type'])) {
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message_type']); // Clear the message type after displaying
}

// Show the correct dashboard based on user role
if ($current_user_role === 'admin') {
    include __DIR__ . '/views/dashboard_admin.php';
} elseif ($current_user_role === 'treasurer') {
    include __DIR__ . '/views/dashboard_treasurer.php';
} elseif ($current_user_role === 'secretary') {
    include __DIR__ . '/views/dashboard_secretary.php';
} else {
    http_response_code(403);
    echo "Toegang geweigerd.";
    exit;
}
?>