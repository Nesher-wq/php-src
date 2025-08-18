<?php

class ContributieController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function berekenContributies($boekjaar) {
        require_once __DIR__ . '/../models/Contributie.php';
        
        try {
            // Haal alle familieleden op
            $stmt = $this->pdo->prepare("SELECT id, stalling FROM familielid");
            $stmt->execute();
            $familieleden = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $contributies = [];
            
            // Genereer contributies voor elk familielid
            foreach ($familieleden as $familielid) {
                $familielid_contributies = \models\Contributie::createContributies(
                    $familielid['id'], 
                    $boekjaar, 
                    $familielid['stalling']
                );
                
                foreach ($familielid_contributies as $contributie) {
                    $contributies[] = [
                        'familielid_id' => $contributie->familielid_id,
                        'boekjaar' => $contributie->boekjaar,
                        'contributie_type' => $contributie->contributie_type,
                        'soort_lid' => $contributie->soort_lid,
                        'leeftijd' => $contributie->leeftijd,
                        'bedrag' => $contributie->getBedrag()
                    ];
                }
            }
            
            return [
                'success' => true,
                'contributies' => $contributies,
                'message' => 'Contributies succesvol berekend voor boekjaar ' . $boekjaar . 
                           ' (' . count($contributies) . ' contributies berekend)'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function validateAccess($session) {
        return isset($session['role']) && $session['role'] === 'treasurer';
    }
}

?>
