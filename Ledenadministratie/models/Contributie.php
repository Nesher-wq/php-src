<?php

namespace models;

class Contributie {
    public $id;
    public $familielid_id;
    public $leeftijd;
    public $soort_lid;
    private $bedrag;
    public $boekjaar;
    private $geboortedatum;
    public $contributie_type; // 'lidmaatschap' of 'stalling'

    public function __construct($familielid_id, $boekjaar, $contributie_type = 'lidmaatschap', $stalling_aantal = null) {
        if ($familielid_id && $boekjaar) {
            $this->familielid_id = $familielid_id;
            $this->boekjaar = $boekjaar;
            $this->contributie_type = $contributie_type;
            
            if ($contributie_type === 'stalling' && $stalling_aantal !== null) {
                // Voor stalling contributie
                $this->bedrag = $stalling_aantal * 50;
                $this->soort_lid = 'Stalling';
                $this->leeftijd = 0; // Niet relevant voor stalling
            } else {
                // Voor lidmaatschap contributie
                $this->initializeLidmaatschap();
            }
        }
    }
    
    private function initializeLidmaatschap() {
        // Haal geboortedatum op van het familielid
        require_once __DIR__ . '/../controllers/SoortlidController.php';
        require_once __DIR__ . '/../config/connection.php';
        
        $conn = new \config\Connection();
        $pdo = $conn->getConnection();
        
        $stmt = $pdo->prepare("SELECT geboortedatum FROM familielid WHERE id = ?");
        $stmt->execute([$this->familielid_id]);
        $familielid = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($familielid && $familielid['geboortedatum']) {
            $this->geboortedatum = $familielid['geboortedatum'];
            
            // Bepaal soort lid en leeftijd
            $controller = new \SoortlidController($pdo);
            $referentiedatum = $this->boekjaar . '-01-01';
            $soortlid = $controller->getSoortlidByLeeftijd($this->geboortedatum, $referentiedatum);
            
            if ($soortlid) {
                $this->soort_lid = $soortlid->omschrijving;
            }
            
            // Bereken en stel leeftijd in
            $this->setLeeftijd();
            
            // Stel basis bedrag in (dit zou uit een configuratie moeten komen)
            $this->bedrag = 100; // Basis contributie bedrag
        }
    }

    // Static factory method om contributies aan te maken voor een familielid
    public static function createContributies($familielid_id, $boekjaar, $stalling_aantal = 0) {
        $contributies = [];
        
        // Maak altijd een lidmaatschap contributie
        $contributies[] = new self($familielid_id, $boekjaar, 'lidmaatschap');
        
        // Maak een stalling contributie als er stallingen zijn
        if ($stalling_aantal > 0) {
            $contributies[] = new self($familielid_id, $boekjaar, 'stalling', $stalling_aantal);
        }
        
        return $contributies;
    }

    public function getBedrag(): float {
        if ($this->contributie_type === 'stalling') {
            return $this->bedrag; // Voor stalling is dit al het juiste bedrag
        }
        
        // Voor lidmaatschap contributie, pas kortingen toe
        $korting = 0;

        if ($this->soort_lid === 'Jeugd' && $this->leeftijd < 8) {
            $korting = 50;
        } elseif ($this->soort_lid === 'Aspirant' && $this->leeftijd >= 8 && $this->leeftijd <= 12) {
            $korting = 40;
        } elseif ($this->soort_lid === 'Junior' && $this->leeftijd >= 13 && $this->leeftijd <= 17) {
            $korting = 25;
        } elseif ($this->soort_lid === 'Senior' && $this->leeftijd >= 18 && $this->leeftijd <= 50) {
            $korting = 0;
        } elseif ($this->soort_lid === 'Oudere' && $this->leeftijd > 50) {
            $korting = 45;
        }

        return $this->bedrag - ($this->bedrag * $korting / 100);
    }

    public function setLeeftijd(): void {
        if ($this->geboortedatum && $this->boekjaar) {
            // Bereken leeftijd op 1 januari van het boekjaar
            $birth = new \DateTime($this->geboortedatum);
            $referenceDate = new \DateTime($this->boekjaar . '-01-01');
            $this->leeftijd = $referenceDate->diff($birth)->y;
        }
    }
}