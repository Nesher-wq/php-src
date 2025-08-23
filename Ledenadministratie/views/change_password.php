<?php
// Pagina voor wachtwoord wijzigen
?><!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord wijzigen</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <h1>Wachtwoord wijzigen</h1>
        <a href="index.php">Terug naar dashboard</a>
    </nav>
    <div class="container">
        <div class="section">
            <?php if (isset($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="change_password">
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
    </div>
</body>
</html>
