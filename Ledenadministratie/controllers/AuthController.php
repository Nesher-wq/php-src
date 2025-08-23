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

            // Check if this is the user's first login with plain text password
            $isFirstLogin = false;
            $firstLoginFieldExists = false;
            if (isset($userDataFromDatabase['first_login'])) {
                $firstLoginFieldExists = true;
                if ($userDataFromDatabase['first_login'] == 1) {
                    $isFirstLogin = true;
                }
            }
            
            // Handle first login case
            if ($firstLoginFieldExists == true && $isFirstLogin == true) {
                $loginResult = $this->handleFirstLogin($userDataFromDatabase, $passwordParameter);
                return $loginResult;
            }
            
            // Handle normal login case (not first login)
            $isNotFirstLogin = false;
            if ($firstLoginFieldExists == false) {
                $isNotFirstLogin = true;
            }
            if ($firstLoginFieldExists == true && $isFirstLogin == false) {
                $isNotFirstLogin = true;
            }
            
            if ($isNotFirstLogin == true) {
                $loginResult = $this->handleNormalLogin($userDataFromDatabase, $passwordParameter);
                return $loginResult;
            }
            
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

    // This function handles first login with plain text password
    public function handleFirstLogin($userDataArray, $plainTextPassword) {
        // Check if the plain text password matches
        $passwordMatches = false;
        if ($plainTextPassword === $userDataArray['password']) {
            $passwordMatches = true;
        }
        
        // If password doesn't match, return false
        if ($passwordMatches == false) {
            return false;
        }
        
        // Hash the password for storage
        $hashedPasswordForStorage = password_hash($plainTextPassword, PASSWORD_DEFAULT);
        
        // Update the user's password in the database
        $updatePasswordStatement = $this->databaseConnection->prepare("UPDATE users SET password = ?, first_login = 0 WHERE id = ?");
        $updateParameters = array($hashedPasswordForStorage, $userDataArray['id']);
        $updateResult = $updatePasswordStatement->execute($updateParameters);
        
        // Check if the password update was successful
        $updateWasSuccessful = false;
        if ($updateResult == true) {
            $updateWasSuccessful = true;
        }
        
        // If update failed, log error and return false
        if ($updateWasSuccessful == false) {
            $updateErrorMessage = "AuthController: Failed to update password for user ID: " . $userDataArray['id'];
            error_log($updateErrorMessage);
            return false;
        }
        
        // Set session variables for successful login
        $this->setSessionVariablesForUser($userDataArray);
        
        // Return true for successful login
        return true;
    }

    // This function handles normal login with hashed password
    public function handleNormalLogin($userDataArray, $passwordToVerify) {
        // Verify the password against the stored hash
        $passwordIsCorrect = password_verify($passwordToVerify, $userDataArray['password']);
        
        // If password is correct, set session variables
        if ($passwordIsCorrect == true) {
            $this->setSessionVariablesForUser($userDataArray);
            return true;
        }
        
        // If password is incorrect, return false
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