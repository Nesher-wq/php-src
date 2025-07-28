<?php

// a 'familie' (English: family) is a group of people who share a common household
// contains properties Id, Naam, StraatEnHuisnummer, Postcode, Woonplaats en familieleden

namespace models;
use models\Familielid; // pending implementation

class Familie {
    private $id;
    private $naam;
    private $straat;
    private $huisnummer;
    private $postcode;
    private $woonplaats;
    private $familieleden = [];

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getNaam() {
        return $this->naam;
    }
    public function setNaam($naam) {
        $this->naam = $naam;
    }

    public function getStraat() {
        return $this->straat;
    }
    public function setStraat($straat) {
        $this->straat = $straat;
    }

    public function getHuisnummer() {
        return $this->huisnummer;
    }
    public function setHuisnummer($huisnummer) {
        $this->huisnummer = $huisnummer;
    }

    public function getPostcode() {
        return $this->postcode;
    }
    public function setPostcode($postcode) {
        $this->postcode = $postcode;
    }

    public function getWoonplaats() {
        return $this->woonplaats;
    }
    public function setWoonplaats($woonplaats) {
        $this->woonplaats = $woonplaats;
    }

    public function voegFamilielidToe(Familielid $lid) {
        $this->familieleden[] = $lid;
    }

    public function getFamilieleden() {
        return $this->familieleden;
    }
}
?>