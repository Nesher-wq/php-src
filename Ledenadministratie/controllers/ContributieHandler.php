<?php
// This file handles contribution calculation requests from users
// All business logic is handled by the ContributieController

// Access global PDO connection variable
global $pdo;

// First check if this is a POST request with the correct action
$requestIsPostMethod = ($_SERVER['REQUEST_METHOD'] === 'POST');

$actionParameterExists = isset($_POST['action']);
$actionIsCorrect = false;
if ($actionParameterExists) {
    $actionFromPost = $_POST['action'];
    $actionIsCorrect = ($actionFromPost === 'bereken_contributies');
}

// Only process if this is the correct POST request
if ($requestIsPostMethod && $actionParameterExists && $actionIsCorrect) {
    // Include the ContributieController
    require_once __DIR__ . '/ContributieController.php';
    
    // Create a ContributieController object
    $contributieControllerObject = new ContributieController($pdo);
    
    // Check if user has access to contribution calculations
    $userHasAccess = $contributieControllerObject->validateAccess($_SESSION);
    
    // If user doesn't have access, show error message
    if (!$userHasAccess) {
        $_SESSION['message'] = 'Geen toegang tot contributie berekening.';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Get the boekjaar from POST data
    $boekjaarFromForm = null;
    if (isset($_POST['boekjaar'])) {
        $boekjaarFromForm = $_POST['boekjaar'];
    }
    
    // Check if boekjaar was provided
    $boekjaarIsEmpty = ($boekjaarFromForm == null || $boekjaarFromForm == '');
    
    // If no boekjaar was selected, show error
    if ($boekjaarIsEmpty) {
        $_SESSION['message'] = 'Geen boekjaar geselecteerd.';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Clear previous session data before new calculation
    if (isset($_SESSION['berekende_contributies'])) {
        unset($_SESSION['berekende_contributies']);
    }
    
    if (isset($_SESSION['geselecteerd_boekjaar'])) {
        unset($_SESSION['geselecteerd_boekjaar']);
    }
    
    // Perform the contribution calculation
    $calculationResult = $contributieControllerObject->berekenContributies($boekjaarFromForm);
    
    // Check if calculation was successful
    $calculationWasSuccessful = (isset($calculationResult['success']) && $calculationResult['success']);
    
    // Handle successful calculation
    if ($calculationWasSuccessful) {
        // Store the calculated contributions in session
        if (isset($calculationResult['contributies'])) {
            $_SESSION['berekende_contributies'] = $calculationResult['contributies'];
        }
        
        // Store the selected boekjaar in session
        $_SESSION['geselecteerd_boekjaar'] = $boekjaarFromForm;
        
        // Set success message
        if (isset($calculationResult['message'])) {
            $_SESSION['message'] = $calculationResult['message'];
        } else {
            $_SESSION['message'] = 'Contributies succesvol berekend.';
        }
        $_SESSION['message_type'] = 'success';
    }
    
    // Handle failed calculation
    if (!$calculationWasSuccessful) {
        // Set error message
        $errorMessageText = 'Fout bij berekenen contributies: ';
        if (isset($calculationResult['error'])) {
            $errorMessageText = $errorMessageText . $calculationResult['error'];
        } else {
            $errorMessageText = $errorMessageText . 'Onbekende fout';
        }
        
        $_SESSION['message'] = $errorMessageText;
        $_SESSION['message_type'] = 'error';
    }
}
?>