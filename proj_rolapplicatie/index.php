<?php
// Start sessie alleen als er nog geen actieve sessie is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Importeer database configuratie
require_once 'login.php';

// Logout functionaliteit
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents('app.log', $logEntry, FILE_APPEND | LOCK_EX);
}

function checkAndCreateDatabase() {
    global $host, $database, $dbuser, $pass, $chrs, $attr, $opts;
    
    try {
        // Verbinden met MySQL server (nog zonder database)
        $pdo = new PDO("mysql:host=$host;charset=$chrs", $dbuser, $pass, $opts);
        
        // Controleer of database bestaat
        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
        $stmt->execute([$database]);
        
        if ($stmt->rowCount() == 0) {
            // Database bestaat niet, maak deze aan
            logMessage("Database '$database' bestaat niet, wordt aangemaakt.");
            $stmt = $pdo->prepare("CREATE DATABASE `$database`");
            $stmt->execute();
            logMessage("Database '$database' is succesvol aangemaakt.");
        } else {
            logMessage("Database '$database' bestaat al.");
        }
        
        // Nu verbinden met de database zoals gedefinieerd in login.php
        $pdo = new PDO($attr, $dbuser, $pass, $opts);
        
        // Controleer of tabel 'users' bestaat
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // Tabel bestaat niet, maak deze aan met createTable.sql
            $createTableSQL = file_get_contents('createTable.sql');
            
            if ($createTableSQL === false) {
                throw new Exception("Kan createTable.sql niet lezen");
            }
            
            $stmt = $pdo->prepare($createTableSQL);
            $stmt->execute();
            
            logMessage("Tabel 'users' is aangemaakt.");
        } else {
            logMessage("Tabel 'users' bestaat al.");
        }
        
        return $pdo;
        
    } catch(PDOException $e) {
        logMessage("Database fout: " . $e->getMessage());
        return false;
    }
}

// Roep de functie aan bij het starten van de applicatie
$pdo = checkAndCreateDatabase();

if ($pdo) {
    logMessage("Database connectie succesvol! Applicatie is klaar voor gebruik.");
    
    // Controleer of gebruiker is ingelogd
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // Toon dashboard
        ?>
        <!DOCTYPE html>
        <html lang="nl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard - Rol Applicatie</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 20px;
                }
                
                .dashboard {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    padding: 40px;
                }
                
                .user-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                }
                
                .logout-btn {
                    background-color: #dc3545;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-block;
                }
                
                .logout-btn:hover {
                    background-color: #c82333;
                }
            </style>
        </head>
        <body>
            <div class="dashboard">
                <h1>Dashboard</h1>
                
                <div class="user-info">
                    <h3>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                    <p><strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                </div>
                
                <h3>Functionaliteit op basis van rol:</h3>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <p>✅ Als admin heb je toegang tot alle functies</p>
                    <p>✅ Je kunt nieuwe gebruikers aanmaken</p>
                <?php else: ?>
                    <p>📋 Als gebruiker heb je beperkte toegang</p>
                <?php endif; ?>
                
                <br>
                <a href="?logout=1" class="logout-btn">Uitloggen</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        // Toon login formulier (bestaande code)
        ?>
        <!DOCTYPE html>
        <html lang="nl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Inloggen - Rol Applicatie</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                
                .login-container {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    padding: 40px;
                    width: 100%;
                    max-width: 400px;
                }
                
                .login-form {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }
                
                .input-field {
                    padding: 12px 16px;
                    border: 2px solid #e1e5e9;
                    border-radius: 6px;
                    font-size: 16px;
                    transition: border-color 0.3s ease;
                }
                
                .input-field:focus {
                    outline: none;
                    border-color: #4a90e2;
                }
                
                .input-field::placeholder {
                    color: #a0a0a0;
                }
                
                .login-button {
                    background-color: #4a90e2;
                    color: white;
                    padding: 12px;
                    border: none;
                    border-radius: 6px;
                    font-size: 16px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }
                
                .login-button:hover {
                    background-color: #357abd;
                }
                
                .login-button:active {
                    background-color: #2968a3;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <form class="login-form" method="POST" action="login.php">
                    <input type="text" name="username" class="input-field" placeholder="Username" required>
                    <input type="password" name="password" class="input-field" placeholder="Password" required>
                    <button type="submit" class="login-button">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
} else {
    logMessage("Database connectie mislukt!");
    echo "Database connectie mislukt! Controleer de logs.";
}

?>