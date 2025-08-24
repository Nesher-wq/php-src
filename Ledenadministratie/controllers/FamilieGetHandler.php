<?php
// Include the Familie model so we can get families from the database
require_once __DIR__ . '/../models/Familie.php';
require_once __DIR__ . '/../includes/utils.php';

// This class handles getting families from the database
class FamilieGetHandler {
    // This variable stores our Familie model object
    public $familieModelObject;
    
    // Constructor function that runs when we create a new FamilieGetHandler
    public function __construct($databaseConnection) {
        // Create a new Familie model object and store it
        $this->familieModelObject = new models\Familie($databaseConnection);
    }
    
    // This function gets a specific familie by its ID
    public function getFamilieById($familieIdParameter) {
        // First check if we have a valid ID
        $familieIdIsEmpty = false;
        $familieIdIsNull = false;
        
        // Check if the familie ID is empty
        if ($familieIdParameter == '') {
            $familieIdIsEmpty = true;
        }
        
        // Check if the familie ID is null
        if ($familieIdParameter == null) {
            $familieIdIsNull = true;
        }
        
        // If the ID is empty or null, we can't get the familie
        $familieIdIsInvalid = false;
        if ($familieIdIsEmpty) {
            $familieIdIsInvalid = true;
        }
        if ($familieIdIsNull) {
            $familieIdIsInvalid = true;
        }
        
        // Return error if ID is invalid
        if ($familieIdIsInvalid) {
            $noIdErrorMessage = "No valid familie ID provided";
            $noIdErrorArray = array();
            $noIdErrorArray['success'] = false;
            $noIdErrorArray['message'] = $noIdErrorMessage;
            return $noIdErrorArray;
        }
        
        // Try to get the familie data from the database
        $databaseErrorOccurred = false;
        $familieDataFromDatabase = null;
        
        try {
            // Try to get the familie data from our model
            $familieDataFromDatabase = $this->familieModelObject->getFamilieById($familieIdParameter);
        } catch (PDOException $exceptionObject) {
            // If there was a database error, mark it as occurred
            $databaseErrorOccurred = true;
            
            // Log the error message
            $errorLogMessage = "Error in getFamilieById: " . $exceptionObject->getMessage();
            writeLog($errorLogMessage);
        }
        
        // If a database error occurred, return error
        if ($databaseErrorOccurred) {
            $databaseErrorMessage = "Database error while getting familie";
            $databaseErrorArray = array();
            $databaseErrorArray['success'] = false;
            $databaseErrorArray['message'] = $databaseErrorMessage;
            return $databaseErrorArray;
        }
        
        // Check if we actually got data back from the database
        $familieDataIsNull = false;
        if ($familieDataFromDatabase == null) {
            $familieDataIsNull = true;
        }
        
        // If we got data, return it
        if (!$familieDataIsNull) {
            return $familieDataFromDatabase;
        }
        
        // If we get here, the familie was not found
        $familieNotFoundMessage = "Familie not found with ID: " . $familieIdParameter;
        $familieNotFoundArray = array();
        $familieNotFoundArray['success'] = false;
        $familieNotFoundArray['message'] = $familieNotFoundMessage;
        return $familieNotFoundArray;
    }
}
