<?php
// handlers/user_handler.php - User management voor admin role

if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// Create user
if (isset($_POST['create_user'])) {
    $result = $userController->createUser(
        $_POST['new_username'],
        $_POST['new_password'],
        $_POST['new_role'],
        $_POST['new_description'] ?? ''
    );
    
    $message = $result ? "Gebruiker succesvol aangemaakt." : "Fout bij het aanmaken van de gebruiker.";
    $message_type = $result ? "success" : "error";
}

// Show edit form
if (isset($_POST['show_edit_form'])) {
    $edit_id = $_POST['edit_id'];
    $edit_user = $userController->getUserById($edit_id);
}

// Update user
if (isset($_POST['update_user'])) {
    $result = $userController->updateUser(
        $_POST['edit_user_id'],
        $_POST['edit_username'],
        $_POST['edit_password'], // Kan leeg zijn
        $_POST['edit_role'],
        $_POST['edit_description'] ?? ''
    );
    
    $message = $result ? "Gebruiker succesvol bijgewerkt." : "Fout bij het bijwerken van de gebruiker.";
    $message_type = $result ? "success" : "error";
}

// Delete user
if (isset($_POST['delete_user'])) {
    $userId = $_POST['delete_id'];
    
    // Controleer of gebruiker niet zichzelf verwijdert
    if ($userId != $_SESSION['user_id']) {
        $result = $userController->deleteUser($userId);
        $message = $result ? "Gebruiker succesvol verwijderd." : "Fout bij het verwijderen van de gebruiker.";
        $message_type = $result ? "success" : "error";
    } else {
        $message = "Je kunt jezelf niet verwijderen.";
        $message_type = "error";
    }
}
?>
