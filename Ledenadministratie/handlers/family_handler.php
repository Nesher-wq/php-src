<?php
require_once __DIR__ . '/../includes/utils.php';
writeLog('Family handler started - Method: ' . $_SERVER['REQUEST_METHOD']);
writeLog('POST data: ' . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    writeLog('Invalid request method, returning');
    return;
}

require_once __DIR__ . '/../controllers/FamilieController.php';

$familieController = new FamilieController($pdo);

// Nieuwe familie toevoegen
if (isset($_POST['add_familie'])) {
    writeLog('Attempting to add new family');
    writeLog('Familie naam: ' . $_POST['familie_naam']);
    
    $result = $familieController->createFamilie(
        $_POST['familie_naam'],
        $_POST['familie_straat'],
        $_POST['familie_huisnummer'],
        $_POST['familie_postcode'],
        $_POST['familie_woonplaats']
    );
    
    writeLog('Create family result: ' . ($result ? 'SUCCESS' : 'FAILED'));
    
    $message = $result ? "Familie succesvol toegevoegd." : "Fout bij het toevoegen van de familie.";
    $message_type = $result ? "success" : "error";
}

// Familie bewerken formulier tonen
if (isset($_POST['edit_familie'])) {
    $edit_familie_id = $_POST['edit_familie_id'];
    $edit_familie = $familieController->getFamilieById($edit_familie_id);
}

// Familie bijwerken
if (isset($_POST['update_familie'])) {
    $result = $familieController->updateFamilie(
        $_POST['familie_id'],
        $_POST['familie_naam'],
        $_POST['familie_straat'],
        $_POST['familie_huisnummer'],
        $_POST['familie_postcode'],
        $_POST['familie_woonplaats']
    );
    
    $message = $result ? "Familie succesvol bijgewerkt." : "Fout bij het bijwerken van de familie.";
    $message_type = $result ? "success" : "error";
}

// Familie verwijderen
if (isset($_POST['delete_familie'])) {
    writeLog('Attempting to delete family with ID: ' . $_POST['delete_familie_id']);
    
    $result = $familieController->deleteFamilie($_POST['delete_familie_id']);
    
    writeLog('Delete family result: ' . ($result ? 'SUCCESS' : 'FAILED'));
    
    $message = $result ? "Familie succesvol verwijderd." : "Fout bij het verwijderen van de familie.";
    $message_type = $result ? "success" : "error";
}

// Bewerken annuleren
if (isset($_POST['cancel_edit'])) {
    unset($edit_familie, $edit_familielid);
}
?>