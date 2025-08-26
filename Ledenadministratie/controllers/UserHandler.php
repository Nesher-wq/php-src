<?php
// This file handles user management requests for admin users

// Access global controller variable
global $userController;

// First check if user has admin role
$userIsAdmin = false;
$sessionRoleExists = false;
if (isset($_SESSION['role'])) {
    $sessionRoleExists = true;
    $userRoleFromSession = $_SESSION['role'];
    
    if ($userRoleFromSession === 'admin') {
        $userIsAdmin = true;
    }
}

// Check if request method is POST
$requestMethodIsPost = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestMethodIsPost = true;
}

// Only continue if user is admin and request is POST
$shouldProcessRequest = false;
if ($userIsAdmin && $requestMethodIsPost) {
    $shouldProcessRequest = true;
}

if (!$shouldProcessRequest) {
    return;
}

// Get the action parameter
$actionFromPost = $_POST['action'] ?? '';

// Handle different actions
$actionIsAddUser = false;
if ($actionFromPost === 'add_user') {
    $actionIsAddUser = true;
}

// Handle add user action
if ($actionIsAddUser) {
    // Get form data for creating user
    $usernameFromForm = $_POST['username'] ?? '';
    
    $passwordFromForm = $_POST['password'] ?? '';
    
    $roleFromForm = $_POST['role'] ?? '';
    
    $descriptionFromForm = $_POST['description'] ?? '';
    
    // Try to create the user
    $createUserResult = $userController->createUser(
        $usernameFromForm,
        $passwordFromForm,
        $roleFromForm,
        $descriptionFromForm
    );
    
    // Check if creation was successful
    $createWasSuccessful = false;
    if ($createUserResult) {
        $createWasSuccessful = true;
    }
    
    // Set appropriate message
    if ($createWasSuccessful) {
        $_SESSION['message'] = "Gebruiker succesvol aangemaakt.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Fout bij het aanmaken van de gebruiker.";
        $_SESSION['message_type'] = "error";
    }
}

// Check for edit user action
$actionIsEditUser = false;
if ($actionFromPost === 'edit_user') {
    $actionIsEditUser = true;
}

// Handle edit user action
if ($actionIsEditUser) {
    // Get form data for updating user
    $userIdFromForm = $_POST['user_id'] ?? '';
    
    $usernameFromForm = $_POST['username'] ?? '';
    
    $passwordFromForm = $_POST['password'] ?? '';
    
    $roleFromForm = $_POST['role'] ?? '';
    
    $descriptionFromForm = $_POST['description'] ?? '';
    
    // Try to update the user
    $updateUserResult = $userController->updateUser(
        $userIdFromForm,
        $usernameFromForm,
        $passwordFromForm,
        $roleFromForm,
        $descriptionFromForm
    );
    
    // Check if update was successful
    $updateWasSuccessful = false;
    if ($updateUserResult) {
        $updateWasSuccessful = true;
    }
    
    // Set appropriate message
    if ($updateWasSuccessful) {
        $_SESSION['message'] = "Gebruiker succesvol bijgewerkt.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Fout bij het bijwerken van de gebruiker.";
        $_SESSION['message_type'] = "error";
    }
}

// Check for delete user action
$actionIsDeleteUser = false;
if ($actionFromPost === 'delete_user') {
    $actionIsDeleteUser = true;
}

// Handle delete user action
if ($actionIsDeleteUser) {
    // Get user ID to delete
    $userIdToDelete = $_POST['user_id'] ?? '';
    
    // Check if user is trying to delete themselves
    $userCanDeleteThisUser = true;
    
    // Get current user ID from session
    $currentUserIdFromSession = null;
    $currentUserIdExists = false;
    if (isset($_SESSION['user_id'])) {
        $currentUserIdExists = true;
        $currentUserIdFromSession = $_SESSION['user_id'];
    }
    
    // Get current username from session
    $currentUsernameFromSession = '';
    $currentUsernameExists = false;
    if (isset($_SESSION['username'])) {
        $currentUsernameExists = true;
        $currentUsernameFromSession = $_SESSION['username'];
    }
    
    // Check if user is trying to delete themselves by ID
    if ($currentUserIdExists) {
        if ($userIdToDelete == $currentUserIdFromSession) {
            $userCanDeleteThisUser = false;
        }
    }
    
    // Check by username if user_id not available in session
    if (!$currentUserIdExists && $currentUsernameExists) {
        // Get user data for the user to delete
        $userToDeleteData = $userController->getUserById($userIdToDelete);
        
        // Check if usernames match
        $usernamesMatch = false;
        if ($userToDeleteData != null) {
            if (isset($userToDeleteData['username'])) {
                if ($userToDeleteData['username'] === $currentUsernameFromSession) {
                    $usernamesMatch = true;
                }
            }
        }
        
        if ($usernamesMatch) {
            $userCanDeleteThisUser = false;
        }
    }
    
    // Proceed with deletion if allowed
    if ($userCanDeleteThisUser) {
        // Try to delete the user
        $deleteUserResult = $userController->deleteUser($userIdToDelete);
        
        // Check if deletion was successful
        $deleteWasSuccessful = false;
        if ($deleteUserResult) {
            $deleteWasSuccessful = true;
        }
        
        // Set appropriate message
        if ($deleteWasSuccessful) {
            $_SESSION['message'] = "Gebruiker succesvol verwijderd.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Fout bij het verwijderen van de gebruiker.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        // User tried to delete themselves
        $_SESSION['message'] = "Je kunt jezelf niet verwijderen.";
        $_SESSION['message_type'] = "error";
    }
}
?>
