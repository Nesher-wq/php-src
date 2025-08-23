<?php

// This class handles deleting familieleden from the database
class FamilielidDeleteHandler {
    // This variable stores our database connection
    public $databaseConnection;
    
    // Constructor function that runs when we create a new FamilielidDeleteHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function deletes a familielid from the database
    public function deleteFamilielid($familielidIdParameter) {
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
}
