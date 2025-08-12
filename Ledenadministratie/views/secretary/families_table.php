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
        require_once __DIR__ . '/../../controllers/FamilieController.php';
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
</div>
