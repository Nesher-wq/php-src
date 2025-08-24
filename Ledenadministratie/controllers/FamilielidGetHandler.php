<?php

// This class handles getting familielid data from the database
class FamilielidGetHandler {
    // This variable stores our database connection
    public $databaseConnection;
    
    // Constructor function that runs when we create a new FamilielidGetHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function gets all familieleden for a specific family
    public function getFamilieLedenByFamilieId($familieIdParameter) {
        // Prepare and execute query to get familieleden by family ID
        $getFamilieLedenStatement = $this->databaseConnection->prepare("SELECT * FROM familielid WHERE familie_id = ? ORDER BY naam ASC");
        $queryParameters = array($familieIdParameter);
        $getFamilieLedenStatement->execute($queryParameters);
        
        // Get all results as array
        $familieLedenArray = $getFamilieLedenStatement->fetchAll(PDO::FETCH_ASSOC);
        
        // Return the array of familieleden
        return $familieLedenArray;
    }

    // This function gets a specific familielid by ID
    public function getFamilielidById($familielidIdParameter) {
        // Use try-catch to handle database errors
        $databaseErrorOccurred = false;
        $familielidData = null;
        
        try {
            // Prepare and execute query to get familielid by ID
            $getFamilielidStatement = $this->databaseConnection->prepare("SELECT * FROM familielid WHERE id = ?");
            $queryParameters = array($familielidIdParameter);
            $getFamilielidStatement->execute($queryParameters);
            
            // Get the familielid data
            $familielidData = $getFamilielidStatement->fetch(PDO::FETCH_ASSOC);
            
            // Check if familielid was found
            $familielidWasFound = false;
            if ($familielidData != false) {
                $familielidWasFound = true;
            }
            
            // If no familielid was found, log and return null
            if (!$familielidWasFound) {
                $logMessage = "No familielid found with ID: " . $familielidIdParameter;
                writeLog($logMessage);
                return null;
            }
            
        } catch (PDOException $exceptionObject) {
            // If database error occurred, log it
            $databaseErrorOccurred = true;
            $errorLogMessage = "Error fetching familielid: " . $exceptionObject->getMessage();
            writeLog($errorLogMessage);
        }
        
        // If error occurred, return null
        if ($databaseErrorOccurred) {
            return null;
        }
        
        // Return the familielid data
        return $familielidData;
    }

    // This function counts familieleden for a specific family
    public function countFamilieLedenByFamilieId($familieIdParameter) {
        // Prepare and execute count query
        $countFamilieLedenStatement = $this->databaseConnection->prepare("SELECT COUNT(*) FROM familielid WHERE familie_id = ?");
        $queryParameters = array($familieIdParameter);
        $countFamilieLedenStatement->execute($queryParameters);
        
        // Get the count result
        $familieLedenCount = $countFamilieLedenStatement->fetchColumn();
        
        // Return the count
        return $familieLedenCount;
    }

    // This function gets all familieleden from the database
    public function getAllFamilieleden() {
        // Execute query to get all familieleden
        $getAllFamilieLedenStatement = $this->databaseConnection->query("SELECT * FROM familielid ORDER BY naam ASC");
        
        // Get all results as array
        $allFamilieLedenArray = $getAllFamilieLedenStatement->fetchAll(PDO::FETCH_ASSOC);
        
        // Return the array of all familieleden
        return $allFamilieLedenArray;
    }
}
