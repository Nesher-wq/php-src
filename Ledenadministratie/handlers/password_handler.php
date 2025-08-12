<?php
// handlers/password_handler.php - Wachtwoord wijzigen voor alle gebruikers

// Wachtwoord wijzigen (voor alle ingelogde gebruikers)
if (basename($_SERVER['SCRIPT_FILENAME']) === 'index.php' && isset($_GET['action']) && $_GET['action'] === 'change_password') {
    include __DIR__ . '/../views/change_password.php';
    exit;
}

// Afhandeling wachtwoord wijzigen (POST vanaf change_password.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_user_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $userId = $_SESSION['user_id'];
    
    $user = $authController->login($_SESSION['username'], $current_password);
    
    if (!$user) {
        $message = 'Huidig wachtwoord is onjuist.';
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'Nieuw wachtwoord moet minimaal 6 tekens zijn.';
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Nieuw wachtwoord en bevestiging komen niet overeen.';
        $message_type = 'error';
    } else {
        // Update wachtwoord via UserController
        $result = $userController->updateUser($userId, $_SESSION['username'], $new_password, $_SESSION['role'], $user['description'] ?? '');
        if ($result) {
            $message = 'Wachtwoord succesvol gewijzigd.';
            $message_type = 'success';
        } else {
            $message = 'Fout bij het wijzigen van het wachtwoord.';
            $message_type = 'error';
        }
    }
    include __DIR__ . '/../views/change_password.php';
    exit;
}
?>
