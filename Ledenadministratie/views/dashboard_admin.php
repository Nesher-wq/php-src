<?php
// Dashboard voor admin
?><!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-right {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Dashboard (Admin)</h1>
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
        
        <?php include __DIR__ . '/admin/users_table.php'; ?>
        
        <?php if (isset($edit_user)): ?>
            <?php include __DIR__ . '/admin/edit_user_form.php'; ?>
        <?php else: ?>
            <?php include __DIR__ . '/admin/create_user_form.php'; ?>
        <?php endif; ?>
    </div>
</body>
</html>