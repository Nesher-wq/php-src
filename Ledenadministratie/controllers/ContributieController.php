<?php

// This class handles contribution calculations for members
class ContributieController {
    // This variable stores our database connection
    public $databaseConnection;
    
    // Constructor function that runs when we create a new ContributieController
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }
    
    // This function calculates contributions for a specific year
    public function berekenContributies($boekjaarParameter) {
        // Include the Contributie model
        require_once __DIR__ . '/../models/Contributie.php';
        
        // Use a try-catch block to handle any errors
        $errorOccurred = false;
        $resultArray = array();
        
        try {
            // Check if session is started, if not start it
            $sessionIsActive = false;
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
                $sessionIsActive = true;
            } else {
                $sessionIsActive = true;
            }
            
            // Set PDO connection in the Contributie model
            \models\Contributie::setPDO($this->databaseConnection);
            
            // Try to calculate the contributions
            $calculationWasSuccessful = \models\Contributie::createContributies($boekjaarParameter);
            
            // Check if calculation was successful
            if ($calculationWasSuccessful == true) {
                // Get the calculated contributions from session
                $berekendContributiesFromSession = array();
                $contributiesExistInSession = false;
                if (isset($_SESSION['berekende_contributies'])) {
                    $contributiesExistInSession = true;
                    $berekendContributiesFromSession = $_SESSION['berekende_contributies'];
                }
                
                // Create success result array
                $resultArray['success'] = true;
                $resultArray['message'] = 'Contributies succesvol berekend voor boekjaar ' . $boekjaarParameter;
                $resultArray['contributies'] = $berekendContributiesFromSession;
                
                return $resultArray;
            }
            
            // If calculation failed, create error result
            $resultArray['success'] = false;
            $resultArray['error'] = 'Er is een fout opgetreden bij het berekenen van de contributies';
            return $resultArray;
            
        } catch (Exception $exceptionObject) {
            // If an exception occurred, create error result
            $errorOccurred = true;
            $resultArray['success'] = false;
            $resultArray['error'] = $exceptionObject->getMessage();
        }
        
        // Return error result if exception occurred
        if ($errorOccurred == true) {
            return $resultArray;
        }
        
        // Default fallback return
        $fallbackResultArray = array();
        $fallbackResultArray['success'] = false;
        $fallbackResultArray['error'] = 'Unknown error occurred';
        return $fallbackResultArray;
    }
    
    // This function validates if user has access to contribution functions
    public function validateAccess($sessionParameter) {
        // Check if role exists in session
        $roleExistsInSession = false;
        if (isset($sessionParameter['role'])) {
            $roleExistsInSession = true;
        }
        
        // If role doesn't exist, access is denied
        if ($roleExistsInSession == false) {
            return false;
        }
        
        // Get the role from session
        $userRoleFromSession = $sessionParameter['role'];
        
        // Check if role is treasurer
        $userIsTreasurer = false;
        if ($userRoleFromSession === 'treasurer') {
            $userIsTreasurer = true;
        }
        
        // Return whether user is treasurer (has access)
        return $userIsTreasurer;
    }
}

?>
