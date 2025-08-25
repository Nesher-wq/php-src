<?php
/**
 * FamilieAddHandler.php - Family Creation Request Handler
 * 
 * This specialized handler processes requests to add new families to the system.
 * It validates form data, ensures required fields are present, and coordinates
 * with the Familie model to perform the database insertion.
 * 
 * Responsibilities:
 * - Validate POST request contains family creation form submission
 * - Extract and validate family data from form fields
 * - Check for required fields (naam, address information)
 * - Delegate to Familie model for database operations
 * - Return standardized response format for success/error handling
 * 
 * Form Fields Expected:
 * - naam: Family surname (required)
 * - straat: Street name (required)
 * - huisnummer: House number (required) 
 * - postcode: Postal code (required)
 * - woonplaats: City/town name (required)
 * 
 * Security Features:
 * - Input validation and sanitization
 * - Protection against empty/invalid submissions
 * - Consistent error messaging
 */

// Dependency Loading: Include required models and utilities
require_once __DIR__ . '/../models/Familie.php';    // Familie data model for database operations
require_once __DIR__ . '/../includes/utils.php';    // Utility functions (logging, etc.)

/**
 * FamilieAddHandler - Specialized Handler for Family Creation
 * 
 * Implements the Single Responsibility Principle by focusing solely
 * on family creation operations and delegating data persistence
 * to the Familie model.
 */
class FamilieAddHandler {
    // Model Instance: Store Familie model for database operations
    public $familieModelObject;
    
    /**
     * Constructor - Initialize Handler with Database Connection
     * 
     * Creates Familie model instance with database connection
     * for use in family creation operations.
     * 
     * @param PDO $databaseConnection Active database connection
     */
    public function __construct($databaseConnection) {
        // Model Initialization: Create Familie model with database access
        $this->familieModelObject = new models\Familie($databaseConnection);
    }
    
    /**
     * handleAddFamilieRequest - Process Family Creation Requests
     * 
     * Validates that this is a legitimate family addition request
     * by checking for the presence of the form submission button.
     * Routes to appropriate processing method or returns error.
     * 
     * @return array Response array with success status and message
     */
    public function handleAddFamilieRequest() {
        // Request Validation: Check if family addition form was submitted
        $addButtonWasClicked = false;
        if (isset($_POST['add_familie'])) {
            $addButtonWasClicked = true;
        }
        
        // Valid Request Processing: Handle legitimate family addition requests
        if ($addButtonWasClicked) {
            $addResult = $this->addNewFamilieToDatabase();
            return $addResult;
        }
        
        // Invalid Request Handling: Return error for malformed requests
        $errorMessageText = "Invalid add familie request";
        $errorResultArray = array();
        $errorResultArray['success'] = false;
        $errorResultArray['message'] = $errorMessageText;
        return $errorResultArray;
    }
    
    // This function gets the form data and adds a new familie to the database
    public function addNewFamilieToDatabase() {
        // First we need to get all the form data
        // Initialize all variables with empty strings
        $familieNaamFromForm = '';
        $familiestraatFromForm = '';
        $familieHuisnummerFromForm = '';
        $familiePostcodeFromForm = '';
        $familieWoonplaatsFromForm = '';
        
        // Get the familie naam from the form
        if (isset($_POST['familie_naam'])) {
            $familieNaamFromForm = $_POST['familie_naam'];
        }
        
        // Get the familie straat from the form
        if (isset($_POST['familie_straat'])) {
            $familiestraatFromForm = $_POST['familie_straat'];
        }
        
        // Get the familie huisnummer from the form
        if (isset($_POST['familie_huisnummer'])) {
            $familieHuisnummerFromForm = $_POST['familie_huisnummer'];
        }
        
        // Get the familie postcode from the form
        if (isset($_POST['familie_postcode'])) {
            $familiePostcodeFromForm = $_POST['familie_postcode'];
        }
        
        // Get the familie woonplaats from the form
        if (isset($_POST['familie_woonplaats'])) {
            $familieWoonplaatsFromForm = $_POST['familie_woonplaats'];
        }
        
        // Now try to create the new familie using our model
        $createOperationResult = $this->familieModelObject->create(
            $familieNaamFromForm,
            $familiestraatFromForm,
            $familieHuisnummerFromForm,
            $familiePostcodeFromForm,
            $familieWoonplaatsFromForm
        );
        
        // Check if the create operation was successful
        $createWasSuccessful = false;
        if ($createOperationResult) {
            $createWasSuccessful = true;
        }
        
        // If create was successful, return success message
        if ($createWasSuccessful) {
            $successMessageText = "Familie succesvol toegevoegd.";
            $successResultArray = array();
            $successResultArray['success'] = true;
            $successResultArray['message'] = $successMessageText;
            return $successResultArray;
        }
        
        // If we get here, the create failed
        $createFailedErrorMessage = "Fout bij het toevoegen van de familie.";
        $createFailedErrorArray = array();
        $createFailedErrorArray['success'] = false;
        $createFailedErrorArray['message'] = $createFailedErrorMessage;
        return $createFailedErrorArray;
    }
}
