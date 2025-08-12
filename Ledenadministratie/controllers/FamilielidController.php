<?php
// Controller voor familieleden
require_once __DIR__ . '/../models/Familielid.php';

class FamilielidController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getFamilieLedenByFamilieId($familie_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM familielid WHERE familie_id = ? ORDER BY naam ASC");
        $stmt->execute([$familie_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFamilielidById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM familielid WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createFamilielid($familie_id, $naam, $geboortedatum, $soort_familielid, $stalling = 0, $soort_lid_id = null) {
        // Bepaal automatisch soort lid op basis van leeftijd op 1 januari van het huidige jaar
        if ($soort_lid_id === null) {
            require_once __DIR__ . '/SoortlidController.php';
            $soortlidController = new \SoortlidController($this->pdo);
            $soortlid = $soortlidController->getSoortlidByLeeftijd($geboortedatum);
            $soort_lid_id = $soortlid ? $soortlid->id : 1; // Fallback naar ID 1
        }
        
        // Valideer stalling (max 3)
        $stalling = max(0, min(3, intval($stalling)));
        
        $stmt = $this->pdo->prepare("INSERT INTO familielid (familie_id, naam, geboortedatum, soort_familielid, stalling, soort_lid_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$familie_id, $naam, $geboortedatum, $soort_familielid, $stalling, $soort_lid_id]);
        return $stmt->rowCount() > 0;
    }

    public function updateFamilielid($id, $naam, $geboortedatum, $soort_familielid, $stalling = 0, $soort_lid_id = null) {
        // Bepaal automatisch soort lid op basis van leeftijd op 1 januari van het huidige jaar
        if ($soort_lid_id === null) {
            require_once __DIR__ . '/SoortlidController.php';
            $soortlidController = new \SoortlidController($this->pdo);
            $soortlid = $soortlidController->getSoortlidByLeeftijd($geboortedatum);
            $soort_lid_id = $soortlid ? $soortlid->id : 1; // Fallback naar ID 1
        }
        
        // Valideer stalling (max 3)
        $stalling = max(0, min(3, intval($stalling)));
        
        $stmt = $this->pdo->prepare("UPDATE familielid SET naam = ?, geboortedatum = ?, soort_familielid = ?, stalling = ?, soort_lid_id = ? WHERE id = ?");
        $stmt->execute([$naam, $geboortedatum, $soort_familielid, $stalling, $soort_lid_id, $id]);
        return $stmt->rowCount() > 0;
    }

    private function calculateAge($geboortedatum) {
        $birth = new \DateTime($geboortedatum);
        $today = new \DateTime();
        return $today->diff($birth)->y;
    }

    public function deleteFamilielid($id) {
        $stmt = $this->pdo->prepare("DELETE FROM familielid WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function countFamilieLedenByFamilieId($familie_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM familielid WHERE familie_id = ?");
        $stmt->execute([$familie_id]);
        return $stmt->fetchColumn();
    }

    public function getAllFamilieleden() {
        $stmt = $this->pdo->query("SELECT * FROM familielid ORDER BY naam ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
