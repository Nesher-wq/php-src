<?php
namespace models;
use config\Connection;

// Directe toegang tot dit bestand blokkeren
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}

class Soortlid {
    public $id;
    public $omschrijving;
    public $minimum_leeftijd;
    public $maximum_leeftijd;

    public function getOmschrijvingById($id) {
        require_once __DIR__ . '/../../../Ledenadministratie_config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("SELECT omschrijving FROM soortlid WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ? $result['omschrijving'] : null;
    }

    // function to connect to SQL using connection.php and return the values from the Soortlid table
    public function getAllSoortleden() 
    {
        require_once __DIR__ . '/../../../Ledenadministratie_config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM soortlid ORDER BY minimum_leeftijd ASC");
        $stmt->execute();

        $soortleden = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $soortlid = new Soortlid();
            $soortlid->id = $row['id'];
            $soortlid->omschrijving = $row['omschrijving'];
            $soortlid->minimum_leeftijd = $row['minimum_leeftijd'];
            $soortlid->maximum_leeftijd = $row['maximum_leeftijd'];
            $soortleden[] = $soortlid;
        }

        return $soortleden;
    }
}