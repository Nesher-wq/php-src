<?php
// Dashboard voor treasurer
?><!DOCTYPE html>
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
        <a href="index.php?action=change_password" style="margin-right:10px;">Change password</a>
        <a href="?action=logout">Logout</a>
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

        <div class="section">
            <h3>Families</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                        <th>Straat</th>
                        <th>Huisnummer</th>
                        <th>Postcode</th>
                        <th>Woonplaats</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                require_once __DIR__ . '/../controllers/FamilieController.php';
                $familieController = new FamilieController($pdo);
                foreach ($familieController->getAllFamilies() as $familie): ?>
                    <tr>
                        <td><?php echo $familie['id']; ?></td>
                        <td><?php echo htmlspecialchars($familie['naam']); ?></td>
                        <td><?php echo htmlspecialchars($familie['straat']); ?></td>
                        <td><?php echo htmlspecialchars($familie['huisnummer']); ?></td>
                        <td><?php echo htmlspecialchars($familie['postcode']); ?></td>
                        <td><?php echo htmlspecialchars($familie['woonplaats']); ?></td>
                        <td class="action-cell">
                            <form method="POST" action="">
                                <input type="hidden" name="edit_familie_id" value="<?php echo $familie['id']; ?>">
                                <button type="submit" name="edit_familie" class="action-btn edit-btn">Bewerken</button>
                            </form>
                            <form method="POST" action="" onsubmit="return confirm('Weet u zeker dat u deze familie wilt verwijderen?');">
                                <input type="hidden" name="delete_familie_id" value="<?php echo $familie['id']; ?>">
                                <button type="submit" name="delete_familie" class="action-btn delete-btn">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
