<?php
// views/dashboard_admin.php

// Haal gebruikers op uit de database
try {
    $stmt = $pdo->prepare("SELECT id, username, role, description FROM users ORDER BY username");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $users = array(); // Zorg voor een lege array als er een fout optreedt
}

// Debug informatie (tijdelijk)
error_log("Users fetched: " . print_r($users, true));

// Get current user info to prevent self-deletion
$currentUsername = '';
if (isset($_SESSION['username'])) {
    $currentUsername = $_SESSION['username'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">
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
            <h2>Hallo, <?php 
                $welcomeUsername = 'admin';
                if (isset($_SESSION['username'])) {
                    $welcomeUsername = $_SESSION['username'];
                }
                echo htmlspecialchars($welcomeUsername); 
            ?></h2>
        </div>
        
        <!-- Include users table - CORRIGEER HET PAD HIER -->
        <?php include __DIR__ . '/admin/users_table.php'; ?>
    </div>
</body>
</html>