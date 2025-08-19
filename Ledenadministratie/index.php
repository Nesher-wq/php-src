<?php
// index.php - Front controller voor Ledenadministratie (MVC)
define('INCLUDED_FROM_INDEX', true);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize dependencies
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/FamilieController.php';
require_once __DIR__ . '/controllers/FamilielidController.php';
require_once __DIR__ . '/models/Soortlid.php';

use config\Connection;

$conn = new Connection();
$pdo = $conn->getConnection();
$userController = new UserController($pdo);
$authController = new AuthController($pdo);
$familieController = new FamilieController($pdo);

// Load soort leden for dropdown usage
$soortlidModel = new \models\Soortlid();
$soort_leden = $soortlidModel->getAllSoortleden();

// Safe session checks
$isLoggedIn = !empty($_SESSION['loggedin']);
$userRole   = $_SESSION['role'] ?? null;

error_log('Index.php - isLoggedIn: ' . ($isLoggedIn ? 'YES' : 'NO'));
error_log('Index.php - userRole: ' . ($userRole ?? 'NULL'));

// If not logged in -> show login view and stop
if (!$isLoggedIn) {
    error_log('Index.php - User not logged in, showing login form');
    
    // login view should handle its own form action (to handlers/auth_handler.php) or post back here
    require_once __DIR__ . '/views/login.php';
    exit;
}

error_log('Index.php - User is logged in, proceeding to dashboard');

// Add these checks before accessing array values
if (isset($_POST['action']) && $_POST['action'] === 'edit_familielid') {
    $edit_familielid_id = $_POST['edit_familielid_id'] ?? null;
    $edit_familie_id = $_POST['edit_familie_id'] ?? null;
    
    if ($edit_familielid_id && $edit_familie_id) {
        $familielidController = new FamilielidController($pdo);
        $edit_familielid = $familielidController->getFamilielidById($edit_familielid_id);
        
        if ($edit_familielid) {
            $edit_familie = $familieController->getFamilieById($edit_familie_id);
            if (!$edit_familie) {
                $edit_familielid = null;
                $message = "Fout: Familie niet gevonden.";
                $message_type = "error";
            }
        } else {
            $message = "Fout: Familielid niet gevonden.";
            $message_type = "error";
        }
    } else {
        $message = "Fout: Ongeldige parameters voor bewerken familielid.";
        $message_type = "error";
    }
}

// At this point user is logged in; include handlers as needed (role-checked)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request ontvangen in index.php");
    error_log("POST data: " . print_r($_POST, true));
    
    // Contributie handler (treasurer)
    if (isset($_POST['handler']) && $_POST['handler'] === 'contributie') {
        if ($userRole === 'treasurer') {
            include __DIR__ . '/handlers/contributie_handler.php';
        } else {
            http_response_code(403);
            echo "Toegang geweigerd.";
            exit;
        }
    }
    
    // Password changes - any logged in user
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        include __DIR__ . '/handlers/password_handler.php';
    }

    // Family management - secretary or admin
    $familyActions = ['add_family','edit_family','delete_family'];
    $familielidActions = ['add_familielid','edit_familielid','delete_familielid'];
    
    if (isset($_POST['action'])) {
        if (in_array($_POST['action'], $familyActions, true)) {
            if (in_array($userRole, ['secretary','admin'], true)) {
                $result = $familieController->handleRequest();
                if (is_array($result)) {
                    // First set the edit_familie if available
                    if ($result['success'] && isset($result['familie'])) {
                        $edit_familie = $result['familie'];
                    }
                    // Only set message if explicitly provided
                    if (isset($result['message'])) {
                        $message = $result['message'];
                        $message_type = $result['success'] ? 'success' : 'error';
                    }
                }
            }
        } elseif (in_array($_POST['action'], $familielidActions, true)) {
            if (in_array($userRole, ['secretary','admin'], true)) {
                include __DIR__ . '/handlers/familielid_handler.php';
            }
        }
    }

    // User management - admin only
    $userActions = ['add_user','edit_user','delete_user'];
    if (isset($_POST['action']) && in_array($_POST['action'], $userActions, true)) {
        if ($userRole === 'admin') {
            include __DIR__ . '/handlers/user_handler.php';
        } else {
            http_response_code(403);
            echo "Toegang geweigerd.";
            exit;
        }
    }
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    error_log('Index.php - Logging out user: ' . ($_SESSION['username'] ?? 'UNKNOWN'));
    session_destroy();
    header('Location: /Ledenadministratie/views/login.php');
    exit;
}

// Render dashboard based on role
switch ($userRole) {
    case 'admin':
        include __DIR__ . '/views/dashboard_admin.php';
        break;
    case 'treasurer':
        include __DIR__ . '/views/dashboard_treasurer.php';
        break;
    case 'secretary':
        include __DIR__ . '/views/dashboard_secretary.php';
        break;
    default:
        http_response_code(403);
        echo "Toegang geweigerd.";
        exit;
}
?>