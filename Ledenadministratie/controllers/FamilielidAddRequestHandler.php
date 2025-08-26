<?php
// This class handles adding familielid requests
class FamilielidAddRequestHandler {
    public $familieControllerObject;
    public $familielidControllerObject;
    
    public function __construct($familieController, $familielidController) {
        $this->familieControllerObject = $familieController;
        $this->familielidControllerObject = $familielidController;
    }
    
    public function handleAddFamilielidRequest() {
        // Get form data
        $familieIdFromForm = $_POST['familie_id'] ?? '';
        $familielidNaamFromForm = $_POST['familielid_naam'] ?? '';
        $familielidGeboortedatumFromForm = $_POST['familielid_geboortedatum'] ?? '';
        $soortFamilielidFromForm = $_POST['soort_familielid'] ?? '';
        $stallingFromForm = $_POST['stalling'] ?? 0;
        
        // Try to create the familielid
        $createResult = $this->familielidControllerObject->createFamilielid(
            $familieIdFromForm,
            $familielidNaamFromForm,
            $familielidGeboortedatumFromForm,
            $soortFamilielidFromForm,
            $stallingFromForm
        );
        
        // Check if creation was successful
        $createWasSuccessful = false;
        if ($createResult) {
            $createWasSuccessful = true;
        }
        
        // Set appropriate message
        if ($createWasSuccessful) {
            $successMessage = "Familielid succesvol toegevoegd.";
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
            $errorMessage = "Fout bij het toevoegen van het familielid.";
            $messageType = "error";
            
            return array(
                'success' => false,
                'message' => $errorMessage,
                'message_type' => $messageType
            );
        }
    }
}
