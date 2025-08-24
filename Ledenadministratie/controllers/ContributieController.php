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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set PDO connection in the Contributie model
            \models\Contributie::setPDO($this->databaseConnection);
            
            // Try to calculate the contributions
            $calculationWasSuccessful = \models\Contributie::createContributies($boekjaarParameter);
            
            // Check if calculation was successful
            if ($calculationWasSuccessful) {
                // Get the calculated contributions from session
                $berekendContributiesFromSession = array();
                if (isset($_SESSION['berekende_contributies'])) {
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
        if ($errorOccurred) {
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
        if (!isset($sessionParameter['role'])) {
            return false;
        }
        
        // Get the role from session
        $userRoleFromSession = $sessionParameter['role'];
        
        // Return whether user is treasurer (has access)
        return ($userRoleFromSession === 'treasurer');
    }
}

?>
