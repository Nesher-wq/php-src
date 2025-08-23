<?php
// This file handles contribution calculation requests from users
// All business logic is handled by the ContributieController

// First check if this is a POST request with the correct action
$requestIsPostMethod = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestIsPostMethod = true;
}

$actionParameterExists = false;
$actionIsCorrect = false;
if (isset($_POST['action'])) {
    $actionParameterExists = true;
    $actionFromPost = $_POST['action'];
    
    if ($actionFromPost === 'bereken_contributies') {
        $actionIsCorrect = true;
    }
}

// Only process if this is the correct POST request
$shouldProcessRequest = false;
if ($requestIsPostMethod == true && $actionParameterExists == true && $actionIsCorrect == true) {
    $shouldProcessRequest = true;
}

if ($shouldProcessRequest == true) {
    // Include the ContributieController
    require_once __DIR__ . '/ContributieController.php';
    
    // Create a ContributieController object
    $contributieControllerObject = new ContributieController($pdo);
    
    // Check if user has access to contribution calculations
    $userHasAccess = $contributieControllerObject->validateAccess($_SESSION);
    
    // If user doesn't have access, show error message
    if ($userHasAccess == false) {
        $_SESSION['message'] = 'Geen toegang tot contributie berekening.';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Get the boekjaar from POST data
    $boekjaarFromForm = null;
    $boekjaarParameterExists = false;
    if (isset($_POST['boekjaar'])) {
        $boekjaarParameterExists = true;
        $boekjaarFromForm = $_POST['boekjaar'];
    }
    
    // Check if boekjaar was provided
    $boekjaarIsEmpty = false;
    if ($boekjaarFromForm == null) {
        $boekjaarIsEmpty = true;
    }
    if ($boekjaarFromForm == '') {
        $boekjaarIsEmpty = true;
    }
    
    // If no boekjaar was selected, show error
    if ($boekjaarIsEmpty == true) {
        $_SESSION['message'] = 'Geen boekjaar geselecteerd.';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Clear previous session data before new calculation
    $previousContributiesExist = false;
    if (isset($_SESSION['berekende_contributies'])) {
        $previousContributiesExist = true;
        unset($_SESSION['berekende_contributies']);
    }
    
    $previousBoekjaarExists = false;
    if (isset($_SESSION['geselecteerd_boekjaar'])) {
        $previousBoekjaarExists = true;
        unset($_SESSION['geselecteerd_boekjaar']);
    }
    
    // Perform the contribution calculation
    $calculationResult = $contributieControllerObject->berekenContributies($boekjaarFromForm);
    
    // Check if calculation was successful
    $calculationWasSuccessful = false;
    if (isset($calculationResult['success'])) {
        if ($calculationResult['success'] == true) {
            $calculationWasSuccessful = true;
        }
    }
    
    // Handle successful calculation
    if ($calculationWasSuccessful == true) {
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
    if ($calculationWasSuccessful == false) {
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