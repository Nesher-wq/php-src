<?php

// a 'familie' (English: family) is a group of people who share a common household
// contains properties Id, Naam, StraatEnHuisnummer, Postcode, Woonplaats en familieleden

namespace models;
use models\Familielid; // pending implementation
use PDO;
use PDOException;

// Directe toegang tot dit bestand blokkeren
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}


class Familie {
    public $id;
    public $naam;
    public $straat;
    public $huisnummer;
    public $postcode;
    public $woonplaats;
    public $familieleden = [];

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function voegFamilielidToe(Familielid $lid) {
        $this->familieleden[] = $lid;
    }

    public static function getAllFamilieleden(array $families): array {
        $allFamilieleden = [];

        foreach ($families as $familie) {
            if ($familie instanceof self) {
                $allFamilieleden = array_merge($allFamilieleden, $familie->familieleden);
            }
        }

        return $allFamilieleden;
    }

    public function create($naam, $straat, $huisnummer, $postcode, $woonplaats) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO familie (naam, straat, huisnummer, postcode, woonplaats) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats]);
        } catch (PDOException $e) {
            writeLog("Familie Model Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $naam, $straat, $huisnummer, $postcode, $woonplaats) {
        try {
            $stmt = $this->pdo->prepare("UPDATE familie SET naam = ?, straat = ?, huisnummer = ?, postcode = ?, woonplaats = ? WHERE id = ?");
            return $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats, $id]);
        } catch (PDOException $e) {
            writeLog("Familie Model Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM familie WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            writeLog("Familie Model Error: " . $e->getMessage());
            return false;
        }
    }

    public function getFamilieById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM familie WHERE id = ?");
            $stmt->execute([$id]);
            $familie = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($familie) {
                // Initialize empty familieleden array
                $familie['familieleden'] = [];
                
                // Get family members if they exist
                $stmt = $this->pdo->prepare("SELECT * FROM familielid WHERE familie_id = ?");
                $stmt->execute([$id]);
                $familieleden = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                if ($familieleden) {
                    $familie['familieleden'] = $familieleden;
                }
            }
            
            return $familie;
        } catch (\PDOException $e) {
            writeLog("Error in Familie model getFamilieById: " . $e->getMessage());
            return null;
        }
    }
}
?>