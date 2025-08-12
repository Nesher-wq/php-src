<?php
// handlers/family_handler.php - Familie management voor secretary role

if ($_SESSION['role'] !== 'secretary' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

require_once __DIR__ . '/../controllers/FamilieController.php';
require_once __DIR__ . '/../controllers/FamilielidController.php';

$familieController = new FamilieController($pdo);
$familielidController = new FamilielidController($pdo);

// Nieuwe familie toevoegen
if (isset($_POST['add_familie'])) {
    $result = $familieController->createFamilie(
        $_POST['familie_naam'],
        $_POST['familie_straat'],
        $_POST['familie_huisnummer'],
        $_POST['familie_postcode'],
        $_POST['familie_woonplaats']
    );
    
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
    $result = $familieController->deleteFamilie($_POST['delete_familie_id']);
    $message = $result ? "Familie succesvol verwijderd." : "Fout bij het verwijderen van de familie.";
    $message_type = $result ? "success" : "error";
}

// Bewerken annuleren
if (isset($_POST['cancel_edit'])) {
    unset($edit_familie, $edit_familielid);
}

// Familielid toevoegen
if (isset($_POST['add_familielid'])) {
    $familie_id = $_POST['familie_id'];
    $result = $familielidController->createFamilielid(
        $familie_id,
        $_POST['familielid_naam'],
        $_POST['familielid_geboortedatum'],
        $_POST['soort_familielid'] ?? '',
        $_POST['stalling'] ?? 0
    );
    
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
    $familie_id = $_POST['familie_id'];
    $result = $familielidController->updateFamilielid(
        $_POST['familielid_id'],
        $_POST['familielid_naam'],
        $_POST['familielid_geboortedatum'],
        $_POST['soort_familielid'] ?? '',
        $_POST['stalling'] ?? 0
    );
    
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
