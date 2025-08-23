<?php

// This class handles getting users from the database
class UserGetHandler {
    // This variable stores our database connection
    public $databaseConnection;

    // Constructor function that runs when we create a new UserGetHandler
    public function __construct($databaseConnectionParameter) {
        // Store the database connection for later use
        $this->databaseConnection = $databaseConnectionParameter;
    }

    // This function gets all users from the database
    public function getAllUsers() {
        // Prepare and execute a query to get all users
        $getAllUsersStatement = $this->databaseConnection->query("SELECT id, username, role, description FROM users ORDER BY role, username ASC");
        
        // Get all the results as an array
        $allUsersArray = $getAllUsersStatement->fetchAll(PDO::FETCH_ASSOC);
        
        // Return the array of users
        return $allUsersArray;
    }

    // This function gets a specific user by their ID
    public function getUserById($userIdParameter) {
        // Prepare a statement to get user by ID
        $getUserByIdStatement = $this->databaseConnection->prepare("SELECT id, username, role, description FROM users WHERE id = ?");
        
        // Set up parameters and execute the query
        $queryParameters = array($userIdParameter);
        $getUserByIdStatement->execute($queryParameters);
        
        // Get the user data
        $userDataFromDatabase = $getUserByIdStatement->fetch(PDO::FETCH_ASSOC);
        
        // Return the user data
        return $userDataFromDatabase;
    }
}
