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
                    <td><?php 
                        $familieNaam = '';
                        if (isset($familie['naam'])) {
                            $familieNaam = $familie['naam'];
                        }
                        echo htmlspecialchars($familieNaam); 
                    ?></td>
                    <td>
                        <?php 
                        $familieStraat = '';
                        $familieHuisnummer = '';
                        if (isset($familie['straat'])) {
                            $familieStraat = $familie['straat'];
                        }
                        if (isset($familie['huisnummer'])) {
                            $familieHuisnummer = $familie['huisnummer'];
                        }
                        echo htmlspecialchars($familieStraat . ' ' . $familieHuisnummer); 
                        ?><br>
                        <?php 
                        $familiePostcode = '';
                        $familieWoonplaats = '';
                        if (isset($familie['postcode'])) {
                            $familiePostcode = $familie['postcode'];
                        }
                        if (isset($familie['woonplaats'])) {
                            $familieWoonplaats = $familie['woonplaats'];
                        }
                        echo htmlspecialchars($familiePostcode . ' ' . $familieWoonplaats); 
                        ?>
                    </td>
                    <td><?php 
                        $aantalLeden = '';
                        if (isset($familie['aantal_leden'])) {
                            $aantalLeden = $familie['aantal_leden'];
                        }
                        echo htmlspecialchars($aantalLeden); 
                    ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="edit_family">
                            <input type="hidden" name="edit_familie_id" value="<?php 
                                $familieIdForEdit = '';
                                if (isset($familie['id'])) {
                                    $familieIdForEdit = $familie['id'];
                                }
                                echo $familieIdForEdit; 
                            ?>">
                            <button type="submit" name="edit_familie" class="action-btn edit-btn">Bewerken</button>
                        </form>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="delete_family">
                            <input type="hidden" name="delete_familie_id" value="<?php 
                                $familieIdForDelete = '';
                                if (isset($familie['id'])) {
                                    $familieIdForDelete = $familie['id'];
                                }
                                echo $familieIdForDelete; 
                            ?>">
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
