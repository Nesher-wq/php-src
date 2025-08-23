<?php

// This class handles user authentication (login and logout)
class AuthController {
    // This variable stores our database connection
    public $databaseConnection;

    // Constructor function that runs when we create a new AuthController
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function handles user login
    public function login($usernameParameter, $passwordParameter) {
        // Use a try-catch block to handle any database errors
        $databaseErrorOccurred = false;
        $loginResult = false;
        
        try {
            // Prepare a database query to find the user by username
            $findUserStatement = $this->databaseConnection->prepare("SELECT * FROM users WHERE username = ?");
            $queryParameters = array($usernameParameter);
            $findUserStatement->execute($queryParameters);
            
            // Get the user data from the database
            $userDataFromDatabase = $findUserStatement->fetch(PDO::FETCH_ASSOC);

            // Check if we actually found a user
            $userWasFound = false;
            if ($userDataFromDatabase != false) {
                $userWasFound = true;
            }
            
            // If no user was found, return false
            if ($userWasFound == false) {
                return false;
            }

            // First try to verify password as if it's already hashed
            $passwordIsCorrectHashed = password_verify($passwordParameter, $userDataFromDatabase['password']);
            
            // If hashed password verification worked, login is successful
            if ($passwordIsCorrectHashed == true) {
                $this->setSessionVariablesForUser($userDataFromDatabase);
                return true;
            }
            
            // If hashed verification failed, check if it's a plain text password
            $passwordIsCorrectPlainText = false;
            if ($passwordParameter === $userDataFromDatabase['password']) {
                $passwordIsCorrectPlainText = true;
            }
            
            // If plain text password matches, hash it and update database
            if ($passwordIsCorrectPlainText == true) {
                // Hash the password for storage
                $hashedPasswordForStorage = password_hash($passwordParameter, PASSWORD_DEFAULT);
                
                // Update the user's password in the database
                $updatePasswordStatement = $this->databaseConnection->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateParameters = array($hashedPasswordForStorage, $userDataFromDatabase['id']);
                $updateResult = $updatePasswordStatement->execute($updateParameters);
                
                // Check if the password update was successful
                $updateWasSuccessful = false;
                if ($updateResult == true) {
                    $updateWasSuccessful = true;
                }
                
                // If update failed, log error but still allow login since password was correct
                if ($updateWasSuccessful == false) {
                    $updateErrorMessage = "AuthController: Failed to update password for user ID: " . $userDataFromDatabase['id'];
                    error_log($updateErrorMessage);
                }
                
                // Set session variables for successful login
                $this->setSessionVariablesForUser($userDataFromDatabase);
                return true;
            }
            
            // If neither hashed nor plain text password worked, login failed
            return false;
            
        } catch (Exception $exceptionObject) {
            // If there was a database error, log it
            $databaseErrorOccurred = true;
            $errorLogMessage = "AuthController: Database error: " . $exceptionObject->getMessage();
            error_log($errorLogMessage);
        }
        
        // If an error occurred, return false
        if ($databaseErrorOccurred == true) {
            return false;
        }
        
        // Default return false if nothing else worked
        return false;
    }

    // This function sets session variables for a logged in user
    public function setSessionVariablesForUser($userDataArray) {
        // Set all the required session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $userDataArray['id'];
        $_SESSION['username'] = $userDataArray['username'];
        $_SESSION['role'] = $userDataArray['role'];
    }

    // This function handles user logout
    public function logout() {
        // Destroy the current session
        session_destroy();
        
        // Redirect to the main page
        $redirectLocation = 'Location: /Ledenadministratie/index.php';
        header($redirectLocation);
        exit;
    }
}
?>