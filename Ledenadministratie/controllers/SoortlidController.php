<?php
// Include the Soortlid model so we can work with member types
require_once __DIR__ . '/../models/Soortlid.php';

// This class handles soortlid (member type) operations
class SoortlidController {
    // This variable stores our database connection
    public $databaseConnection;
    
    // Constructor function that runs when we create a new SoortlidController
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function gets all soortleden from the database
    public function getAllSoortleden() {
        // Prepare and execute query to get all soortleden
        $getAllSoortledenStatement = $this->databaseConnection->prepare("SELECT * FROM soortlid ORDER BY omschrijving ASC");
        $getAllSoortledenStatement->execute();
        
        // Get all results as array
        $allSoortledenArray = $getAllSoortledenStatement->fetchAll(PDO::FETCH_ASSOC);
        
        // Return the array of soortleden
        return $allSoortledenArray;
    }

    // This function gets a specific soortlid by ID
    public function getSoortlidById($soortlidIdParameter) {
        // Prepare and execute query to get soortlid by ID
        $getSoortlidStatement = $this->databaseConnection->prepare("SELECT * FROM soortlid WHERE id = ?");
        $queryParameters = array($soortlidIdParameter);
        $getSoortlidStatement->execute($queryParameters);
        
        // Get the soortlid data
        $soortlidDataFromDatabase = $getSoortlidStatement->fetch(PDO::FETCH_ASSOC);
        
        // Return the soortlid data
        return $soortlidDataFromDatabase;
    }

    // This function gets a soortlid based on age
    public function getSoortlidByLeeftijd($geboortedatumParameter, $datumParameter = null) {
        // First determine which date to use for calculation
        $dateToUseForCalculation = $datumParameter;
        $datumWasProvided = false;
        if ($datumParameter !== null) {
            $datumWasProvided = true;
        }
        
        // If no date was provided, use current date
        if (!$datumWasProvided) {
            $dateToUseForCalculation = date('Y-m-d');
        }

        // Calculate age on the specified date
        $ageInYears = $this->calculateAgeOnDate($geboortedatumParameter, $dateToUseForCalculation);

        // Find matching soortlid based on age
        $soortlidFromDatabase = $this->findSoortlidByAge($ageInYears);
        
        // If no soortlid was found, return null
        $soortlidWasFound = false;
        if ($soortlidFromDatabase != false) {
            $soortlidWasFound = true;
        }
        
        if (!$soortlidWasFound) {
            return null;
        }

        // Create and populate a Soortlid object
        $soortlidObject = $this->createSoortlidObjectFromData($soortlidFromDatabase);
        
        return $soortlidObject;
    }
    
    // This function calculates age on a specific date
    public function calculateAgeOnDate($geboortedatumParameter, $referenceDateParameter) {
        // Create DateTime objects for birth date and reference date
        $birthDateObject = new \DateTime($geboortedatumParameter);
        $referenceDateObject = new \DateTime($referenceDateParameter);
        
        // Calculate the difference in years
        $dateDifferenceObject = $referenceDateObject->diff($birthDateObject);
        $ageInYears = $dateDifferenceObject->y;
        
        return $ageInYears;
    }
    
    // This function finds a soortlid by age from database
    public function findSoortlidByAge($ageParameter) {
        // Prepare query to find soortlid by age range
        $findSoortlidStatement = $this->databaseConnection->prepare("SELECT * FROM soortlid WHERE minimum_leeftijd <= ? AND (maximum_leeftijd >= ? OR maximum_leeftijd IS NULL) LIMIT 1");
        $queryParameters = array($ageParameter, $ageParameter);
        $findSoortlidStatement->execute($queryParameters);
        
        // Get the result
        $soortlidResult = $findSoortlidStatement->fetch(\PDO::FETCH_ASSOC);
        
        return $soortlidResult;
    }
    
    // This function creates a Soortlid object from database data
    public function createSoortlidObjectFromData($soortlidDataArray) {
        // Include the Soortlid model
        require_once __DIR__ . '/../models/Soortlid.php';
        
        // Create a new Soortlid object
        $soortlidObject = new \models\Soortlid();
        
        // Populate the object with data from database
        $soortlidObject->id = $soortlidDataArray['id'];
        $soortlidObject->omschrijving = $soortlidDataArray['omschrijving'];
        $soortlidObject->minimum_leeftijd = $soortlidDataArray['minimum_leeftijd'];
        $soortlidObject->maximum_leeftijd = $soortlidDataArray['maximum_leeftijd'];
        
        return $soortlidObject;
    }
}
?>
