<?php 
/**
 * LOGIN EN DATABASE CONFIGURATIE BESTAND
 * 
 * Dit bestand bevat:
 * - Database configuratie instellingen
 * - Gebruikers authenticatie logica
 * - Sessie management na succesvol inloggen
 * - Redirect functionaliteit
 */

// =====================================================
// SESSIE MANAGEMENT
// =====================================================
// Controleer of sessie al actief is voordat we session_start() aanroepen
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =====================================================
// DATABASE CONNECTIE VIA Connection CLASS
// =====================================================
require_once 'connection.php';
$conn = new Connection();
$pdo = $conn->getConnection();

// =====================================================
// LOGIN FORM PROCESSING
// =====================================================

// Alleen login logica uitvoeren als dit een POST request is EN als het een login poging is
// Controleert ook dat het NIET gaat om admin/user formulieren (die hebben andere veldnamen)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['create_user']) && !isset($_POST['edit_user']) && !isset($_POST['change_password'])) {
    
    // =====================================================
    // STAP 1: INPUT VALIDATIE
    // =====================================================
    // Controleer of de benodigde velden aanwezig zijn
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        // =====================================================
        // STAP 2: DATABASE AUTHENTICATIE
        // =====================================================
        // Database controle implementeren
        try {
            // Maak database connectie via Connection class
            // ...existing code...
            
            // =====================================================
            // STAP 3: GEBRUIKER OPZOEKEN
            // =====================================================
            // Zoek gebruiker in database met prepared statement (veilig tegen SQL-injectie)
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            // =====================================================
            // STAP 4: GEBRUIKER VERIFICATIE
            // =====================================================
            // Controleer of gebruiker bestaat
            if ($stmt->rowCount() > 0) {
                $user_data = $stmt->fetch();
                
                // =====================================================
                // STAP 5: WACHTWOORD VERIFICATIE
                // =====================================================
                // Controleer wachtwoord met password_verify voor veilige hash verificatie
                if (password_verify($password, $user_data['password'])) {
                    
                    // =====================================================
                    // STAP 6: SESSIE INITIALISATIE
                    // =====================================================
                    // Sessie management - sla gebruikersgegevens op
                    $_SESSION['user_id'] = $user_data['id'];
                    $_SESSION['username'] = $user_data['username'];
                    $_SESSION['role'] = $user_data['role'];
                    $_SESSION['logged_in'] = true;
                    
                    // =====================================================
                    // STAP 7: REDIRECT NAAR DASHBOARD
                    // =====================================================
                    // Direct redirect naar dashboard (geen wachttijd meer)
                    header('Location: index.php');
                    exit;
                    
                } else {
                    // Wachtwoord is incorrect
                    echo "<strong>Fout: Wachtwoord klopt niet!</strong><br>";
                }
            } else {
                // Gebruiker niet gevonden in database
                echo "<strong>Fout: Gebruiker niet gevonden!</strong><br>";
            }
            
        } catch (PDOException $e) {
            // Database connectie of query fout
            echo "Database fout: " . htmlspecialchars($e->getMessage()) . "<br>";
        }
        
    } else {
        // Niet alle benodigde velden aanwezig
        echo "Fout: Username en/of password ontbreken<br>";
    }
} else {
    // =====================================================
    // REDIRECT FUNCTIONALITEIT
    // =====================================================
    // Alleen redirect als dit bestand direct wordt aangeroepen (niet via include/require)
    // Dit voorkomt ongewenste redirects wanneer login.php wordt geïncludeerd
    if (basename($_SERVER['PHP_SELF']) === 'login.php') {
        header('Location: index.php');
        exit;
    }
}
?>