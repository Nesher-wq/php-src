<?php
if (!defined('INCLUDED_FROM_INDEX')) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

// Start de sessie als die nog niet is gestart
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Dashboard voor penningmeester
require_once __DIR__ . '/../models/Boekjaar.php';

$boekjaarModel = new models\Boekjaar();
$boekjaren = $boekjaarModel->getAllBoekjaren();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Penningmeester</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <h1>Dashboard (Penningmeester)</h1>
        <div class="nav-right">
            <a href="index.php?action=change_password" style="margin-right:10px;">Wachtwoord veranderen</a>
            <a href="?action=logout">Uitloggen</a>
        </div>
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

        <!-- Boekjaar selectie en contributie berekening -->
        <div class="contributie-section">
            <h3>Contributie Berekening</h3>
            <form method="POST" class="contributie-form">
                <input type="hidden" name="handler" value="contributie">
                <input type="hidden" name="action" value="bereken_contributies">
                <label for="boekjaar">Boekjaar:</label>
                <select name="boekjaar" id="boekjaar" required>
                    <option value="">Selecteer boekjaar...</option>
                    <?php foreach ($boekjaren as $boekjaar): ?>
                        <option value="<?php echo htmlspecialchars($boekjaar->jaar); ?>" 
                                <?php 
                                $isSelected = false;
                                if (isset($_SESSION['geselecteerd_boekjaar'])) {
                                    if ($_SESSION['geselecteerd_boekjaar'] == $boekjaar->jaar) {
                                        $isSelected = true;
                                    }
                                }
                                
                                if ($isSelected) {
                                    echo 'selected';
                                }
                                ?>>
                            <?php echo htmlspecialchars($boekjaar->jaar); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Bereken</button>
            </form>
        </div>

        <?php include __DIR__ . '/treasurer/familielid_table.php'; ?>
    </div>
</body>
</html>
