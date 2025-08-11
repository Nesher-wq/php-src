<?php
// index.php - Front controller voor Ledenadministratie (MVC)
session_start();
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AuthController.php';

$conn = new Connection();
$pdo = $conn->getConnection();
$userController = new UserController($pdo);
$authController = new AuthController($pdo);

// Allowed roles (voor views/controllers)
$allowed_roles = ['admin', 'treasurer', 'secretary'];

// LOGOUT
// Routing: alleen routeren, geen business logica
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
    include __DIR__ . '/views/login.php';
    exit;
}

// Vanaf hier is de gebruiker ingelogd
$message = null;
$message_type = '';

// Familie Management Actions (voor secretary)
if ($_SESSION['role'] === 'secretary' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/controllers/FamilieController.php';
    require_once __DIR__ . '/controllers/FamilielidController.php';
    $familieController = new FamilieController($pdo);
    $familielidController = new FamilielidController($pdo);

    // Nieuwe familie toevoegen
    if (isset($_POST['add_familie'])) {
        $naam = $_POST['familie_naam'];
        $straat = $_POST['familie_straat'];
        $huisnummer = $_POST['familie_huisnummer'];
        $postcode = $_POST['familie_postcode'];
        $woonplaats = $_POST['familie_woonplaats'];
        $result = $familieController->createFamilie($naam, $straat, $huisnummer, $postcode, $woonplaats);
        if ($result) {
            $message = "Familie succesvol toegevoegd.";
            $message_type = "success";
        } else {
            $message = "Fout bij het toevoegen van de familie.";
            $message_type = "error";
        }
    }

    // Familie bewerken formulier tonen
    if (isset($_POST['edit_familie'])) {
        $edit_familie_id = $_POST['edit_familie_id'];
        $edit_familie = $familieController->getFamilieById($edit_familie_id);
    }

    // Familie bijwerken
    if (isset($_POST['update_familie'])) {
        $id = $_POST['familie_id'];
        $naam = $_POST['familie_naam'];
        $straat = $_POST['familie_straat'];
        $huisnummer = $_POST['familie_huisnummer'];
        $postcode = $_POST['familie_postcode'];
        $woonplaats = $_POST['familie_woonplaats'];
        $result = $familieController->updateFamilie($id, $naam, $straat, $huisnummer, $postcode, $woonplaats);
        if ($result) {
            $message = "Familie succesvol bijgewerkt.";
            $message_type = "success";
        } else {
            $message = "Fout bij het bijwerken van de familie.";
            $message_type = "error";
        }
    }

    // Familie verwijderen
    if (isset($_POST['delete_familie'])) {
        $id = $_POST['delete_familie_id'];
        $result = $familieController->deleteFamilie($id);
        if ($result) {
            $message = "Familie succesvol verwijderd.";
            $message_type = "success";
        } else {
            $message = "Fout bij het verwijderen van de familie.";
            $message_type = "error";
        }
    }

    // Bewerken annuleren
    if (isset($_POST['cancel_edit'])) {
        unset($edit_familie);
        unset($edit_familielid);
    }

    // Familielid toevoegen
    if (isset($_POST['add_familielid'])) {
        $familie_id = $_POST['familie_id'];
        $naam = $_POST['familielid_naam'];
        $geboortedatum = $_POST['familielid_geboortedatum'];
        $omschrijving = $_POST['familielid_omschrijving'];
        
        $result = $familielidController->createFamilielid($familie_id, $naam, $geboortedatum, $omschrijving);
        
        if ($result) {
            $message = "Familielid succesvol toegevoegd.";
            $message_type = "success";
        } else {
            $message = "Fout bij het toevoegen van het familielid.";
            $message_type = "error";
        }
        
        // Herlaad familie voor edit-scherm
        $edit_familie = $familieController->getFamilieById($familie_id);
    }

    // Familielid bewerken formulier tonen
    if (isset($_POST['edit_familielid'])) {
        $edit_familielid_id = $_POST['edit_familielid_id'];
        $familie_id = $_POST['edit_familie_id'];
        
        $edit_familielid = $familielidController->getFamilielidById($edit_familielid_id);
        
        // Herlaad familie voor edit-scherm
        $edit_familie = $familieController->getFamilieById($familie_id);
    }

    // Familielid bijwerken
    if (isset($_POST['update_familielid'])) {
        $familielid_id = $_POST['familielid_id'];
        $familie_id = $_POST['familie_id'];
        $naam = $_POST['familielid_naam'];
        $geboortedatum = $_POST['familielid_geboortedatum'];
        $omschrijving = $_POST['familielid_omschrijving'];
        
        $result = $familielidController->updateFamilielid($familielid_id, $naam, $geboortedatum, $omschrijving);
        
        if ($result) {
            $message = "Familielid succesvol bijgewerkt.";
            $message_type = "success";
        } else {
            $message = "Fout bij het bijwerken van het familielid.";
            $message_type = "error";
        }
        
        // Herlaad familie voor edit-scherm
        $edit_familie = $familieController->getFamilieById($familie_id);
        unset($edit_familielid);
    }

    // Annuleren van familielid bewerken
    if (isset($_POST['cancel_edit_familielid'])) {
        $familie_id = $_POST['familie_id'];
        $edit_familie = $familieController->getFamilieById($familie_id);
        unset($edit_familielid);
    }

    // Familielid verwijderen
    if (isset($_POST['delete_familielid'])) {
        $familielid_id = $_POST['delete_familielid_id'];
        $familie_id = $_POST['familie_id'];
        
        $result = $familielidController->deleteFamilielid($familielid_id);
        
        if ($result) {
            $message = "Familielid succesvol verwijderd.";
            $message_type = "success";
        } else {
            $message = "Fout bij het verwijderen van het familielid.";
            $message_type = "error";
        }
        
        // Herlaad familie voor edit-scherm
        $edit_familie = $familieController->getFamilieById($familie_id);
    }
}

