<?php

class ContributieController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function berekenContributies($boekjaar) {
        require_once __DIR__ . '/../models/Contributie.php';
        
        try {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set PDO connection in Contributie model
            \models\Contributie::setPDO($this->pdo);
            
            // Calculate contributions
            $success = \models\Contributie::createContributies($boekjaar);
            
            if ($success) {
                // Get the calculated contributions from session (set by the model)
                $contributies = $_SESSION['berekende_contributies'] ?? [];
                
                return [
                    'success' => true,
                    'message' => 'Contributies succesvol berekend voor boekjaar ' . $boekjaar,
                    'contributies' => $contributies
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Er is een fout opgetreden bij het berekenen van de contributies'
                ];
            }
            
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
