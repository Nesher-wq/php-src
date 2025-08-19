<?php
require_once __DIR__ . '/../../includes/utils.php';

// Get all families with member count
$stmt = $pdo->query("
    SELECT f.*, COUNT(fl.id) as aantal_leden 
    FROM familie f 
    LEFT JOIN familielid fl ON f.id = fl.familie_id 
    GROUP BY f.id, f.naam 
    ORDER BY f.naam
");
$families = $stmt->fetchAll(PDO::FETCH_ASSOC);
writeLog('Loaded families count: ' . count($families));
?>

<div class="section">
    <h3>Families</h3>
    <table>
        <thead>
            <tr>
                <th>Naam</th>
                <th>Adres</th>
                <th>Aantal leden</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($families as $familie): ?>
                <tr>
                    <td><?= htmlspecialchars($familie['naam']) ?></td>
                    <td>
                        <?= htmlspecialchars($familie['straat'] . ' ' . $familie['huisnummer']) ?><br>
                        <?= htmlspecialchars($familie['postcode'] . ' ' . $familie['woonplaats']) ?>
                    </td>
                    <td><?= htmlspecialchars($familie['aantal_leden']) ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="edit_family">
                            <input type="hidden" name="edit_familie_id" value="<?= $familie['id'] ?>">
                            <button type="submit" name="edit_familie" class="action-btn edit-btn">Bewerken</button>
                        </form>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="delete_family">
                            <input type="hidden" name="delete_familie_id" value="<?= $familie['id'] ?>">
                            <button type="submit" name="delete_familie" class="action-btn delete-btn" 
                                    onclick="return confirm('Weet je zeker dat je deze familie wilt verwijderen?')">
                                Verwijderen
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
