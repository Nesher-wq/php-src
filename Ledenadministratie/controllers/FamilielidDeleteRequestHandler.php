<?php
// This class handles deleting familielid requests
class FamilielidDeleteRequestHandler {
    public $familieControllerObject;
    public $familielidControllerObject;
    
    public function __construct($familieController, $familielidController) {
        $this->familieControllerObject = $familieController;
        $this->familielidControllerObject = $familielidController;
    }
    
    public function handleDeleteFamilielidRequest() {
        // Get familie ID from form
        $familieIdFromForm = '';
        if (isset($_POST['familie_id'])) {
            $familieIdFromForm = $_POST['familie_id'];
        }
        
        // Get familielid ID to delete
        $deleteFamilielidId = '';
        if (isset($_POST['delete_familielid_id'])) {
            $deleteFamilielidId = $_POST['delete_familielid_id'];
        }
        
        // Try to delete the familielid
        $deleteResult = $this->familielidControllerObject->deleteFamilielid($deleteFamilielidId);
        
        // Check if deletion was successful
        $deleteWasSuccessful = false;
        if ($deleteResult == true) {
            $deleteWasSuccessful = true;
        }
        
        // Set appropriate message and reload familie
        if ($deleteWasSuccessful == true) {
            $successMessage = "Familielid succesvol verwijderd.";
            $messageType = "success";
            
            // Reload familie for edit screen
            $editFamilie = $this->familieControllerObject->getFamilieById($familieIdFromForm);
            
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
