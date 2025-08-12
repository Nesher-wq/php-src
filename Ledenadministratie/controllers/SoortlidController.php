<?php
// Controller voor soortlid beheer
require_once __DIR__ . '/../models/Soortlid.php';

class SoortlidController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllSoortleden() {
        $stmt = $this->pdo->prepare("SELECT * FROM soortlid ORDER BY omschrijving ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSoortlidById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM soortlid WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSoortlidByLeeftijd($geboortedatum, $datum = null) {
        // Als datum niet is opgegeven, gebruik de huidige datum
        if ($datum === null) {
            $datum = date('Y-m-d');
        }

        // Bereken leeftijd op de opgegeven datum
        $birth = new \DateTime($geboortedatum);
        $referenceDate = new \DateTime($datum);
        $leeftijd = $referenceDate->diff($birth)->y;

        $stmt = $this->pdo->prepare("SELECT * FROM soortlid WHERE minimum_leeftijd <= ? AND (maximum_leeftijd >= ? OR maximum_leeftijd IS NULL) LIMIT 1");
        $stmt->execute([$leeftijd, $leeftijd]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            require_once __DIR__ . '/../models/Soortlid.php';
            $soortlid = new \models\Soortlid();
            $soortlid->id = $result['id'];
            $soortlid->omschrijving = $result['omschrijving'];
            $soortlid->minimum_leeftijd = $result['minimum_leeftijd'];
            $soortlid->maximum_leeftijd = $result['maximum_leeftijd'];
            return $soortlid;
        }

        return null;
    }
}
?>
