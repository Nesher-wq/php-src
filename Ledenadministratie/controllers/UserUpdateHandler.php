<?php

// This class handles updating users in the database
class UserUpdateHandler {
    // This variable stores our database connection
    public $databaseConnection;

    // Constructor function that runs when we create a new UserUpdateHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }
    
    // This function updates a user in the database
    public function updateUser($userIdParameter, $usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter = '') {
        // First get the current user data
        $currentUserData = $this->getCurrentUserData($userIdParameter);
        
        // Check if this is the admin user
        $isAdminUser = false;
        if ($currentUserData['username'] === 'admin') {
            $isAdminUser = true;
        }
        
        // Handle admin user update differently
        if ($isAdminUser) {
            $adminUpdateResult = $this->updateAdminUser($userIdParameter, $passwordParameter, $descriptionParameter);
            return $adminUpdateResult;
        }
        
        // For non-admin users, first check if new username already exists
        $usernameConflictExists = $this->checkUsernameConflict($usernameParameter, $userIdParameter);
        
        // If username conflict exists, return false
        if ($usernameConflictExists) {
            return false;
        }

        // Check if password is provided
        $passwordIsProvided = false;
        if ($passwordParameter != '') {
            $passwordIsProvided = true;
        }
        
        // Update user with or without password
        if (!$passwordIsProvided) {
            $updateResult = $this->updateUserWithoutPassword($userIdParameter, $usernameParameter, $roleParameter, $descriptionParameter);
        } else {
            $updateResult = $this->updateUserWithPassword($userIdParameter, $usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter);
        }
        
        return $updateResult;
    }
    
    // This function gets current user data
    public function getCurrentUserData($userIdParameter) {
        // Prepare statement to get current user
        $getUserStatement = $this->databaseConnection->prepare("SELECT username, role FROM users WHERE id = ?");
        $queryParameters = array($userIdParameter);
        $getUserStatement->execute($queryParameters);
        
        // Get and return the user data
        $userDataFromDatabase = $getUserStatement->fetch(PDO::FETCH_ASSOC);
        return $userDataFromDatabase;
    }
    
    // This function updates the admin user (only password and description)
    public function updateAdminUser($userIdParameter, $passwordParameter, $descriptionParameter) {
        // Check if password is provided
        $passwordIsProvided = false;
        if ($passwordParameter != '') {
            $passwordIsProvided = true;
        }
        
        // Update admin with or without password
        if ($passwordIsProvided) {
            // Hash the new password
            $hashedPasswordForStorage = password_hash($passwordParameter, PASSWORD_DEFAULT);
            
            // Update with password
            $updateStatement = $this->databaseConnection->prepare("UPDATE users SET password = ?, description = ? WHERE id = ?");
            $updateParameters = array($hashedPasswordForStorage, $descriptionParameter, $userIdParameter);
            $updateStatement->execute($updateParameters);
        } else {
            // Update without password
            $updateStatement = $this->databaseConnection->prepare("UPDATE users SET description = ? WHERE id = ?");
            $updateParameters = array($descriptionParameter, $userIdParameter);
            $updateStatement->execute($updateParameters);
        }
        
        return true;
    }
    
    // This function checks if new username conflicts with existing users
    public function checkUsernameConflict($usernameParameter, $userIdParameter) {
        // Check if new username already exists for other users
        $checkConflictStatement = $this->databaseConnection->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkParameters = array($usernameParameter, $userIdParameter);
        $checkConflictStatement->execute($checkParameters);
        
        // Check if any conflicts were found
        $conflictRowsFound = $checkConflictStatement->rowCount();
        $conflictExists = false;
        if ($conflictRowsFound > 0) {
            $conflictExists = true;
        }
        
        return $conflictExists;
    }
    
    // This function updates user without password
    public function updateUserWithoutPassword($userIdParameter, $usernameParameter, $roleParameter, $descriptionParameter) {
        // Prepare update statement without password
        $updateStatement = $this->databaseConnection->prepare("UPDATE users SET username = ?, role = ?, description = ? WHERE id = ?");
        $updateParameters = array($usernameParameter, $roleParameter, $descriptionParameter, $userIdParameter);
        $updateResult = $updateStatement->execute($updateParameters);
        
        return $updateResult;
    }
    
    // This function updates user with password
    public function updateUserWithPassword($userIdParameter, $usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter) {
        // Hash the new password
        $hashedPasswordForStorage = password_hash($passwordParameter, PASSWORD_DEFAULT);
        
        // Prepare update statement with password
        $updateStatement = $this->databaseConnection->prepare("UPDATE users SET username = ?, password = ?, role = ?, description = ? WHERE id = ?");
        $updateParameters = array($usernameParameter, $hashedPasswordForStorage, $roleParameter, $descriptionParameter, $userIdParameter);
        $updateResult = $updateStatement->execute($updateParameters);
        
        return $updateResult;
    }
}
