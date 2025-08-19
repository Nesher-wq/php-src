<?php
// Contributie request handler - verwerkt alleen POST requests
// Alle business logic zit in ContributieController

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bereken_contributies') {
    require_once __DIR__ . '/../controllers/ContributieController.php';
    
    $contributieController = new ContributieController($pdo);
    
    // Toegangscontrole
    if (!$contributieController->validateAccess($_SESSION)) {
        $_SESSION['message'] = 'Geen toegang tot contributie berekening.';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    $boekjaar = $_POST['boekjaar'] ?? null;
    
    if (!$boekjaar) {
        $_SESSION['message'] = 'Geen boekjaar geselecteerd.';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Clear previous session data before new calculation
    unset($_SESSION['berekende_contributies']);
    unset($_SESSION['geselecteerd_boekjaar']);
    
    // Voer berekening uit
    $result = $contributieController->berekenContributies($boekjaar);
    
    if ($result['success']) {
        $_SESSION['berekende_contributies'] = $result['contributies'];
        $_SESSION['geselecteerd_boekjaar'] = $boekjaar;
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Fout bij berekenen contributies: ' . $result['error'];
        $_SESSION['message_type'] = 'error';
    }
}
?>