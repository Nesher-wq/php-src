<?php
if (!defined('INCLUDED_FROM_INDEX')) {
    http_response_code(403);
    exit('Direct access not allowed.');
}
?>

<h4>Familielid bewerken</h4>
<form method="POST" action="/Ledenadministratie/index.php" class="form">
    <input type="hidden" name="action" value="edit_familielid">
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
        <label for="soort_familielid">Soort familielid (optional):</label>
        <input type="text" id="soort_familielid" name="soort_familielid" value="<?php echo htmlspecialchars($edit_familielid['soort_familielid']); ?>" placeholder="bijv. vader, moeder, zoon, dochter">
    </div>
    <div class="form-group">
        <label for="stalling">Stalling (aantal boten, 0-3):</label>
        <input type="number" id="stalling" name="stalling" min="0" max="3" value="<?php 
            $stallingValue = 0;
            if (isset($edit_familielid['stalling'])) {
                $stallingValue = $edit_familielid['stalling'];
            }
            echo htmlspecialchars($stallingValue); 
        ?>" required>
    </div>
    <button type="submit" name="update_familielid" class="btn">Bijwerken</button>
    <button type="submit" name="cancel_edit_familielid" class="btn btn-secondary">Annuleren</button>
</form>
