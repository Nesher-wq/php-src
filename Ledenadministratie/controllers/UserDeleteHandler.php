<?php

// This class handles deleting users from the database
class UserDeleteHandler {
    // This variable stores our database connection
    public $databaseConnection;

    // Constructor function that runs when we create a new UserDeleteHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }
    
    // This function deletes a user from the database
    public function deleteUser($userIdParameter) {
        // First get the user data to check if deletion is allowed
        $userToDelete = $this->getUserToDelete($userIdParameter);
        
        // Check if user is trying to delete themselves
        $userIsDeletingThemselves = $this->checkIfUserIsDeletingThemselves($userToDelete);
        
        // If user is trying to delete themselves, prevent it
        if ($userIsDeletingThemselves) {
            return false;
        }

        // Check if this is the admin user
        $isAdminUser = false;
        if ($userToDelete['username'] === 'admin') {
            $isAdminUser = true;
        }
        
        // Prevent deletion of admin user
        if ($isAdminUser) {
            return false;
        }

        // Proceed with deletion
        $deleteResult = $this->performUserDeletion($userIdParameter);
        
        return $deleteResult;
    }
    
    // This function gets the user data for the user to be deleted
    public function getUserToDelete($userIdParameter) {
        // Prepare statement to get user data
        $getUserStatement = $this->databaseConnection->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $queryParameters = array($userIdParameter);
        $getUserStatement->execute($queryParameters);
        
        // Get and return the user data
        $userDataFromDatabase = $getUserStatement->fetch(PDO::FETCH_ASSOC);
        return $userDataFromDatabase;
    }
    
    // This function checks if user is trying to delete themselves
    public function checkIfUserIsDeletingThemselves($userToDeleteData) {
        // Check if session user_id exists and matches the user to delete
        $sessionUserIdExists = false;
        if (isset($_SESSION['user_id'])) {
            $sessionUserIdExists = true;
        }
        
        // If session exists, check if IDs match
        $userIsDeletingThemselves = false;
        if ($sessionUserIdExists) {
            $sessionUserId = $_SESSION['user_id'];
            $userToDeleteId = $userToDeleteData['id'];
            
            if ($sessionUserId == $userToDeleteId) {
                $userIsDeletingThemselves = true;
            }
        }
        
        return $userIsDeletingThemselves;
    }
    
    // This function performs the actual user deletion
    public function performUserDeletion($userIdParameter) {
        // Prepare delete statement
        $deleteStatement = $this->databaseConnection->prepare("DELETE FROM users WHERE id = ?");
        $deleteParameters = array($userIdParameter);
        $deleteResult = $deleteStatement->execute($deleteParameters);
        
        // Check if any rows were affected
        $rowsAffected = $deleteStatement->rowCount();
        $deletionWasSuccessful = false;
        if ($rowsAffected > 0) {
            $deletionWasSuccessful = true;
        }
        
        return $deletionWasSuccessful;
    }
}
