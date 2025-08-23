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
        // Get the familie ID from the form
        $familieIdFromForm = '';
        if (isset($_POST['familie_id'])) {
            $familieIdFromForm = $_POST['familie_id'];
        }
        
        // Get familielid naam from form
        $familielidNaamFromForm = '';
        if (isset($_POST['familielid_naam'])) {
            $familielidNaamFromForm = $_POST['familielid_naam'];
        }
        
        // Get familielid geboortedatum from form
        $familielidGeboortedatumFromForm = '';
        if (isset($_POST['familielid_geboortedatum'])) {
            $familielidGeboortedatumFromForm = $_POST['familielid_geboortedatum'];
        }
        
        // Get soort familielid from form
        $soortFamilielidFromForm = '';
        if (isset($_POST['soort_familielid'])) {
            $soortFamilielidFromForm = $_POST['soort_familielid'];
        }
        
        // Get stalling from form
        $stallingFromForm = 0;
        if (isset($_POST['stalling'])) {
            $stallingFromForm = $_POST['stalling'];
        }
        
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
        if ($createResult == true) {
            $createWasSuccessful = true;
        }
        
        // Set appropriate message
        if ($createWasSuccessful == true) {
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
