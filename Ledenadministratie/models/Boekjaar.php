<?php

namespace models;
use config\Connection;

// Directe toegang tot dit bestand blokkeren
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}

class Boekjaar {
    public $id;
    public $jaar;

    public function __construct($id = null, $jaar = null) {
        $this->id = $id;
        $this->jaar = $jaar;
    }

    // Haal alle boekjaren op
    public function getAllBoekjaren() {
        require_once __DIR__ . '/../config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM boekjaar ORDER BY jaar DESC");
        $stmt->execute();

        $boekjaren = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $boekjaar = new Boekjaar();
            $boekjaar->id = $row['id'];
            $boekjaar->jaar = $row['jaar'];
            $boekjaren[] = $boekjaar;
        }

        return $boekjaren;
    }

    // Haal het huidige boekjaar op (het meest recente jaar)
    public function getHuidigBoekjaar() {
        require_once __DIR__ . '/../config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM boekjaar ORDER BY jaar DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            $boekjaar = new Boekjaar();
            $boekjaar->id = $result['id'];
            $boekjaar->jaar = $result['jaar'];
            return $boekjaar;
        }

        return null;
    }

    // Haal boekjaar op basis van jaar
    public function getBoekjaarByJaar($jaar) {
        require_once __DIR__ . '/../config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM boekjaar WHERE jaar = ?");
        $stmt->execute([$jaar]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            $boekjaar = new Boekjaar();
            $boekjaar->id = $result['id'];
            $boekjaar->jaar = $result['jaar'];
            return $boekjaar;
        }

        return null;
    }

    // Voeg een nieuw boekjaar toe
    public function addBoekjaar($jaar) {
        require_once __DIR__ . '/../config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("INSERT INTO boekjaar (jaar) VALUES (?)");
        return $stmt->execute([$jaar]);
    }

    // Controleer of een boekjaar bestaat
    public function boekjaarExists($jaar) {
        require_once __DIR__ . '/../config/connection.php';
        $conn = new Connection();
        $pdo = $conn->getConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM boekjaar WHERE jaar = ?");
        $stmt->execute([$jaar]);
        return $stmt->fetchColumn() > 0;
    }
}

?>
