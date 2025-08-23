<?php
// Include all the familielid handlers and models we need
require_once __DIR__ . '/../models/Familielid.php';
require_once __DIR__ . '/FamilielidGetHandler.php';
require_once __DIR__ . '/FamilielidCreateHandler.php';
require_once __DIR__ . '/FamilielidUpdateHandler.php';
require_once __DIR__ . '/FamilielidDeleteHandler.php';

// This class is the main controller for handling Familielid requests
class FamilielidController {
    // These variables store our database connection and handler objects
    public $databaseConnection;
    public $getHandlerObject;
    public $createHandlerObject;
    public $updateHandlerObject;
    public $deleteHandlerObject;
    
    // Constructor function that runs when we create a new FamilielidController
    public function __construct($databaseConnectionParameter) {
        // Store the database connection
        $this->databaseConnection = $databaseConnectionParameter;
        
        // Create all the handler objects we need for different operations
        $this->getHandlerObject = new FamilielidGetHandler($databaseConnectionParameter);
        $this->createHandlerObject = new FamilielidCreateHandler($databaseConnectionParameter);
        $this->updateHandlerObject = new FamilielidUpdateHandler($databaseConnectionParameter);
        $this->deleteHandlerObject = new FamilielidDeleteHandler($databaseConnectionParameter);
    }

    // This function gets familieleden for a specific family using the get handler
    public function getFamilieLedenByFamilieId($familieIdParameter) {
        // Use our get handler to get familieleden by family ID
        $familieLedenResult = $this->getHandlerObject->getFamilieLedenByFamilieId($familieIdParameter);
        return $familieLedenResult;
    }

    // This function gets a specific familielid by ID using the get handler
    public function getFamilielidById($familielidIdParameter) {
        // Use our get handler to get familielid by ID
        $familielidResult = $this->getHandlerObject->getFamilielidById($familielidIdParameter);
        return $familielidResult;
    }

    // This function creates a new familielid using the create handler
    public function createFamilielid($familieIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter = 0, $soortLidIdParameter = null) {
        // Use our create handler to create the familielid
        $createResult = $this->createHandlerObject->createFamilielid($familieIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter, $soortLidIdParameter);
        return $createResult;
    }

    // This function updates a familielid using the update handler
    public function updateFamilielid($familielidIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter = 0, $soortLidIdParameter = null) {
        // Use our update handler to update the familielid
        $updateResult = $this->updateHandlerObject->updateFamilielid($familielidIdParameter, $naamParameter, $geboortedatumParameter, $soortFamilielidParameter, $stallingParameter, $soortLidIdParameter);
        return $updateResult;
    }

    // This function deletes a familielid using the delete handler
    public function deleteFamilielid($familielidIdParameter) {
        // Use our delete handler to delete the familielid
        $deleteResult = $this->deleteHandlerObject->deleteFamilielid($familielidIdParameter);
        return $deleteResult;
    }

    // This function counts familieleden for a specific family using the get handler
    public function countFamilieLedenByFamilieId($familieIdParameter) {
        // Use our get handler to count familieleden by family ID
        $countResult = $this->getHandlerObject->countFamilieLedenByFamilieId($familieIdParameter);
        return $countResult;
    }

    // This function gets all familieleden using the get handler
    public function getAllFamilieleden() {
        // Use our get handler to get all familieleden
        $allFamilieLedenResult = $this->getHandlerObject->getAllFamilieleden();
        return $allFamilieLedenResult;
    }
    
    // This helper function calculates age based on birth date
    public function calculateAge($geboortedatumParameter) {
        // Create date objects for birth date and today
        $birthDateObject = new \DateTime($geboortedatumParameter);
        $todayDateObject = new \DateTime();
        
        // Calculate the difference in years
        $dateDifferenceObject = $todayDateObject->diff($birthDateObject);
        $ageInYears = $dateDifferenceObject->y;
        
        // Return the age in years
        return $ageInYears;
    }
}
?>
