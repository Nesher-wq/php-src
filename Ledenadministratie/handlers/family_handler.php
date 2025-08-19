<?php
require_once __DIR__ . '/../includes/utils.php';
writeLog('Family handler started - Method: ' . $_SERVER['REQUEST_METHOD']);
writeLog('POST data: ' . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    writeLog('Invalid request method, returning');
    return;
}

require_once __DIR__ . '/../controllers/FamilieController.php';
require_once __DIR__ . '/../controllers/FamilielidController.php';

$familieController = new FamilieController($pdo);
$familielidController = new FamilielidController($pdo);

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

// Familielid toevoegen
if (isset($_POST['add_familielid'])) {
    writeLog('Attempting to add new family member');
    writeLog('POST data for family member: ' . print_r($_POST, true));
    
    $familie_id = $_POST['familie_id'];
    $result = $familielidController->createFamilielid(
        $familie_id,
        $_POST['familielid_naam'],
        $_POST['familielid_geboortedatum'],
        $_POST['soort_familielid'] ?? '',
        $_POST['stalling'] ?? 0
    );
    
    writeLog('Create family member result: ' . ($result ? 'SUCCESS' : 'FAILED'));
    
    $message = $result ? "Familielid succesvol toegevoegd." : "Fout bij het toevoegen van het familielid.";
    $message_type = $result ? "success" : "error";
    
    // Herlaad familie voor edit-scherm
    $edit_familie = $familieController->getFamilieById($familie_id);
}

// Familielid bewerken formulier tonen
if (isset($_POST['edit_familielid'])) {
    $edit_familielid_id = $_POST['edit_familielid_id'];
    $familie_id = $_POST['edit_familie_id'];
    
    $edit_familielid = $familielidController->getFamilielidById($edit_familielid_id);
    $edit_familie = $familieController->getFamilieById($familie_id);
}

// Familielid bijwerken
if (isset($_POST['update_familielid'])) {
    writeLog('Attempting to update family member');
    writeLog('POST data for family member update: ' . print_r($_POST, true));
    
    $familie_id = $_POST['familie_id'];
    $result = $familielidController->updateFamilielid(
        $_POST['familielid_id'],
        $_POST['familielid_naam'],
        $_POST['familielid_geboortedatum'],
        $_POST['soort_familielid'] ?? '',
        $_POST['stalling'] ?? 0
    );
    
    writeLog('Update family member result: ' . ($result ? 'SUCCESS' : 'FAILED'));
    
    $message = $result ? "Familielid succesvol bijgewerkt." : "Fout bij het bijwerken van het familielid.";
    $message_type = $result ? "success" : "error";
    
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
    $familie_id = $_POST['familie_id'];
    $result = $familielidController->deleteFamilielid($_POST['delete_familielid_id']);
    
    $message = $result ? "Familielid succesvol verwijderd." : "Fout bij het verwijderen van het familielid.";
    $message_type = $result ? "success" : "error";
    
    // Herlaad familie voor edit-scherm
    $edit_familie = $familieController->getFamilieById($familie_id);
}
?>
