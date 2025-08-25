<?php
/**
 * FamilieController.php - Main Controller for Family Management Operations
 * 
 * This controller serves as the central coordinator for all family-related operations
 * in the membership administration system. It delegates specific operations to 
 * specialized handler classes for better code organization and maintainability.
 * 
 * Responsibilities:
 * - Route family requests to appropriate handlers (Add/Edit/Delete/Get)
 * - Maintain consistent response format across all family operations
 * - Enforce business rules and validation
 * - Provide unified interface for family operations to the main application
 * 
 * Handlers:
 * - FamilieAddHandler: Creates new families
 * - FamilieEditHandler: Updates existing family information
 * - FamilieDeleteHandler: Removes families (with member validation)
 * - FamilieGetHandler: Retrieves family data for display/editing
 */

// Dependency Loading: Include all required models and handlers
require_once __DIR__ . '/../models/Familie.php';           // Family data model
require_once __DIR__ . '/../includes/utils.php';           // Utility functions (logging, etc.)
require_once __DIR__ . '/FamilieAddHandler.php';           // Family creation handler
require_once __DIR__ . '/FamilieEditHandler.php';          // Family editing handler
require_once __DIR__ . '/FamilieDeleteHandler.php';        // Family deletion handler
require_once __DIR__ . '/FamilieGetHandler.php';           // Family retrieval handler

/**
 * FamilieController - Central Family Operations Controller
 * 
 * Acts as a facade pattern implementation, providing a single interface
 * for all family operations while delegating to specialized handlers.
 */
class FamilieController {
    // Handler Objects: Store instances of specialized operation handlers
    public $familieModelObject;     // Direct access to Familie model
    public $addHandlerObject;       // Handles family creation requests
    public $editHandlerObject;      // Handles family editing requests  
    public $deleteHandlerObject;    // Handles family deletion requests
    public $getHandlerObject;       // Handles family data retrieval
    
    /**
     * Constructor - Initialize Controller with Database Connection
     * 
     * Creates all necessary handler objects and model instances,
     * passing the database connection to each for consistent data access.
     * 
     * @param PDO $databaseConnection Active database connection
     */
    public function __construct($databaseConnection) {
        // Model Initialization: Create Familie model for direct database operations
        $this->familieModelObject = new models\Familie($databaseConnection);
        
        // Handler Initialization: Create specialized handlers for each operation type
        $this->addHandlerObject = new FamilieAddHandler($databaseConnection);
        $this->editHandlerObject = new FamilieEditHandler($databaseConnection);
        $this->deleteHandlerObject = new FamilieDeleteHandler($databaseConnection);
        $this->getHandlerObject = new FamilieGetHandler($databaseConnection);
    }

    /**
     * handleRequest - Main Request Router for Family Operations
     * 
     * Validates the request method and routes POST requests to appropriate
     * handlers based on the action parameter. Ensures only POST requests
     * are processed for data modification operations.
     * 
     * @return array Response array with success status and message/data
     */
    public function handleRequest() {
        // Request Method Validation: Ensure only POST requests are processed
        $requestMethodIsPost = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $requestMethodIsPost = true;
        }
        
        // Invalid Method Handling: Reject non-POST requests
        if (!$requestMethodIsPost) {
            $invalidMethodErrorMessage = 'Invalid request method';
            $invalidMethodErrorArray = array();
            $invalidMethodErrorArray['success'] = false;
            $invalidMethodErrorArray['message'] = $invalidMethodErrorMessage;
            return $invalidMethodErrorArray;
        }

        // Get the action from POST data
        $actionFromPostData = '';
        if (isset($_POST['action'])) {
            $actionFromPostData = $_POST['action'];
        }

        // Check what action we need to perform and call the right handler
        $actionIsAddFamily = false;
        if ($actionFromPostData == 'add_family') {
            $actionIsAddFamily = true;
        }
        
        // If action is add family, use the add handler
        if ($actionIsAddFamily) {
            $addHandlerResult = $this->addHandlerObject->handleAddFamilieRequest();
            return $addHandlerResult;
        }
        
        // Check if action is edit family
        $actionIsEditFamily = false;
        if ($actionFromPostData == 'edit_family') {
            $actionIsEditFamily = true;
        }
        
        // If action is edit family, use the edit handler
        if ($actionIsEditFamily) {
            $editHandlerResult = $this->editHandlerObject->handleEditFamilieRequest();
            return $editHandlerResult;
        }
        
        // Check if action is delete family
        $actionIsDeleteFamily = false;
        if ($actionFromPostData == 'delete_family') {
            $actionIsDeleteFamily = true;
        }
        
        // If action is delete family, use the delete handler
        if ($actionIsDeleteFamily) {
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
