<?php

// a 'familie' (English: family) is a group of people who share a common household
// contains properties Id, Naam, StraatEnHuisnummer, Postcode, Woonplaats en familieleden

namespace models;
use models\Familielid; // pending implementation

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
}
?>