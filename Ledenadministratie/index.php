<?php
/**
 * index.php - Main Application Entry Point for Ledenadministratie System
 * 
 * This file serves as the central router and controller for the membership administration system.
 * It handles user authentication, role-based access control, and routes users to appropriate dashboards.
 * 
 * Flow:
 * 1. Initialize session and security
 * 2. Load required dependencies (models, controllers, utilities)
 * 3. Check user authentication status
 * 4. Route to login page OR role-specific dashboard (admin/secretary/treasurer)
 * 5. Handle form submissions and display appropriate views
 */

// Security: Define constant to prevent direct access to included files
define('INCLUDED_FROM_INDEX', true);

// Session Management: Ensure session is started for user authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dependency Loading: Include all required files for the application
require_once __DIR__ . '/../../Ledenadministratie_config/connection.php';  // Database connection (outside web root for security)
require_once __DIR__ . '/controllers/UserController.php';                   // User management operations
require_once __DIR__ . '/controllers/AuthController.php';                   // Authentication logic
require_once __DIR__ . '/controllers/FamilieController.php';                // Family management operations
require_once __DIR__ . '/controllers/FamilielidController.php';             // Family member management
require_once __DIR__ . '/models/Soortlid.php';                             // Member type definitions
require_once __DIR__ . '/includes/utils.php';                              // Utility functions (logging, etc.)

// Database & Controller Initialization: Set up core application components
$database_connection = new config\Connection();
$database_pdo = $database_connection->getConnection();
$user_controller = new UserController($database_pdo);
$auth_controller = new AuthController($database_pdo);
$familie_controller = new FamilieController($database_pdo);

// Compatibility Variables: Create aliases for use in included view files
// These allow views to access controllers using different variable names
$pdo = $database_pdo;               // Direct PDO access for views that need it
$userController = $user_controller;   // CamelCase alias for UserController
$authController = $auth_controller;   // CamelCase alias for AuthController

// Authentication Check: Determine if user is logged in and get their role
$user_is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];

// Role Management: Extract user role from session for access control
$current_user_role = $_SESSION['role'] ?? '';

// Request State Management: Initialize variables for form processing
$edit_familie = null;  // Will hold family data when editing

// Authentication Flow: Route user based on login status
if (!$user_is_logged_in) {
    // User Not Logged In: Show login page with any error/success messages
    
    // Message Handling: Check for login-related messages and display them
    $error_message = $_SESSION['error_message'] ?? '';
    $success_message = $_SESSION['success_message'] ?? '';
    
    if (isset($_SESSION['error_message'])) {
        unset($_SESSION['error_message']); // Clear message after displaying to prevent re-display
    }
    if (isset($_SESSION['success_message'])) {
        unset($_SESSION['success_message']); // Clear message after displaying to prevent re-display
    }
    
    // Display Login View: Include login form and exit (no further processing needed)
    require_once __DIR__ . '/views/login.php';
    exit;
}

// User Is Logged In: Handle form submissions and display appropriate dashboard

// POST Request Handling: Process form submissions from authenticated users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = handlePostRequest($current_user_role, $familie_controller);
    
    // Extract variables from result
    if (is_array($result) && isset($result['edit_familie'])) {
        // This is a familielid operation result with specific structure
        $edit_familie = $result['edit_familie'] ?? null;
        $edit_familielid = $result['edit_familielid'] ?? null;
        
        // Set session messages if provided
        if (!empty($result['message'] ?? '')) {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['message_type'] ?? 'info';
        }
    } else {
        // This is a regular familie operation result (direct famille data)
        $edit_familie = $result;
    }
}

/**
 * handlePostRequest - Central POST Request Router
 * 
 * This function processes all form submissions based on user role and action type.
 * It ensures role-based access control and routes requests to appropriate handlers.
 * 
 * @param string $user_role The current user's role (admin/secretary/treasurer)
 * @param FamilieController $familie_controller Controller for family operations
 * @return array|null Returns family data for editing, or null for other operations
 */
function handlePostRequest($user_role, $familie_controller) {
    $edit_familie_data = null; // Will hold family data if user is editing a family
    
    // Extract Action: Determine what operation the user wants to perform
    $action = $_POST['action'] ?? '';
    
    // Extract Handler: Determine which specialized handler should process this request
    $handler = $_POST['handler'] ?? '';
    
    // Treasurer Operations: Handle contribution-related requests
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
        
        // Extract familie data for edit form display
        $edit_familie_data = $result['familie'] ?? null;
        
        // Process the result and set session messages for user feedback
        if ($result['success'] ?? false) {
            // Only set success message if there's actually a message to display
            $message = $result['message'] ?? '';
            if (!empty(trim($message))) {
                $_SESSION['message'] = $message;
                $_SESSION['message_type'] = 'success';
            }
        } else {
            // Only set error message if there's actually a message to display
            $message = $result['message'] ?? '';
            if (!empty(trim($message))) {
                $_SESSION['message'] = $message;
                $_SESSION['message_type'] = 'error';
            }
        }
        
        // Redirect to prevent form resubmission for operations that don't need to show edit form
        if ($action === 'add_family' || $action === 'delete_family') {
            header('Location: /Ledenadministratie/index.php');
            exit;
        }
        
        // For edit_family operations that load the edit form, don't redirect
        // The edit form will be shown below
    }

    // Handle family member actions (secretary only)
    if (($action === 'add_familielid' || $action === 'edit_familielid' || $action === 'delete_familielid') && $can_manage_families) {
        // Include the handler and capture the variables it sets
        include __DIR__ . '/controllers/FamilielidHandler.php';
        
        // Return both familie and familielid data
        return array(
            'edit_familie' => $edit_familie ?? null,
            'edit_familielid' => $edit_familielid ?? null,
            'message' => $message ?? '',
            'message_type' => $message_type ?? ''
        );
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
    
    // Return any edit data for use in the global scope
    return $edit_familie_data;
}

// Handle change password page request (GET)
$get_action = $_GET['action'] ?? '';

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
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']); // Clear the message after displaying
}
if (isset($_SESSION['message_type'])) {
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