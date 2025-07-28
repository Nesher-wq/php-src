<?php
/**
 * HOOFDBESTAND VAN DE ROL-GEBASEERDE APPLICATIE
 * 
 * Dit bestand bevat:
 * - Database setup en connectie
 * - Gebruikers authenticatie controle
 * - Admin functionaliteit (gebruikers aanmaken/bewerken)
 * - User functionaliteit (wachtwoord wijzigen)
 * - Dashboard weergave op basis van gebruikersrol
 */

// =====================================================
// SESSIE MANAGEMENT
// =====================================================
// Start sessie alleen als er nog geen actieve sessie is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =====================================================
// CONFIGURATIE EN AFHANKELIJKHEDEN
// =====================================================
// Importeer database connectie
require_once 'connection.php';

// =====================================================
// DATABASE CONNECTIE EN INITIALISATIE
// =====================================================
// Maak database connectie en initialiseer $pdo
$conn = new Connection();
$pdo = $conn->checkAndCreateDatabase();

if (!$pdo) {
    logMessage("Database connectie mislukt!");
    echo "Database connectie mislukt! Controleer de logs.";
    exit;
}

// =====================================================
// LOGOUT FUNCTIONALITEIT
// =====================================================
// Logout functionaliteit
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// =====================================================
// LOGGING FUNCTIE
// =====================================================
/**
 * Schrijft een bericht naar het logbestand met timestamp
 * @param string $message Het bericht om te loggen
 */
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents('app.log', $logEntry, FILE_APPEND | LOCK_EX);
}


