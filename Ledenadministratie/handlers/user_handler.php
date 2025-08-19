<?php
// handlers/user_handler.php - User management voor admin role

if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// Handle different actions based on the action parameter
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_user':
        $result = $userController->createUser(
            $_POST['username'],
            $_POST['password'],
            $_POST['role'],
            $_POST['description'] ?? ''
        );
        
        $message = $result ? "Gebruiker succesvol aangemaakt." : "Fout bij het aanmaken van de gebruiker.";
        $message_type = $result ? "success" : "error";
        break;
        
    case 'edit_user':
        $result = $userController->updateUser(
            $_POST['user_id'],
            $_POST['username'],
            $_POST['password'] ?? '', // Password is optional for updates
            $_POST['role'],
            $_POST['description'] ?? ''
        );
        
        $message = $result ? "Gebruiker succesvol bijgewerkt." : "Fout bij het bijwerken van de gebruiker.";
        $message_type = $result ? "success" : "error";
        break;
        
    case 'delete_user':
        $userId = $_POST['user_id'];
        
        // Controleer of gebruiker niet zichzelf verwijdert
        // Voor bestaande sessies zonder user_id, gebruik username als fallback
        $currentUserId = $_SESSION['user_id'] ?? null;
        $currentUsername = $_SESSION['username'] ?? '';
        
        $canDelete = true;
        
        if ($currentUserId && $userId == $currentUserId) {
            $canDelete = false;
        } elseif (!$currentUserId && $currentUsername) {
            // Fallback: check by username if user_id not available in session
            $userToDelete = $userController->getUserById($userId);
            if ($userToDelete && $userToDelete['username'] === $currentUsername) {
                $canDelete = false;
            }
        }
        
        if ($canDelete) {
            $result = $userController->deleteUser($userId);
            $message = $result ? "Gebruiker succesvol verwijderd." : "Fout bij het verwijderen van de gebruiker.";
            $message_type = $result ? "success" : "error";
        } else {
            $message = "Je kunt jezelf niet verwijderen.";
            $message_type = "error";
        }
        break;
}
?>
