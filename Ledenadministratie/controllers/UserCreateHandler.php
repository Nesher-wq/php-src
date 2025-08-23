<?php

// This class handles creating new users in the system
class UserCreateHandler {
    // This variable stores our database connection
    public $databaseConnection;

    // Constructor function that runs when we create a new UserCreateHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function creates a new user in the database
    public function createUser($usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter = '') {
        // First check if the username already exists
        $usernameExistsCheckResult = $this->checkIfUsernameExists($usernameParameter);
        
        // If username already exists, return false
        if ($usernameExistsCheckResult == true) {
            return false;
        }
        
        // Store the password as plain text - it will be hashed on first login
        $passwordForStorage = $passwordParameter;
        
        // Prepare the SQL statement to insert the new user
        $insertUserStatement = $this->databaseConnection->prepare("INSERT INTO users (username, password, role, description) VALUES (?, ?, ?, ?)");
        
        // Set up the parameters for the insert statement
        $insertParameters = array($usernameParameter, $passwordForStorage, $roleParameter, $descriptionParameter);
        
        // Execute the insert statement
        $insertResult = $insertUserStatement->execute($insertParameters);
        
        // Check if the insert was successful
        $insertWasSuccessful = false;
        if ($insertResult == true) {
            // Check how many rows were affected
            $rowsAffected = $insertUserStatement->rowCount();
            if ($rowsAffected > 0) {
                $insertWasSuccessful = true;
            }
        }
        
        // Return the result
        return $insertWasSuccessful;
    }
    
    // This function checks if a username already exists in the database
    public function checkIfUsernameExists($usernameToCheck) {
        // Prepare a statement to check for existing username
        $checkUsernameStatement = $this->databaseConnection->prepare("SELECT username FROM users WHERE username = ?");
        
        // Set up parameters and execute the check
        $checkParameters = array($usernameToCheck);
        $checkUsernameStatement->execute($checkParameters);
        
        // Check how many rows were found
        $rowsFound = $checkUsernameStatement->rowCount();
        
        // If we found any rows, the username exists
        $usernameExists = false;
        if ($rowsFound > 0) {
            $usernameExists = true;
        }
        
        // Return whether the username exists
        return $usernameExists;
    }
}
