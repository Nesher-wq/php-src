<?php // login.php
// Controleer of sessie al actief is voordat we session_start() aanroepen
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

  $host = 'localhost';    // Change as necessary
  $database = 'rolapplicatie'; // Change as necessary
  $dbuser = 'root';         // Change as necessary
  $pass = 'mysql';        // Change as necessary
  $chrs = 'utf8mb4';
  $attr = "mysql:host=$host;dbname=$database;charset=$chrs";
  $opts =
  [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];

// Alleen login logica uitvoeren als dit een POST request is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST request ontvangen!<br>";
    
    // Stap 2: Controleer of de benodigde velden aanwezig zijn
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password) . "<br>";
        
        // Stap 3: Database controle implementeren
        try {
            // Maak database connectie
            $pdo = new PDO($attr, $dbuser, $pass, $opts);
            
            // Zoek gebruiker in database met prepared statement (veilig tegen SQL-injectie)
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            // Controleer of gebruiker bestaat
            if ($stmt->rowCount() > 0) {
                $user_data = $stmt->fetch();
                echo "Gebruiker gevonden in database!<br>";
                echo "Database password: " . htmlspecialchars($user_data['password']) . "<br>";
                echo "Ingevoerde password: " . htmlspecialchars($password) . "<br>";
                
                // Controleer wachtwoord (simpele vergelijking voor nu)
                if ($password === $user_data['password']) {
                    // Stap 4: Sessie management - sla gebruikersgegevens op
                    $_SESSION['user_id'] = $user_data['id'];
                    $_SESSION['username'] = $user_data['username'];
                    $_SESSION['role'] = $user_data['role'];
                    $_SESSION['logged_in'] = true;
                    
                    echo "<strong>Login succesvol!</strong><br>";
                    echo "Welkom " . htmlspecialchars($user_data['username']) . "!<br>";
                    echo "Je rol is: " . htmlspecialchars($user_data['role']) . "<br>";
                    echo "Sessie gestart!<br>";
                    
                    // Redirect naar index.php (over 2 seconden)
                    echo "<p>Je wordt doorgestuurd naar het dashboard...</p>";
                    echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
                    
                } else {
                    echo "<strong>Fout: Wachtwoord klopt niet!</strong><br>";
                }
            } else {
                echo "<strong>Fout: Gebruiker niet gevonden!</strong><br>";
            }
            
        } catch (PDOException $e) {
            echo "Database fout: " . htmlspecialchars($e->getMessage()) . "<br>";
        }
        
    } else {
        echo "Fout: Username en/of password ontbreken<br>";
    }
} else {
    // Alleen redirect als dit bestand direct wordt aangeroepen
    if (basename($_SERVER['PHP_SELF']) === 'login.php') {
        header('Location: index.php');
        exit;
    }
}
?>