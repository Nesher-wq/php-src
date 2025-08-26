<?php

// This class handles deleting familieleden from the database
class FamilielidDeleteHandler {
    // These variables store our database connection and controller objects
    public $databaseConnection;
    public $familieControllerObject;
    public $familielidControllerObject;
    
    // Constructor function that runs when we create a new FamilielidDeleteHandler
    public function __construct($databaseConnectionParameter, $familieController = null, $familielidController = null) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
        
        // Store controller objects if provided (for request handling)
        if ($familieController !== null) {
            $this->familieControllerObject = $familieController;
        }
        if ($familielidController !== null) {
            $this->familielidControllerObject = $familielidController;
        }
    }

    // This function deletes a familielid from the database (simple version for controller use)
    public function deleteFamilielid($familielidIdParameter) {
        // Use the private method to perform the actual delete
        $deleteWasSuccessful = $this->executeDatabaseDelete($familielidIdParameter);
        
        // Return whether the delete was successful
        return $deleteWasSuccessful;
    }
    
    // Private method that performs the actual database delete operation
    private function executeDatabaseDelete($familielidIdParameter) {
        // Prepare the delete statement
        $deleteFamilielidStatement = $this->databaseConnection->prepare("DELETE FROM familielid WHERE id = ?");
        
        // Set up the parameters for the delete
        $deleteParameters = array($familielidIdParameter);
        
        // Execute the delete statement
        $deleteResult = $deleteFamilielidStatement->execute($deleteParameters);
        
        // Check if any rows were affected
        $rowsAffected = $deleteFamilielidStatement->rowCount();
        $deleteWasSuccessful = false;
        if ($rowsAffected > 0) {
            $deleteWasSuccessful = true;
        }
        
        // Return whether the delete was successful
        return $deleteWasSuccessful;
    }
    
    // This function handles full delete requests (from forms)
    public function handleDeleteFamilielidRequest() {
        // Get form data
        $familieIdFromForm = $_POST['familie_id'] ?? '';
        $deleteFamilielidId = $_POST['delete_familielid_id'] ?? '';
        
        // Try to delete the familielid using our private method
        $deleteWasSuccessful = $this->executeDatabaseDelete($deleteFamilielidId);
        
        // Set appropriate message and reload familie
        if ($deleteWasSuccessful) {
            $successMessage = "Familielid succesvol verwijderd.";
            $messageType = "success";
            
            // Reload familie for edit screen (if controllers are available)
            $editFamilie = null;
            if ($this->familieControllerObject !== null) {
                $editFamilie = $this->familieControllerObject->getFamilieById($familieIdFromForm);
            }
            
            return array(
                'success' => true,
                'message' => $successMessage,
                'message_type' => $messageType,
                'edit_familie' => $editFamilie
            );
        } else {
            $errorMessage = "Fout bij het verwijderen van het familielid.";
            $messageType = "error";
            
            return array(
                'success' => false,
                'message' => $errorMessage,
                'message_type' => $messageType
            );
        }
    }
}
