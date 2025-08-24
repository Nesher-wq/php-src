<?php
// This file handles password change requests for all users

// Access global controller variables
global $authController, $userController;

// Check if user wants to access the change password page
$accessChangePasswordPage = false;
$scriptFilenameIsIndex = false;
$actionParameterExists = false;
$actionIsChangePassword = false;

// Check if this is the index.php script
$currentScriptFilename = basename($_SERVER['SCRIPT_FILENAME']);
if ($currentScriptFilename === 'index.php') {
    $scriptFilenameIsIndex = true;
}

// Check if action parameter exists
if (isset($_GET['action'])) {
    $actionParameterExists = true;
    $actionFromGet = $_GET['action'];
    
    // Check if action is change_password
    if ($actionFromGet === 'change_password') {
        $actionIsChangePassword = true;
    }
}

// If all conditions are met, show change password page
if ($scriptFilenameIsIndex && $actionParameterExists && $actionIsChangePassword) {
    $accessChangePasswordPage = true;
}

if ($accessChangePasswordPage) {
    include __DIR__ . '/../views/change_password.php';
    exit;
}

// Check if this is a password change POST request
$passwordChangePostRequest = false;
$requestMethodIsPost = false;
$changePasswordButtonClicked = false;

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestMethodIsPost = true;
}

// Check if change password button was clicked
if (isset($_POST['change_password'])) {
    $changePasswordButtonClicked = true;
}

// If both conditions are met, this is a password change request
if ($requestMethodIsPost && $changePasswordButtonClicked) {
    $passwordChangePostRequest = true;
}

// Handle password change POST request
if ($passwordChangePostRequest) {
    // Get form data
    $currentPasswordFromForm = '';
    $newPasswordFromForm = '';
    $confirmPasswordFromForm = '';
    
    // Get current password
    if (isset($_POST['current_password'])) {
        $currentPasswordFromForm = $_POST['current_password'];
    }
    
    // Get new password
    if (isset($_POST['new_user_password'])) {
        $newPasswordFromForm = $_POST['new_user_password'];
    }
    
    // Get confirm password
    if (isset($_POST['confirm_password'])) {
        $confirmPasswordFromForm = $_POST['confirm_password'];
    }
    
    // Get user ID from session
    $userIdFromSession = $_SESSION['user_id'];
    
    // Verify current password by attempting login
    $currentPasswordIsCorrect = $authController->login($_SESSION['username'], $currentPasswordFromForm);
    
    // Initialize result variables
    $passwordChangeSuccessful = false;
    $message = '';
    $message_type = '';
    
    // Check if current password is incorrect
    $currentPasswordIsIncorrect = false;
    if (!$currentPasswordIsCorrect) {
        $currentPasswordIsIncorrect = true;
    }
    
    if ($currentPasswordIsIncorrect) {
        $message = 'Huidig wachtwoord is onjuist.';
        $message_type = 'error';
    } else {
        // Check if new password meets minimum length requirement
        $newPasswordLength = strlen($newPasswordFromForm);
        $newPasswordIsTooShort = false;
        if ($newPasswordLength < 6) {
            $newPasswordIsTooShort = true;
        }
        
        if ($newPasswordIsTooShort) {
            $message = 'Nieuw wachtwoord moet minimaal 6 tekens zijn.';
            $message_type = 'error';
        } else {
            // Check if new password and confirmation match
            $passwordsDoNotMatch = false;
            if ($newPasswordFromForm !== $confirmPasswordFromForm) {
                $passwordsDoNotMatch = true;
            }
            
            if ($passwordsDoNotMatch) {
                $message = 'Nieuw wachtwoord en bevestiging komen niet overeen.';
                $message_type = 'error';
            } else {
                // Get user description for update
                $userDescriptionForUpdate = '';
                $userDescriptionExists = false;
                if (isset($currentPasswordIsCorrect['description'])) {
                    $userDescriptionExists = true;
                    $userDescriptionForUpdate = $currentPasswordIsCorrect['description'];
                }
                
                // Update password via UserController
                $updateResult = $userController->updateUser(
                    $userIdFromSession, 
                    $_SESSION['username'], 
                    $newPasswordFromForm, 
                    $_SESSION['role'], 
                    $userDescriptionForUpdate
                );
                
                // Check if update was successful
                $updateWasSuccessful = false;
                if ($updateResult) {
                    $updateWasSuccessful = true;
                }
                
                if ($updateWasSuccessful) {
                    $message = 'Wachtwoord succesvol gewijzigd.';
                    $message_type = 'success';
                    $passwordChangeSuccessful = true;
                } else {
                    $message = 'Fout bij het wijzigen van het wachtwoord.';
                    $message_type = 'error';
                }
            }
        }
    }
    
    // Include the change password view to show result
    include __DIR__ . '/../views/change_password.php';
    exit;
}
?>
