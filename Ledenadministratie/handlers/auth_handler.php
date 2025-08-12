<?php
// handlers/auth_handler.php - Authentication en routing

// LOGOUT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Login POST direct verwerken
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $login_error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $user = $authController->login($_POST['username'], $_POST['password']);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            header('Location: index.php');
            exit;
        } else {
            $login_error = 'Ongeldige gebruikersnaam of wachtwoord.';
        }
    }
    include __DIR__ . '/../views/login.php';
    exit;
}
?>
