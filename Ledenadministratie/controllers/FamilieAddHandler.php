<?php
// Include the Familie model so we can add new families to the database
require_once __DIR__ . '/../models/Familie.php';
require_once __DIR__ . '/../includes/utils.php';

// This class handles adding new families to the database
class FamilieAddHandler {
    // This variable stores our Familie model object
    public $familieModelObject;
    
    // Constructor function that runs when we create a new FamilieAddHandler
    public function __construct($databaseConnection) {
        // Create a new Familie model object and store it
        $this->familieModelObject = new models\Familie($databaseConnection);
    }
    
    // This is the main function that handles add requests for families
    public function handleAddFamilieRequest() {
        // First we check if someone clicked the add familie button
        $addButtonWasClicked = false;
        if (isset($_POST['add_familie'])) {
            $addButtonWasClicked = true;
        }
        
        // If the add button was clicked, we try to add the new familie
        if ($addButtonWasClicked) {
            $addResult = $this->addNewFamilieToDatabase();
            return $addResult;
        }
        
        // If we get here, something went wrong with the request
        $errorMessageText = "Invalid add familie request";
        $errorResultArray = array();
        $errorResultArray['success'] = false;
        $errorResultArray['message'] = $errorMessageText;
        return $errorResultArray;
    }
    
    // This function gets the form data and adds a new familie to the database
    public function addNewFamilieToDatabase() {
        // First we need to get all the form data
        // Initialize all variables with empty strings
        $familieNaamFromForm = '';
        $familiestraatFromForm = '';
        $familieHuisnummerFromForm = '';
        $familiePostcodeFromForm = '';
        $familieWoonplaatsFromForm = '';
        
        // Get the familie naam from the form
        if (isset($_POST['familie_naam'])) {
            $familieNaamFromForm = $_POST['familie_naam'];
        }
        
        // Get the familie straat from the form
        if (isset($_POST['familie_straat'])) {
            $familiestraatFromForm = $_POST['familie_straat'];
        }
        
        // Get the familie huisnummer from the form
        if (isset($_POST['familie_huisnummer'])) {
            $familieHuisnummerFromForm = $_POST['familie_huisnummer'];
        }
        
        // Get the familie postcode from the form
        if (isset($_POST['familie_postcode'])) {
            $familiePostcodeFromForm = $_POST['familie_postcode'];
        }
        
        // Get the familie woonplaats from the form
        if (isset($_POST['familie_woonplaats'])) {
            $familieWoonplaatsFromForm = $_POST['familie_woonplaats'];
        }
        
        // Now try to create the new familie using our model
        $createOperationResult = $this->familieModelObject->create(
            $familieNaamFromForm,
            $familiestraatFromForm,
            $familieHuisnummerFromForm,
            $familiePostcodeFromForm,
            $familieWoonplaatsFromForm
        );
        
        // Check if the create operation was successful
        $createWasSuccessful = false;
        if ($createOperationResult) {
            $createWasSuccessful = true;
        }
        
        // If create was successful, return success message
        if ($createWasSuccessful) {
            $successMessageText = "Familie succesvol toegevoegd.";
            $successResultArray = array();
            $successResultArray['success'] = true;
            $successResultArray['message'] = $successMessageText;
            return $successResultArray;
        }
        
        // If we get here, the create failed
        $createFailedErrorMessage = "Fout bij het toevoegen van de familie.";
        $createFailedErrorArray = array();
        $createFailedErrorArray['success'] = false;
        $createFailedErrorArray['message'] = $createFailedErrorMessage;
        return $createFailedErrorArray;
    }
}
