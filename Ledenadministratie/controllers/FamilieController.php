<?php
// Include all the Familie models and handlers we need
require_once __DIR__ . '/../models/Familie.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/FamilieAddHandler.php';
require_once __DIR__ . '/FamilieEditHandler.php';
require_once __DIR__ . '/FamilieDeleteHandler.php';
require_once __DIR__ . '/FamilieGetHandler.php';

use models\Familie;

// This class is the main controller for handling Familie requests
class FamilieController {
    // These variables store our Familie model and handler objects
    public $familieModelObject;
    public $addHandlerObject;
    public $editHandlerObject;
    public $deleteHandlerObject;
    public $getHandlerObject;
    
    // Constructor function that runs when we create a new FamilieController
    public function __construct($databaseConnection) {
        // Create a new Familie model object and store it
        $this->familieModelObject = new Familie($databaseConnection);
        
        // Create all the handler objects we need for different operations
        $this->addHandlerObject = new FamilieAddHandler($databaseConnection);
        $this->editHandlerObject = new FamilieEditHandler($databaseConnection);
        $this->deleteHandlerObject = new FamilieDeleteHandler($databaseConnection);
        $this->getHandlerObject = new FamilieGetHandler($databaseConnection);
    }

    // This is the main function that handles all Familie requests
    public function handleRequest() {
        // First check if the request method is POST
        $requestMethodIsPost = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $requestMethodIsPost = true;
        }
        
        // If request method is not POST, return error
        if ($requestMethodIsPost == false) {
            $invalidMethodErrorMessage = 'Invalid request method';
            $invalidMethodErrorArray = array();
            $invalidMethodErrorArray['success'] = false;
            $invalidMethodErrorArray['message'] = $invalidMethodErrorMessage;
            return $invalidMethodErrorArray;
        }

        // Get the action from POST data
        $actionFromPostData = '';
        $actionExists = false;
        if (isset($_POST['action'])) {
            $actionExists = true;
            $actionFromPostData = $_POST['action'];
        }

        // Check what action we need to perform and call the right handler
        $actionIsAddFamily = false;
        if ($actionFromPostData == 'add_family') {
            $actionIsAddFamily = true;
        }
        
        // If action is add family, use the add handler
        if ($actionIsAddFamily == true) {
            $addHandlerResult = $this->addHandlerObject->handleAddFamilieRequest();
            return $addHandlerResult;
        }
        
        // Check if action is edit family
        $actionIsEditFamily = false;
        if ($actionFromPostData == 'edit_family') {
            $actionIsEditFamily = true;
        }
        
        // If action is edit family, use the edit handler
        if ($actionIsEditFamily == true) {
            $editHandlerResult = $this->editHandlerObject->handleEditFamilieRequest();
            return $editHandlerResult;
        }
        
        // Check if action is delete family
        $actionIsDeleteFamily = false;
        if ($actionFromPostData == 'delete_family') {
            $actionIsDeleteFamily = true;
        }
        
        // If action is delete family, use the delete handler
        if ($actionIsDeleteFamily == true) {
            $deleteHandlerResult = $this->deleteHandlerObject->handleDeleteFamilieRequest();
            return $deleteHandlerResult;
        }

        // If we get here, no valid action was found
        $noValidActionErrorArray = array();
        $noValidActionErrorArray['success'] = false;
        return $noValidActionErrorArray;
    }

    // This function gets a specific familie by its ID
    public function getFamilieById($familieIdParameter) {
        // Use our get handler to get the familie data
        $getHandlerResult = $this->getHandlerObject->getFamilieById($familieIdParameter);
        return $getHandlerResult;
    }
}