// =====================================================
// APPLICATIE LOGICA
// =====================================================
// $pdo is hierboven geïnitialiseerd
if ($pdo) {   
    // =====================================================
    // GEBRUIKERS AUTHENTICATIE CONTROLE
    // =====================================================
    // Controleer of gebruiker is ingelogd
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        
        // =====================================================
        // ADMIN FORM PROCESSING
        // =====================================================
        // Admin form handling
        if ($_SESSION['role'] === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // =====================================================
            // ADMIN FUNCTIE: NIEUWE GEBRUIKER AANMAKEN
            // =====================================================
            // User aanmaken
            if (isset($_POST['create_user'])) {
                $new_username = trim($_POST['new_username']);
                $new_password = trim($_POST['new_password']);
                
                // Valideer invoer
                if (!empty($new_username) && !empty($new_password)) {
                    try {
                        // Controleer of username al bestaat
                        $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
                        $stmt->execute([$new_username]);
                        
                        if ($stmt->rowCount() > 0) {
                            // Username bestaat al
                            $message = "Fout: Gebruikersnaam '$new_username' bestaat al!";
                            $message_type = "error";
                        } else {
                            // Hash het wachtwoord veilig
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            
                            // Maak nieuwe user aan (standaard rol: user)
                            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
                            $stmt->execute([$new_username, $hashed_password]);
                            
                            // Succesbericht
                            $message = "Gebruiker '$new_username' succesvol aangemaakt!";
                            $message_type = "success";
                            logMessage("Admin '{$_SESSION['username']}' heeft gebruiker '$new_username' aangemaakt.");
                        }
                    } catch (PDOException $e) {
                        // Database fout
                        $message = "Database fout: " . $e->getMessage();
                        $message_type = "error";
                    }
                } else {
                    // Validatie fout
                    $message = "Fout: Alle velden zijn verplicht!";
                    $message_type = "error";
                }
            }
            
            // =====================================================
            // ADMIN FUNCTIE: GEBRUIKER WACHTWOORD BIJWERKEN
            // =====================================================
            // User bijwerken
            if (isset($_POST['edit_user'])) {
                $edit_username = trim($_POST['edit_username']);
                $edit_password = trim($_POST['edit_password']);
                
                // Valideer invoer
                if (!empty($edit_username) && !empty($edit_password)) {
                    try {
                        // Controleer of user bestaat
                        $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
                        $stmt->execute([$edit_username]);
                        
                        if ($stmt->rowCount() == 0) {
                            // Gebruiker niet gevonden
                            $message = "Fout: Gebruiker '$edit_username' niet gevonden!";
                            $message_type = "error";
                        } else {
                            // Hash het nieuwe wachtwoord veilig
                            $hashed_password = password_hash($edit_password, PASSWORD_DEFAULT);
                            
                            // Update user password
                            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                            $stmt->execute([$hashed_password, $edit_username]);
                            
                            // Succesbericht
                            $message = "Wachtwoord voor gebruiker '$edit_username' succesvol bijgewerkt!";
                            $message_type = "success";
                            logMessage("Admin '{$_SESSION['username']}' heeft wachtwoord van '$edit_username' bijgewerkt.");
                        }
                    } catch (PDOException $e) {
                        // Database fout
                        $message = "Database fout: " . $e->getMessage();
                        $message_type = "error";
                    }
                } else {
                    // Validatie fout
                    $message = "Fout: Alle velden zijn verplicht!";
                    $message_type = "error";
                }
            }
        }
        
        // =====================================================
        // USER FORM PROCESSING (GEWONE GEBRUIKERS)
        // =====================================================
        // User form handling (voor gewone gebruikers)
        if ($_SESSION['role'] === 'user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // =====================================================
            // USER FUNCTIE: EIGEN WACHTWOORD WIJZIGEN
            // =====================================================
            // Wachtwoord wijzigen
            if (isset($_POST['change_password'])) {
                $current_password = trim($_POST['current_password']);
                $new_user_password = trim($_POST['new_user_password']);
                $confirm_password = trim($_POST['confirm_password']);
                
                // Valideer dat alle velden ingevuld zijn
                if (!empty($current_password) && !empty($new_user_password) && !empty($confirm_password)) {
                    // Controleer of nieuwe wachtwoorden overeenkomen
                    if ($new_user_password !== $confirm_password) {
                        $message = "Fout: Nieuwe wachtwoorden komen niet overeen!";
                        $message_type = "error";
                    } else {
                        try {
                            // Haal huidige gebruiker op uit database
                            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
                            $stmt->execute([$_SESSION['username']]);
                            $user_data = $stmt->fetch();
                            
                            // Controleer of huidig wachtwoord klopt
                            if (password_verify($current_password, $user_data['password'])) {
                                // Hash het nieuwe wachtwoord
                                $hashed_new_password = password_hash($new_user_password, PASSWORD_DEFAULT);
                                
                                // Update wachtwoord in database
                                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                                $stmt->execute([$hashed_new_password, $_SESSION['username']]);
                                
                                // Succesbericht
                                $message = "Wachtwoord succesvol gewijzigd!";
                                $message_type = "success";
                                logMessage("User '{$_SESSION['username']}' heeft eigen wachtwoord gewijzigd.");
                            } else {
                                // Huidig wachtwoord incorrect
                                $message = "Fout: Huidig wachtwoord is incorrect!";
                                $message_type = "error";
                            }
                        } catch (PDOException $e) {
                            // Database fout
                            $message = "Database fout: " . $e->getMessage();
                            $message_type = "error";
                        }
                    }
                } else {
                    // Validatie fout
                    $message = "Fout: Alle velden zijn verplicht!";
                    $message_type = "error";
                }
            }
        }
        
        // =====================================================
        // HTML DASHBOARD WEERGAVE
        // =====================================================
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
                    font-family: -apple-system, system-ui, 'Segoe UI', Roboto, sans-serif;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 0;
                }
                
                .navbar {
                    background-color: #333;
                    color: white;
                    padding: 15px 20px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .navbar h1 {
                    margin: 0;
                    font-size: 18px;
                }
                
                .navbar a {
                    color: white;
                    text-decoration: none;
                    padding: 8px 16px;
                    border-radius: 4px;
                    background-color: #dc3545;
                }
                
                .navbar a:hover {
                    background-color: #c82333;
                }
                
                .container {
                    max-width: 800px;
                    margin: 20px auto;
                    padding: 0 20px;
                }
                
                .welcome-section {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                    margin-bottom: 20px;
                }
                
                .section {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                    margin-bottom: 20px;
                }
                
                .section h3 {
                    margin-top: 0;
                    margin-bottom: 15px;
                    color: #333;
                }
                
                .form-group {
                    margin-bottom: 15px;
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    color: #555;
                    font-weight: 500;
                }
                
                .form-group input {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 14px;
                    box-sizing: border-box;
                }
                
                .form-group input:focus {
                    outline: none;
                    border-color: #4a90e2;
                }
                
                .btn {
                    background-color: #333;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    width: 100%;
                }
                
                .btn:hover {
                    background-color: #555;
                }
                
                .btn-edit {
                    background-color: #666;
                }
                
                .btn-edit:hover {
                    background-color: #888;
                }
                
                .message {
                    padding: 12px;
                    border-radius: 4px;
                    margin-bottom: 20px;
                    font-weight: 500;
                }
                
                .message.success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                
                .message.error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
            </style>
        </head>
        <body>
            <nav class="navbar">
                <h1>Dashboard</h1>
                <a href="?logout=1">Logout</a>
            </nav>
            
            <div class="container">
                <div class="welcome-section">
                    <h2>Hallo, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                </div>
                
                <?php if (isset($message)): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <!-- Admin functionaliteit -->
                    <div class="section">
                        <h3>User aanmaken</h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="new_username">Username:</label>
                                <input type="text" id="new_username" name="new_username" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">Password:</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>
                            <button type="submit" name="create_user" class="btn">Create</button>
                        </form>
                    </div>
                    
                    <div class="section">
                        <h3>User bijwerken</h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="edit_username">Username:</label>
                                <input type="text" id="edit_username" name="edit_username" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_password">New Password:</label>
                                <input type="password" id="edit_password" name="edit_password" required>
                            </div>
                            <button type="submit" name="edit_user" class="btn btn-edit">Edit</button>
                        </form>
                    </div>

                    <div class="section">
                        <h3>Users</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Username</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT username, role FROM users ORDER BY role, username ASC");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['role']) . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                <?php else: ?>
                    <!-- User functionaliteit -->
                    <div class="section">
                        <h3>Wachtwoord wijzigen</h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="current_password">Huidig wachtwoord:</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_user_password">Nieuw wachtwoord:</label>
                                <input type="password" id="new_user_password" name="new_user_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Bevestig nieuw wachtwoord:</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn">Wachtwoord wijzigen</button>
                        </form>
                    </div>
                <?php endif; ?>
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
                    font-family: -apple-system, system-ui, 'Segoe UI', Roboto, sans-serif;
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