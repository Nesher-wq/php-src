<?php
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
                        <th>Aantal leden</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                require_once __DIR__ . '/../controllers/FamilieController.php';
                $familieController = new FamilieController($pdo);
                foreach ($familieController->getAllFamilies() as $familie): 
                    // Tel aantal familieleden via familielid tabel
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM familielid WHERE familie_id = ?");
                    $stmt->execute([$familie['id']]);
                    $aantal_leden = $stmt->fetchColumn();
                ?>
                    <tr>
                        <td><?php echo $familie['id']; ?></td>
                        <td><?php echo htmlspecialchars($familie['naam']); ?></td>
                        <td><?php echo htmlspecialchars($familie['straat']); ?></td>
                        <td><?php echo htmlspecialchars($familie['huisnummer']); ?></td>
                        <td><?php echo htmlspecialchars($familie['postcode']); ?></td>
                        <td><?php echo htmlspecialchars($familie['woonplaats']); ?></td>
                        <td><?php echo $aantal_leden; ?></td>
                        <td class="action-cell">
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="edit_familie_id" value="<?php echo $familie['id']; ?>">
                                <button type="submit" name="edit_familie" class="action-btn edit-btn">Bewerken</button>
                            </form>
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Weet u zeker dat u deze familie wilt verwijderen?');">
                                <input type="hidden" name="delete_familie_id" value="<?php echo $familie['id']; ?>">
                                <button type="submit" name="delete_familie" class="action-btn delete-btn">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (isset($edit_familie)): ?>
                <h4>Familie bewerken</h4>
                <form method="POST" action="" class="form">
                    <input type="hidden" name="familie_id" value="<?php echo $edit_familie['id']; ?>">
                    <div class="form-group">
                        <label for="familie_naam">Naam:</label>
                        <input type="text" id="familie_naam" name="familie_naam" value="<?php echo htmlspecialchars($edit_familie['naam']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_straat">Straat:</label>
                        <input type="text" id="familie_straat" name="familie_straat" value="<?php echo htmlspecialchars($edit_familie['straat']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_huisnummer">Huisnummer:</label>
                        <input type="text" id="familie_huisnummer" name="familie_huisnummer" value="<?php echo htmlspecialchars($edit_familie['huisnummer']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_postcode">Postcode:</label>
                        <input type="text" id="familie_postcode" name="familie_postcode" value="<?php echo htmlspecialchars($edit_familie['postcode']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_woonplaats">Woonplaats:</label>
                        <input type="text" id="familie_woonplaats" name="familie_woonplaats" value="<?php echo htmlspecialchars($edit_familie['woonplaats']); ?>" required>
                    </div>
                    <button type="submit" name="update_familie" class="btn">Bijwerken</button>
                    <button type="submit" name="cancel_edit" class="btn btn-secondary">Annuleren</button>
                </form>

                <h4>Familieleden</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naam</th>
                            <th>Geboortedatum</th>
                            <th>Omschrijving</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM familielid WHERE familie_id = ?");
                    $stmt->execute([$edit_familie['id']]);
                    $familieleden = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($familieleden as $familielid): 
                    ?>
                        <tr>
                            <td><?php echo $familielid['id']; ?></td>
                            <td><?php echo htmlspecialchars($familielid['naam']); ?></td>
                            <td><?php echo htmlspecialchars($familielid['geboortedatum']); ?></td>
                            <td><?php echo htmlspecialchars($familielid['omschrijving']); ?></td>
                            <td class="action-cell">
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="edit_familielid_id" value="<?php echo $familielid['id']; ?>">
                                    <input type="hidden" name="edit_familie_id" value="<?php echo $edit_familie['id']; ?>">
                                    <button type="submit" name="edit_familielid" class="action-btn edit-btn">Bewerken</button>
                                </form>
                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Weet u zeker dat u dit familielid wilt verwijderen?');">
                                    <input type="hidden" name="delete_familielid_id" value="<?php echo $familielid['id']; ?>">
                                    <input type="hidden" name="familie_id" value="<?php echo $edit_familie['id']; ?>">
                                    <button type="submit" name="delete_familielid" class="action-btn delete-btn">Verwijderen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (isset($edit_familielid)): ?>
                    <h4>Familielid bewerken</h4>
                    <form method="POST" action="" class="form">
                        <input type="hidden" name="familielid_id" value="<?php echo $edit_familielid['id']; ?>">
                        <input type="hidden" name="familie_id" value="<?php echo $edit_familie['id']; ?>">
                        <div class="form-group">
                            <label for="familielid_naam">Naam:</label>
                            <input type="text" id="familielid_naam" name="familielid_naam" value="<?php echo htmlspecialchars($edit_familielid['naam']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="familielid_geboortedatum">Geboortedatum:</label>
                            <input type="date" id="familielid_geboortedatum" name="familielid_geboortedatum" value="<?php echo htmlspecialchars($edit_familielid['geboortedatum']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="familielid_omschrijving">Omschrijving:</label>
                            <input type="text" id="familielid_omschrijving" name="familielid_omschrijving" value="<?php echo htmlspecialchars($edit_familielid['omschrijving']); ?>">
                        </div>
                        <button type="submit" name="update_familielid" class="btn">Bijwerken</button>
                        <button type="submit" name="cancel_edit_familielid" class="btn btn-secondary">Annuleren</button>
                    </form>
                <?php else: ?>
                    <h4>Nieuw familielid toevoegen</h4>
                    <form method="POST" action="" class="form">
                        <input type="hidden" name="familie_id" value="<?php echo $edit_familie['id']; ?>">
                        <div class="form-group">
                            <label for="familielid_naam">Naam:</label>
                            <input type="text" id="familielid_naam" name="familielid_naam" required>
                        </div>
                        <div class="form-group">
                            <label for="familielid_geboortedatum">Geboortedatum:</label>
                            <input type="date" id="familielid_geboortedatum" name="familielid_geboortedatum" required>
                        </div>
                        <div class="form-group">
                            <label for="familielid_omschrijving">Omschrijving:</label>
                            <input type="text" id="familielid_omschrijving" name="familielid_omschrijving">
                        </div>
                        <button type="submit" name="add_familielid" class="btn">Toevoegen</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <!-- Form voor het toevoegen van een nieuwe familie -->
                <h4>Nieuwe familie toevoegen</h4>
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="familie_naam">Naam:</label>
                        <input type="text" id="familie_naam" name="familie_naam" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_straat">Straat:</label>
                        <input type="text" id="familie_straat" name="familie_straat" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_huisnummer">Huisnummer:</label>
                        <input type="text" id="familie_huisnummer" name="familie_huisnummer" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_postcode">Postcode:</label>
                        <input type="text" id="familie_postcode" name="familie_postcode" required>
                    </div>
                    <div class="form-group">
                        <label for="familie_woonplaats">Woonplaats:</label>
                        <input type="text" id="familie_woonplaats" name="familie_woonplaats" required>
                    </div>
                    <button type="submit" name="add_familie" class="btn">Toevoegen</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
