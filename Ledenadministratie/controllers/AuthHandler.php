<?php
// This file handles login and logout requests from users
// Include the required files
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/connection.php';

use config\Connection;

// Check if session is already started, if not start it
$sessionIsActive = false;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    $sessionIsActive = true;
} else {
    $sessionIsActive = true;
}

// Check if this is a POST request (login attempt)
$requestMethodIsPost = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestMethodIsPost = true;
}

// Handle login POST request
if ($requestMethodIsPost == true) {
    // Get username and password from POST data
    $usernameFromForm = '';
    $passwordFromForm = '';
    
    // Check if username was provided
    $usernameFieldExists = false;
    if (isset($_POST['username'])) {
        $usernameFieldExists = true;
        $usernameFromForm = $_POST['username'];
    }
    
    // Check if password was provided
    $passwordFieldExists = false;
    if (isset($_POST['password'])) {
        $passwordFieldExists = true;
        $passwordFromForm = $_POST['password'];
    }
    
    // Check if both username and password are provided
    $usernameIsEmpty = false;
    if ($usernameFromForm == '') {
        $usernameIsEmpty = true;
    }
    
    $passwordIsEmpty = false;
    if ($passwordFromForm == '') {
        $passwordIsEmpty = true;
    }
    
    // If username or password is empty, show error
    $credentialsAreMissing = false;
    if ($usernameIsEmpty == true) {
        $credentialsAreMissing = true;
    }
    if ($passwordIsEmpty == true) {
        $credentialsAreMissing = true;
    }
    
    if ($credentialsAreMissing == true) {
        $_SESSION['login_error'] = 'Gebruikersnaam en wachtwoord zijn verplicht.';
        $redirectLocation = 'Location: /Ledenadministratie/index.php';
        header($redirectLocation);
        exit;
    }

    // Try to perform login
    $databaseErrorOccurred = false;
    $loginWasSuccessful = false;
    
    try {
        // Create database connection
        $connectionObject = new Connection();
        $databaseConnection = $connectionObject->getConnection();
        
        // Create AuthController object
        $authControllerObject = new AuthController($databaseConnection);
        
        // Attempt login
        $loginResult = $authControllerObject->login($usernameFromForm, $passwordFromForm);
        
        // Check if login was successful
        if ($loginResult == true) {
            $loginWasSuccessful = true;
        }
        
    } catch (Exception $exceptionObject) {
        // If exception occurred during login
        $databaseErrorOccurred = true;
        $errorLogMessage = "Auth handler: Exception: " . $exceptionObject->getMessage();
        error_log($errorLogMessage);
    }
    
    // Handle successful login
    if ($loginWasSuccessful == true) {
        $_SESSION['login_success'] = 'Login succesvol!';
        $successRedirectLocation = 'Location: /Ledenadministratie/index.php';
        header($successRedirectLocation);
        exit;
    }
    
    // Handle failed login
    if ($loginWasSuccessful == false && $databaseErrorOccurred == false) {
        $_SESSION['login_error'] = 'Ongeldige gebruikersnaam of wachtwoord.';
        $errorRedirectLocation = 'Location: /Ledenadministratie/index.php';
        header($errorRedirectLocation);
        exit;
    }
    
    // Handle database error during login
    if ($databaseErrorOccurred == true) {
        $_SESSION['login_error'] = 'Er is een fout opgetreden bij het inloggen: Database error';
        $databaseErrorRedirectLocation = 'Location: /Ledenadministratie/index.php';
        header($databaseErrorRedirectLocation);
        exit;
    }
}

// Check if this is a logout request
$logoutRequested = false;
$actionParameterExists = false;
if (isset($_GET['action'])) {
    $actionParameterExists = true;
    $actionFromGet = $_GET['action'];
    
    if ($actionFromGet === 'logout') {
        $logoutRequested = true;
    }
}

// Handle logout request
if ($logoutRequested == true) {
    // Clear all session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to main page
    $logoutRedirectLocation = 'Location: /Ledenadministratie/index.php';
    header($logoutRedirectLocation);
    exit;
}
?>