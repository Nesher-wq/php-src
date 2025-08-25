<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Inloggen</h2>
        <?php if (isset($error_message) && !empty(trim($error_message))): ?>
            <div class="message error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($success_message) && !empty(trim($success_message))): ?>
            <div class="message success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/Ledenadministratie/controllers/AuthHandler.php" class="login-form">
            <input type="text" name="username" class="input-field" placeholder="Gebruikersnaam" required>
            <input type="password" name="password" class="input-field" placeholder="Wachtwoord" required>
            <button type="submit" class="login-button">Inloggen</button>
        </form>
    </div>
</body>
</html>