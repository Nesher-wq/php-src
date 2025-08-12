<?php
// Dashboard voor penningmeester
require_once __DIR__ . '/../models/Boekjaar.php';
use models\Boekjaar;

$boekjaarModel = new Boekjaar();
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
        <div class="contributie-section" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
            <h3>Contributie Berekening</h3>
            <form method="POST" action="handlers/contributie_handler.php" style="display: flex; align-items: center; gap: 10px;">
                <input type="hidden" name="action" value="bereken_contributies">
                <label for="boekjaar">Boekjaar:</label>
                <select name="boekjaar" id="boekjaar" required>
                    <option value="">Selecteer boekjaar...</option>
                    <?php foreach ($boekjaren as $boekjaar): ?>
                        <option value="<?php echo htmlspecialchars($boekjaar->jaar); ?>" 
                                <?php echo (isset($_SESSION['geselecteerd_boekjaar']) && $_SESSION['geselecteerd_boekjaar'] == $boekjaar->jaar) ? 'selected' : ''; ?>>
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
