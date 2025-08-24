<?php
// Check if this file is being included from the main index file
$accessIsAllowed = false;
if (defined('INCLUDED_FROM_INDEX')) {
    $accessIsAllowed = true;
}

// If direct access is attempted, deny it
if (!$accessIsAllowed) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

// Access global PDO connection variable
global $pdo;

// Include utility functions
require_once __DIR__ . '/../includes/utils.php';

// Check if this is a POST request
$requestMethodIsPost = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestMethodIsPost = true;
}

// Only continue if this is a POST request
if (!$requestMethodIsPost) {
    return;
}

// Include required controllers and handlers
require_once __DIR__ . '/FamilieController.php';
require_once __DIR__ . '/FamilielidController.php';
require_once __DIR__ . '/FamilielidAddRequestHandler.php';
require_once __DIR__ . '/FamilielidEditRequestHandler.php';
require_once __DIR__ . '/FamilielidDeleteHandler.php';

// Create controller objects
$familieControllerObject = new FamilieController($pdo);
$familielidControllerObject = new FamilielidController($pdo);

// Create handler objects
$addRequestHandlerObject = new FamilielidAddRequestHandler($familieControllerObject, $familielidControllerObject);
$editRequestHandlerObject = new FamilielidEditRequestHandler($familieControllerObject, $familielidControllerObject);
$deleteRequestHandlerObject = new FamilielidDeleteHandler($pdo, $familieControllerObject, $familielidControllerObject);

// Initialize result variables
$handlerResult = null;
$message = '';
$message_type = '';
$edit_familie = null;
$edit_familielid = null;

// Check if add familielid button was clicked
$addFamilielidButtonClicked = false;
if (isset($_POST['add_familielid'])) {
    $addFamilielidButtonClicked = true;
}

// Handle add familielid request
if ($addFamilielidButtonClicked) {
    $handlerResult = $addRequestHandlerObject->handleAddFamilielidRequest();
    
    // Extract results from handler
    if (isset($handlerResult['message'])) {
        $message = $handlerResult['message'];
    }
    if (isset($handlerResult['message_type'])) {
        $message_type = $handlerResult['message_type'];
    }
    if (isset($handlerResult['edit_familie'])) {
        $edit_familie = $handlerResult['edit_familie'];
    }
}

// Check if edit familielid button was clicked
$editFamilielidButtonClicked = false;
if (isset($_POST['edit_familielid'])) {
    $editFamilielidButtonClicked = true;
}

// Handle edit familielid form request
if ($editFamilielidButtonClicked) {
    $handlerResult = $editRequestHandlerObject->handleEditFamilielidFormRequest();
    
    // Extract results from handler
    if (isset($handlerResult['message'])) {
        $message = $handlerResult['message'];
    }
    if (isset($handlerResult['message_type'])) {
        $message_type = $handlerResult['message_type'];
    }
    if (isset($handlerResult['edit_familie'])) {
        $edit_familie = $handlerResult['edit_familie'];
    }
    if (isset($handlerResult['edit_familielid'])) {
        $edit_familielid = $handlerResult['edit_familielid'];
    }
}

// Check if update familielid button was clicked
$updateFamilielidButtonClicked = false;
if (isset($_POST['update_familielid'])) {
    $updateFamilielidButtonClicked = true;
}

// Handle update familielid request
if ($updateFamilielidButtonClicked) {
    $handlerResult = $editRequestHandlerObject->handleUpdateFamilielidRequest();
    
    // Extract results from handler
    if (isset($handlerResult['message'])) {
        $message = $handlerResult['message'];
    }
    if (isset($handlerResult['message_type'])) {
        $message_type = $handlerResult['message_type'];
    }
    if (isset($handlerResult['edit_familie'])) {
        $edit_familie = $handlerResult['edit_familie'];
    }
    
    // Clear edit familielid if requested
    if (isset($handlerResult['clear_edit_familielid']) && $handlerResult['clear_edit_familielid']) {
        unset($edit_familielid);
    }
}

// Check if cancel edit familielid button was clicked
$cancelEditFamilielidButtonClicked = false;
if (isset($_POST['cancel_edit_familielid'])) {
    $cancelEditFamilielidButtonClicked = true;
}

// Handle cancel edit familielid request
if ($cancelEditFamilielidButtonClicked) {
    $handlerResult = $editRequestHandlerObject->handleCancelEditFamilielidRequest();
    
    // Extract results from handler
    if (isset($handlerResult['edit_familie'])) {
        $edit_familie = $handlerResult['edit_familie'];
    }
    
    // Clear edit familielid
    if (isset($handlerResult['clear_edit_familielid']) && $handlerResult['clear_edit_familielid']) {
        unset($edit_familielid);
    }
}

// Check if delete familielid button was clicked
$deleteFamilielidButtonClicked = false;
if (isset($_POST['delete_familielid'])) {
    $deleteFamilielidButtonClicked = true;
}

// Handle delete familielid request
if ($deleteFamilielidButtonClicked) {
    $handlerResult = $deleteRequestHandlerObject->handleDeleteFamilielidRequest();
    
    // Extract results from handler
    if (isset($handlerResult['message'])) {
        $message = $handlerResult['message'];
    }
    if (isset($handlerResult['message_type'])) {
        $message_type = $handlerResult['message_type'];
    }
    if (isset($handlerResult['edit_familie'])) {
        $edit_familie = $handlerResult['edit_familie'];
    }
}
?>
