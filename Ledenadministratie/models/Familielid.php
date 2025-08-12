<?php

namespace models;

// Directe toegang tot dit bestand blokkeren
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}

class Familielid {
    public $id;
    public $naam;
    public $geboortedatum;
    public $soort_familielid;
    public $soort_lid;
    public $stalling;
}

?>