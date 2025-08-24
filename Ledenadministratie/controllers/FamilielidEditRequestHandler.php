<?php
// This class handles editing familielid requests
class FamilielidEditRequestHandler {
    public $familieControllerObject;
    public $familielidControllerObject;
    
    public function __construct($familieController, $familielidController) {
        $this->familieControllerObject = $familieController;
        $this->familielidControllerObject = $familielidController;
    }
    
    public function handleEditFamilielidFormRequest() {
        // Get familielid ID to edit
        $editFamilielidId = null;
        if (isset($_POST['edit_familielid_id'])) {
            $editFamilielidId = $_POST['edit_familielid_id'];
        }
        
        // Get familie ID
        $familieId = null;
        if (isset($_POST['edit_familie_id'])) {
            $familieId = $_POST['edit_familie_id'];
        }
        
        // Check if both IDs are provided
        $bothIdsProvided = false;
        if ($editFamilielidId != null && $familieId != null) {
            $bothIdsProvided = true;
        }
        
        if ($bothIdsProvided) {
            // Get familielid data
            $editFamilielidData = $this->familielidControllerObject->getFamilielidById($editFamilielidId);
            
            // Get familie data
            $editFamilieData = $this->familieControllerObject->getFamilieById($familieId);
            
            // Check if both data sets were retrieved
            $familielidDataExists = false;
            if ($editFamilielidData != null) {
                $familielidDataExists = true;
            }
            
            $familieDataExists = false;
            if ($editFamilieData != null) {
                $familieDataExists = true;
            }
            
            // If both data sets exist, return them
            if ($familielidDataExists && $familieDataExists) {
                return array(
                    'success' => true,
                    'edit_familielid' => $editFamilielidData,
                    'edit_familie' => $editFamilieData
                );
            } else {
                // If data retrieval failed
                $errorMessage = "Fout bij het ophalen van familielid gegevens.";
                $messageType = "error";
                $logMessage = "Failed to retrieve familielid or familie data";
                writeLog($logMessage);
                
                return array(
                    'success' => false,
                    'message' => $errorMessage,
                    'message_type' => $messageType
                );
            }
        } else {
            // If IDs are missing
            return array(
                'success' => false,
                'message' => "Missing familielid or familie ID",
                'message_type' => "error"
            );
        }
    }
    
    public function handleUpdateFamilielidRequest() {
        // Get familie ID from form
        $familieIdFromForm = '';
        if (isset($_POST['familie_id'])) {
            $familieIdFromForm = $_POST['familie_id'];
        }
        
        // Get familielid ID from form
        $familielidIdFromForm = '';
        if (isset($_POST['familielid_id'])) {
            $familielidIdFromForm = $_POST['familielid_id'];
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
        
        // Try to update the familielid
        $updateResult = $this->familielidControllerObject->updateFamilielid(
            $familielidIdFromForm,
            $familielidNaamFromForm,
            $familielidGeboortedatumFromForm,
            $soortFamilielidFromForm,
            $stallingFromForm
        );
        
        // Check if update was successful
        $updateWasSuccessful = false;
        if ($updateResult) {
            $updateWasSuccessful = true;
        }
        
        // Set appropriate message and reload familie
        if ($updateWasSuccessful) {
            $successMessage = "Familielid succesvol bijgewerkt.";
            $messageType = "success";
            
            // Reload familie for edit screen
            $editFamilie = $this->familieControllerObject->getFamilieById($familieIdFromForm);
            
            return array(
                'success' => true,
                'message' => $successMessage,
                'message_type' => $messageType,
                'edit_familie' => $editFamilie,
                'clear_edit_familielid' => true
            );
        } else {
            $errorMessage = "Fout bij het bijwerken van het familielid.";
            $messageType = "error";
            
            return array(
                'success' => false,
                'message' => $errorMessage,
                'message_type' => $messageType
            );
        }
    }
    
    public function handleCancelEditFamilielidRequest() {
        // Get familie ID from form
        $familieIdFromForm = '';
        if (isset($_POST['familie_id'])) {
            $familieIdFromForm = $_POST['familie_id'];
        }
        
        // Reload familie for edit screen
        $editFamilie = $this->familieControllerObject->getFamilieById($familieIdFromForm);
        
        return array(
            'success' => true,
            'edit_familie' => $editFamilie,
            'clear_edit_familielid' => true
        );
    }
}
