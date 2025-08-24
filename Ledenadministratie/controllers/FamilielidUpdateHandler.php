<?php

// This class handles updating familieleden in the database
class FamilielidUpdateHandler {
    // This variable stores our database connection
    public $databaseConnection;
    
    // Constructor function that runs when we create a new FamilielidUpdateHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function updates an existing familielid in the database
    public function updateFamilielid($familielidIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter = 0, $soortLidIdParameter = null) {
        // First determine the soort_lid_id if not provided
        $soortLidIdToUse = $soortLidIdParameter;
        $soortLidIdWasProvided = false;
        if ($soortLidIdParameter !== null) {
            $soortLidIdWasProvided = true;
        }
        
        // If soort_lid_id was not provided, determine automatically
        if (!$soortLidIdWasProvided) {
            $soortLidIdToUse = $this->determineSoortLidByAge($geboortedatumParameter);
        }
        
        // Validate the stalling parameter (max 3)
        $validatedStalling = $this->validateStallingValue($stallingParameter);
        
        // Now update the familielid in the database
        $updateResult = $this->updateFamilielidInDatabase($familielidIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $validatedStalling, $soortLidIdToUse);
        
        return $updateResult;
    }
    
    // This function determines soort_lid based on age
    public function determineSoortLidByAge($geboortedatumParameter) {
        // Include the SoortlidController
        require_once __DIR__ . '/SoortlidController.php';
        
        // Create a SoortlidController instance
        $soortlidControllerObject = new \SoortlidController($this->databaseConnection);
        
        // Get the soortlid based on age
        $soortlidObjectFromAge = $soortlidControllerObject->getSoortlidByLeeftijd($geboortedatumParameter);
        
        // Check if we got a valid soortlid
        $soortlidWasFound = false;
        $soortLidIdResult = 1; // Default fallback to ID 1
        
        if ($soortlidObjectFromAge != null) {
            $soortlidWasFound = true;
            $soortLidIdResult = $soortlidObjectFromAge->id;
        }
        
        // Return the determined soort_lid_id
        return $soortLidIdResult;
    }
    
    // This function validates the stalling value
    public function validateStallingValue($stallingInputParameter) {
        // Convert to integer
        $stallingAsInteger = intval($stallingInputParameter);
        
        // Ensure minimum value is 0
        $minimumStalling = 0;
        if ($stallingAsInteger < $minimumStalling) {
            $stallingAsInteger = $minimumStalling;
        }
        
        // Ensure maximum value is 3
        $maximumStalling = 3;
        if ($stallingAsInteger > $maximumStalling) {
            $stallingAsInteger = $maximumStalling;
        }
        
        return $stallingAsInteger;
    }
    
    // This function updates the familielid in the database
    public function updateFamilielidInDatabase($familielidIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter, $soortLidIdParameter) {
        // Prepare the update statement
        $updateFamilielidStatement = $this->databaseConnection->prepare("UPDATE familielid SET naam = ?, geboortedatum = ?, soort_familielid = ?, stalling = ?, soort_lid_id = ? WHERE id = ?");
        
        // Set up the parameters for the update
        $updateParameters = array($naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter, $soortLidIdParameter, $familielidIdParameter);
        
        // Execute the update statement
        $updateResult = $updateFamilielidStatement->execute($updateParameters);
        
        // Check if any rows were affected
        $rowsAffected = $updateFamilielidStatement->rowCount();
        $updateWasSuccessful = false;
        if ($rowsAffected > 0) {
            $updateWasSuccessful = true;
        }
        
        // Return whether the update was successful
        return $updateWasSuccessful;
    }
}
