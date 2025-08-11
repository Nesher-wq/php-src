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

    public function createFamilielid($familie_id, $naam, $geboortedatum, $omschrijving) {
        $stmt = $this->pdo->prepare("INSERT INTO familielid (familie_id, naam, geboortedatum, omschrijving) VALUES (?, ?, ?, ?)");
        $stmt->execute([$familie_id, $naam, $geboortedatum, $omschrijving]);
        return $stmt->rowCount() > 0;
    }

    public function updateFamilielid($id, $naam, $geboortedatum, $omschrijving) {
        $stmt = $this->pdo->prepare("UPDATE familielid SET naam = ?, geboortedatum = ?, omschrijving = ? WHERE id = ?");
        $stmt->execute([$naam, $geboortedatum, $omschrijving, $id]);
        return $stmt->rowCount() > 0;
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
}
?>
