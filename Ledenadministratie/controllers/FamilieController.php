<?php
// Controller voor familiebeheer
require_once __DIR__ . '/../models/Familie.php';

class FamilieController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllFamilies() {
        $stmt = $this->pdo->query("SELECT * FROM familie ORDER BY naam ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function createFamilie($naam, $straat, $huisnummer, $postcode, $woonplaats) {
        try {
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController: Creating family with name: $naam" . PHP_EOL, FILE_APPEND);
            
            $sql = "INSERT INTO familie (naam, straat, huisnummer, postcode, woonplaats) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController: Executing SQL: $sql" . PHP_EOL, FILE_APPEND);
            
            $result = $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats]);
            
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController: SQL execution result: " . ($result ? "SUCCESS" : "FAILED") . PHP_EOL, FILE_APPEND);
            
            return $result;
        } catch (PDOException $e) {
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return false;
        }
    }

    public function getFamilieById($id) {
        try {
            // Get family data
            $stmt = $this->pdo->prepare("SELECT * FROM familie WHERE id = ?");
            $stmt->execute([$id]);
            $familie = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($familie) {
                // Get family members
                $stmt = $this->pdo->prepare("SELECT * FROM familielid WHERE familie_id = ?");
                $stmt->execute([$id]);
                $familie['familieleden'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $familie;
        } catch (PDOException $e) {
            error_log("Error in getFamilieById: " . $e->getMessage());
            return false;
        }
    }

    public function updateFamilie($id, $naam, $straat, $huisnummer, $postcode, $woonplaats) {
        $stmt = $this->pdo->prepare("UPDATE familie SET naam = ?, straat = ?, huisnummer = ?, postcode = ?, woonplaats = ? WHERE id = ?");
        $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats, $id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteFamilie($id) {
        try {
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController: Deleting family with ID: $id" . PHP_EOL, FILE_APPEND);
            
            $sql = "DELETE FROM familie WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$id]);
            
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController: Delete result: " . ($result ? "SUCCESS" : "FAILED") . PHP_EOL, FILE_APPEND);
            return $result;
        } catch (PDOException $e) {
            file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . "FamilieController Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return false;
        }
    }
}
