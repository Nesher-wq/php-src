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
        $stmt = $this->pdo->prepare("INSERT INTO familie (naam, straat, huisnummer, postcode, woonplaats) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats]);
        return $stmt->rowCount() > 0;
    }

    public function getFamilieById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM familie WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateFamilie($id, $naam, $straat, $huisnummer, $postcode, $woonplaats) {
        $stmt = $this->pdo->prepare("UPDATE familie SET naam = ?, straat = ?, huisnummer = ?, postcode = ?, woonplaats = ? WHERE id = ?");
        $stmt->execute([$naam, $straat, $huisnummer, $postcode, $woonplaats, $id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteFamilie($id) {
        $stmt = $this->pdo->prepare("DELETE FROM familie WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
