<?php
if (!defined('INCLUDED_FROM_INDEX')) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

// Dashboard voor secretaris
?><!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secretaris</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <h1>Dashboard (Secretaris)</h1>
        <div class="nav-right">
            <a href="index.php?action=change_password" style="margin-right:10px;">Wachtwoord aanpassen</a>
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
        
        <?php include __DIR__ . '/secretary/families_table.php'; ?>

        <?php if (isset($edit_familie)): ?>
            <?php include __DIR__ . '/secretary/edit_familie_form.php'; ?>
            <?php include __DIR__ . '/secretary/familieleden_table.php'; ?>
            
            <?php if (isset($edit_familielid)): ?>
                <?php include __DIR__ . '/secretary/edit_familielid_form.php'; ?>
            <?php else: ?>
                <?php include __DIR__ . '/secretary/add_familielid_form.php'; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php include __DIR__ . '/secretary/add_familie_form.php'; ?>
        <?php endif; ?>
    </div>
</body>
</html>