// User Management Actions (alleen voor admin)
if ($_SESSION['role'] === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create user
    if (isset($_POST['create_user'])) {
        $username = $_POST['new_username'];
        $password = $_POST['new_password'];
        $role = $_POST['new_role'];
        $description = $_POST['new_description'] ?? '';
        
        $result = $userController->createUser($username, $password, $role, $description);
        if ($result) {
            $message = "Gebruiker succesvol aangemaakt.";
            $message_type = "success";
        } else {
            $message = "Fout bij het aanmaken van de gebruiker.";
            $message_type = "error";
        }
    }
    
    // Show edit form
    if (isset($_POST['show_edit_form'])) {
        $edit_id = $_POST['edit_id'];
        $edit_user = $userController->getUserById($edit_id);
    }
    
    // Update user
    if (isset($_POST['update_user'])) {
        $userId = $_POST['edit_user_id'];
        $username = $_POST['edit_username'];
        $password = $_POST['edit_password']; // Kan leeg zijn
        $role = $_POST['edit_role'];
        $description = $_POST['edit_description'] ?? '';
        
        $result = $userController->updateUser($userId, $username, $password, $role, $description, $_SESSION['username']);
        if ($result) {
            $message = "Gebruiker succesvol bijgewerkt.";
            $message_type = "success";
        } else {
            $message = "Fout bij het bijwerken van de gebruiker. Je hebt mogelijk niet de juiste rechten of probeerde de hoofdadmin te wijzigen.";
            $message_type = "error";
        }
    }
    
    // Delete user
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['delete_id'];
        
        // Controleer of gebruiker niet zichzelf verwijdert
        if ($userId != $_SESSION['user_id']) {
            $result = $userController->deleteUser($userId, $_SESSION['username']);
            if ($result) {
                $message = "Gebruiker succesvol verwijderd.";
                $message_type = "success";
            } else {
                $message = "Fout bij het verwijderen van de gebruiker. Je hebt mogelijk niet de juiste rechten of probeerde de hoofdadmin te verwijderen.";
                $message_type = "error";
            }
        } else {
            $message = "Je kunt jezelf niet verwijderen.";
            $message_type = "error";
        }
    }
}

// Wachtwoord wijzigen (voor alle ingelogde gebruikers)
if (basename($_SERVER['SCRIPT_FILENAME']) === 'index.php' && isset($_GET['action']) && $_GET['action'] === 'change_password') {
    // Alleen via GET-parameter, niet direct via change_password.php
    include __DIR__ . '/views/change_password.php';
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
        $result = $userController->updateUser($userId, $_SESSION['username'], $new_password, $_SESSION['role'], $user['description'] ?? '', $_SESSION['username']);
        if ($result) {
            $message = 'Wachtwoord succesvol gewijzigd.';
            $message_type = 'success';
        } else {
            $message = 'Fout bij het wijzigen van het wachtwoord.';
            $message_type = 'error';
        }
    }
    include __DIR__ . '/views/change_password.php';
    exit;
}

// Render de juiste dashboard op basis van rol
if ($_SESSION['role'] === 'admin') {
    include __DIR__ . '/views/dashboard_admin.php';
} elseif ($_SESSION['role'] === 'treasurer') {
    include __DIR__ . '/views/dashboard_treasurer.php';
} elseif ($_SESSION['role'] === 'secretary') {
    include __DIR__ . '/views/dashboard_secretary.php';
} else {
    // fallback of error
    http_response_code(403);
    echo "Toegang geweigerd.";
    exit;
}