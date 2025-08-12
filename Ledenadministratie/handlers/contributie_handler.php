<?php
require_once __DIR__ . '/../models/Contributie.php';
require_once __DIR__ . '/../models/Boekjaar.php';
require_once __DIR__ . '/../models/Familielid.php';
require_once __DIR__ . '/../config/connection.php';

use models\Contributie;
use models\Boekjaar;
use config\Connection;

session_start();

// Controleer of gebruiker is ingelogd en penningmeester is
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'treasurer') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bereken_contributies') {
    $boekjaar = $_POST['boekjaar'] ?? null;
    
    if (!$boekjaar) {
        $_SESSION['message'] = 'Geen boekjaar geselecteerd.';
        $_SESSION['message_type'] = 'error';
        header('Location: ../index.php?action=dashboard_treasurer');
        exit;
    }
    
    try {
        // Haal alle familieleden op
        $conn = new Connection();
        $pdo = $conn->getConnection();
        
        $stmt = $pdo->prepare("SELECT id, stalling FROM familielid");
        $stmt->execute();
        $familieleden = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $contributies = [];
        
        // Genereer contributies voor elk familielid
        foreach ($familieleden as $familielid) {
            $familielid_contributies = Contributie::createContributies(
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
        
        // Sla contributies op in session voor weergave
        $_SESSION['berekende_contributies'] = $contributies;
        $_SESSION['geselecteerd_boekjaar'] = $boekjaar;
        $_SESSION['message'] = 'Contributies succesvol berekend voor boekjaar ' . $boekjaar;
        $_SESSION['message_type'] = 'success';
        
    } catch (Exception $e) {
        $_SESSION['message'] = 'Fout bij berekenen contributies: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
}

header('Location: ../index.php?action=dashboard_treasurer');
exit;
?>
