
<?php

// Include all the user handlers we need
require_once __DIR__ . '/../models/Familielid.php';
require_once __DIR__ . '/UserCreateHandler.php';
require_once __DIR__ . '/UserGetHandler.php';
require_once __DIR__ . '/UserUpdateHandler.php';
require_once __DIR__ . '/UserDeleteHandler.php';

// This class is the main controller for handling User requests
class UserController {
    // These variables store our database connection and handler objects
    public $databaseConnection;
    public $createHandlerObject;
    public $getHandlerObject;
    public $updateHandlerObject;
    public $deleteHandlerObject;

    // Constructor function that runs when we create a new UserController
    public function __construct($databaseConnectionParameter) {
        // Store the database connection
        $this->databaseConnection = $databaseConnectionParameter;
        
        // Create all the handler objects we need for different operations
        $this->createHandlerObject = new UserCreateHandler($databaseConnectionParameter);
        $this->getHandlerObject = new UserGetHandler($databaseConnectionParameter);
        $this->updateHandlerObject = new UserUpdateHandler($databaseConnectionParameter);
        $this->deleteHandlerObject = new UserDeleteHandler($databaseConnectionParameter);
    }

    // This function creates a new user using the create handler
    public function createUser($usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter = '') {
        // Use our create handler to create the user
        $createResult = $this->createHandlerObject->createUser($usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter);
        return $createResult;
    }

    // This function gets all users using the get handler
    public function getAllUsers() {
        // Use our get handler to get all users
        $allUsersResult = $this->getHandlerObject->getAllUsers();
        return $allUsersResult;
    }

    // This function gets a specific user by ID using the get handler
    public function getUserById($userIdParameter) {
        // Use our get handler to get the user by ID
        $userByIdResult = $this->getHandlerObject->getUserById($userIdParameter);
        return $userByIdResult;
    }
    
    // This function updates a user using the update handler
    public function updateUser($userIdParameter, $usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter = '') {
        // Use our update handler to update the user
        $updateResult = $this->updateHandlerObject->updateUser($userIdParameter, $usernameParameter, $passwordParameter, $roleParameter, $descriptionParameter);
        return $updateResult;
    }
    
    // This function deletes a user using the delete handler
    public function deleteUser($userIdParameter) {
        // Use our delete handler to delete the user
        $deleteResult = $this->deleteHandlerObject->deleteUser($userIdParameter);
        return $deleteResult;
    }
}