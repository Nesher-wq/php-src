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
        // Get form data for edit request
        $editFamilielidId = $_POST['edit_familielid_id'] ?? null;
        $familieId = $_POST['edit_familie_id'] ?? null;
        
        // Check if both IDs are provided
        $bothIdsProvided = ($editFamilielidId !== null && $familieId !== null);
        
        if ($bothIdsProvided) {
            // Get familielid data
            $editFamilielidData = $this->familielidControllerObject->getFamilielidById($editFamilielidId);
            
            // Get familie data
            $editFamilieData = $this->familieControllerObject->getFamilieById($familieId);
            
            // Check if both data sets were retrieved successfully
            $familielidDataExists = ($editFamilielidData !== null);
            $familieDataExists = ($editFamilieData !== null);
            
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
        // Get form data for update
        $familieIdFromForm = $_POST['familie_id'] ?? '';
        $familielidIdFromForm = $_POST['familielid_id'] ?? '';
        $familielidNaamFromForm = $_POST['familielid_naam'] ?? '';
        $familielidGeboortedatumFromForm = $_POST['familielid_geboortedatum'] ?? '';
        $soortFamilielidFromForm = $_POST['soort_familielid'] ?? '';
        $stallingFromForm = $_POST['stalling'] ?? 0;
        
        // Try to update the familielid
        $updateResult = $this->familielidControllerObject->updateFamilielid(
            $familielidIdFromForm,
            $familielidNaamFromForm,
            $familielidGeboortedatumFromForm,
            $soortFamilielidFromForm,
            $stallingFromForm
        );
        
        // Check if update was successful
        $updateWasSuccessful = (bool)$updateResult;
        
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
        $familieIdFromForm = $_POST['familie_id'] ?? '';
        
        // Reload familie for edit screen
        $editFamilie = $this->familieControllerObject->getFamilieById($familieIdFromForm);
        
        return array(
            'success' => true,
            'edit_familie' => $editFamilie,
            'clear_edit_familielid' => true
        );
    }
}
