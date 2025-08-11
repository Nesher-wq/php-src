<?php

namespace models;

// Directe toegang tot dit bestand blokkeren
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}

/*
Beschrijving van de entiteit 'Familielid':

    Id: uniek identificatienummer van het familielid.

    Naam: naam van het familielid.

    Geboortedatum: de geboortedatum van het familielid.

    Soort lid: de rol of status van het familielid binnen de familie, bijvoorbeeld vader, moeder, zoon, dochter, oom, tante, neef, nicht, etc.
    */

    class Familielid {
        private $id;
        private $naam;
        private $geboortedatum;
        private $omschrijving;

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

        public function getGeboortedatum() {
            return $this->geboortedatum;
        }

        public function setGeboortedatum($geboortedatum) {
            $this->geboortedatum = $geboortedatum;
        }

        public function getOmschrijving() {
            return $this->omschrijving;
        }

        public function setOmschrijving($omschrijving) {
            $this->omschrijving = $omschrijving;
        }
    }

?>