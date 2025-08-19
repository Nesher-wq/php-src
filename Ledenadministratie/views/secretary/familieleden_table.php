<h3>Familieleden</h3>
<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Geboortedatum</th>
            <th>Soort</th>
            <th>Stalling</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php if (isset($edit_familie['familieleden']) && is_array($edit_familie['familieleden'])): ?>
            <?php foreach ($edit_familie['familieleden'] as $familielid): ?>
                <tr>
                    <td><?= htmlspecialchars($familielid['naam']) ?></td>
                    <td><?= htmlspecialchars($familielid['geboortedatum']) ?></td>
                    <td><?= htmlspecialchars($familielid['soort_familielid']) ?></td>
                    <td><?= htmlspecialchars($familielid['stalling']) ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="edit_familielid">
                            <input type="hidden" name="edit_familielid_id" value="<?= $familielid['id'] ?>">
                            <input type="hidden" name="edit_familie_id" value="<?= $edit_familie['id'] ?>">
                            <button type="submit" name="edit_familielid" class="action-btn edit-btn">Bewerken</button>
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Weet u zeker dat u dit familielid wilt verwijderen?');">
                            <input type="hidden" name="action" value="delete_familielid">
                            <input type="hidden" name="delete_familielid_id" value="<?= $familielid['id'] ?>">
                            <input type="hidden" name="familie_id" value="<?= $edit_familie['id'] ?>">
                            <button type="submit" name="delete_familielid" class="action-btn delete-btn">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align: center;">Geen familieleden gevonden</td></tr>
        <?php endif; ?>
    </tbody>
</table>
