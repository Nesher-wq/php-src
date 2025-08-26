<?php
// Include the Familie model so we can use it to delete families
require_once __DIR__ . '/../models/Familie.php';
require_once __DIR__ . '/../includes/utils.php';

// This class handles deleting families from the database
class FamilieDeleteHandler {
    // This variable stores our Familie model object
    public $familieModelObject;
    
    // Constructor function that runs when we create a new FamilieDeleteHandler
    public function __construct($databaseConnection) {
        // Create a new Familie model object and store it
        $this->familieModelObject = new models\Familie($databaseConnection);
    }
    
    // This is the main function that handles delete requests for families
    public function handleDeleteFamilieRequest() {
        // First we check if someone clicked the delete familie button
        $deleteButtonWasClicked = false;
        if (isset($_POST['delete_familie'])) {
            $deleteButtonWasClicked = true;
        }
        
        // If the delete button was clicked, we try to delete the familie
        if ($deleteButtonWasClicked) {
            $deleteResult = $this->deleteFamilieFromDatabase();
            return $deleteResult;
        }
        
        // If we get here, something went wrong with the request
        $errorMessageText = "Invalid delete familie request";
        $errorResultArray = array();
        $errorResultArray['success'] = false;
        $errorResultArray['message'] = $errorMessageText;
        return $errorResultArray;
    }
    
    // This function actually deletes the familie from the database
    public function deleteFamilieFromDatabase() {
        // Get the ID of the familie we want to delete
        $familieIdToDelete = $_POST['delete_familie_id'] ?? $_POST['familie_id'] ?? '';
        $familieIdFound = !empty($familieIdToDelete);
        
        // Check if we actually found a valid familie ID
        $familieIdIsEmpty = false;
        if ($familieIdToDelete == '') {
            $familieIdIsEmpty = true;
        }
        
        // If no familie ID was provided, we can't delete anything
        if ($familieIdIsEmpty) {
            $noIdErrorMessage = "Geen familie ID opgegeven voor verwijdering.";
            $noIdErrorArray = array();
            $noIdErrorArray['success'] = false;
            $noIdErrorArray['message'] = $noIdErrorMessage;
            return $noIdErrorArray;
        }
        
        // Now we try to delete the familie using our model
        $deleteOperationResult = $this->familieModelObject->delete($familieIdToDelete);
        
        // Check if the delete operation was successful
        // The model now returns an array with success status and message
        if (is_array($deleteOperationResult)) {
            // New structured response from model
            $deleteResultArray = array();
            $deleteResultArray['success'] = $deleteOperationResult['success'];
            $deleteResultArray['message'] = $deleteOperationResult['message'];
            return $deleteResultArray;
        } else {
            // Fallback for old boolean response (backwards compatibility)
            $deleteWasSuccessful = false;
            if ($deleteOperationResult) {
                $deleteWasSuccessful = true;
            }
            
            // If delete was successful, return success message
            if ($deleteWasSuccessful) {
                $successMessageText = "Familie succesvol verwijderd.";
                $successResultArray = array();
                $successResultArray['success'] = true;
                $successResultArray['message'] = $successMessageText;
                return $successResultArray;
            }
            
            // If we get here, the delete failed
            $deleteFailedErrorMessage = "Fout bij het verwijderen van de familie.";
            $deleteFailedErrorArray = array();
            $deleteFailedErrorArray['success'] = false;
            $deleteFailedErrorArray['message'] = $deleteFailedErrorMessage;
            return $deleteFailedErrorArray;
        }
    }
}
