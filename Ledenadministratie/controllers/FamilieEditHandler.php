<?php
// Include the Familie model so we can edit families in the database
require_once __DIR__ . '/../models/Familie.php';
require_once __DIR__ . '/../includes/utils.php';

// This class handles editing families in the database
class FamilieEditHandler {
    // This variable stores our Familie model object
    public $familieModelObject;
    
    // Constructor function that runs when we create a new FamilieEditHandler
    public function __construct($databaseConnection) {
        // Create a new Familie model object and store it
        $this->familieModelObject = new models\Familie($databaseConnection);
    }
    
    // This is the main function that handles edit requests for families
    public function handleEditFamilieRequest() {
        // First check if someone wants to get familie data for editing
        $editButtonWasClicked = false;
        if (isset($_POST['edit_familie'])) {
            $editButtonWasClicked = true;
        }
        
        // If edit button was clicked, get the familie data
        if ($editButtonWasClicked) {
            $editResult = $this->getFamilieDataForEditing();
            return $editResult;
        }
        
        // Check if someone wants to update an existing familie
        $updateButtonWasClicked = false;
        if (isset($_POST['update_familie'])) {
            $updateButtonWasClicked = true;
        }
        
        // If update button was clicked, update the familie
        if ($updateButtonWasClicked) {
            $updateResult = $this->updateExistingFamilieInDatabase();
            return $updateResult;
        }
        
        // If we get here, something went wrong with the request
        $errorMessageText = "Invalid edit familie request";
        $errorResultArray = array();
        $errorResultArray['success'] = false;
        $errorResultArray['message'] = $errorMessageText;
        return $errorResultArray;
    }
    
    // This function gets familie data for editing the form
    public function getFamilieDataForEditing() {
        // First we need to get the ID of the familie we want to edit
        $familieIdToEdit = $_POST['edit_familie_id'] ?? '';
        
        // Check if we actually found a valid familie ID
        $familieIdIsEmpty = false;
        if ($familieIdToEdit == '') {
            $familieIdIsEmpty = true;
        }
        
        // If we have a valid ID, get the familie data
        if (!$familieIdIsEmpty) {
            // Try to get the familie data from the database
            $familieDataFromDatabase = $this->getFamilieById($familieIdToEdit);
            
            // Check if we actually got familie data
            if ($familieDataFromDatabase !== null) {
                // Create the return array with the familie data
                $successResultArray = array();
                $successResultArray['success'] = true;
                $successResultArray['familie'] = $familieDataFromDatabase;
                return $successResultArray;
            } else {
                // Familie not found or database error
                $notFoundErrorMessage = "Familie not found or database error";
                $notFoundErrorArray = array();
                $notFoundErrorArray['success'] = false;
                $notFoundErrorArray['message'] = $notFoundErrorMessage;
                return $notFoundErrorArray;
            }
        }
        
        // If we get here, no valid ID was provided
        $noIdErrorMessage = "No valid familie ID provided for editing";
        $noIdErrorArray = array();
        $noIdErrorArray['success'] = false;
        $noIdErrorArray['message'] = $noIdErrorMessage;
        return $noIdErrorArray;
    }
    
    // This function updates an existing familie in the database
    public function updateExistingFamilieInDatabase() {
        // First we need to get all the form data for updating
        // Initialize all variables with empty strings
        $familieIdToUpdate = '';
        $updatedFamilieNaamFromForm = '';
        $updatedFamiliestraatFromForm = '';
        $updatedFamilieHuisnummerFromForm = '';
        // Get form data for update
        $familieIdToUpdate = $_POST['familie_id'] ?? '';
        $updatedFamilieNaamFromForm = $_POST['familie_naam'] ?? '';
        $updatedFamiliestraatFromForm = $_POST['familie_straat'] ?? '';
        $updatedFamilieHuisnummerFromForm = $_POST['familie_huisnummer'] ?? '';
        $updatedFamiliePostcodeFromForm = $_POST['familie_postcode'] ?? '';
        $updatedFamilieWoonplaatsFromForm = $_POST['familie_woonplaats'] ?? '';
        
        // Now try to update the familie using our model
        $updateOperationResult = $this->familieModelObject->update(
            $familieIdToUpdate,
            $updatedFamilieNaamFromForm,
            $updatedFamiliestraatFromForm,
            $updatedFamilieHuisnummerFromForm,
            $updatedFamiliePostcodeFromForm,
            $updatedFamilieWoonplaatsFromForm
        );
        
        // Check if the update operation was successful
        $updateWasSuccessful = false;
        if ($updateOperationResult) {
            $updateWasSuccessful = true;
        }
        
        // If update was successful, return success message
        if ($updateWasSuccessful) {
            $successMessageText = "Familie succesvol bijgewerkt.";
            $successResultArray = array();
            $successResultArray['success'] = true;
            $successResultArray['message'] = $successMessageText;
            return $successResultArray;
        }
        
        // If we get here, the update failed
        $updateFailedErrorMessage = "Fout bij het bijwerken van de familie.";
        $updateFailedErrorArray = array();
        $updateFailedErrorArray['success'] = false;
        $updateFailedErrorArray['message'] = $updateFailedErrorMessage;
        return $updateFailedErrorArray;
    }
    
    // This function gets a specific familie by its ID
    public function getFamilieById($familieIdParameter) {
        // We use a try-catch block to handle any database errors
        $databaseErrorOccurred = false;
        $familieDataResult = null;
        
        try {
            // Try to get the familie data from the model
            $familieDataResult = $this->familieModelObject->getFamilieById($familieIdParameter);
        } catch (PDOException $exceptionObject) {
            // If there was a database error, log it
            $databaseErrorOccurred = true;
            $errorLogMessage = "Error in getFamilieById: " . $exceptionObject->getMessage();
            writeLog($errorLogMessage);
        }
        
        // If an error occurred, return null
        if ($databaseErrorOccurred) {
            return null;
        }
        
        // Return the familie data
        return $familieDataResult;
    }
}
