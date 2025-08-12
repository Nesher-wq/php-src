<h4>Familieleden</h4>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Geboortedatum</th>
            <th>Soort familielid</th>
            <th>Soort lid</th>
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
            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($familielid['geboortedatum']))); ?></td>
            <td><?php echo htmlspecialchars($familielid['soort_familielid']); ?></td>
            <td><?php 
                // Get soort lid description
                if (isset($familielid['soort_lid_id'])) {
                    $soortlidModel = new \models\Soortlid();
                    echo htmlspecialchars($soortlidModel->getOmschrijvingById($familielid['soort_lid_id']));
                }
            ?></td>
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
