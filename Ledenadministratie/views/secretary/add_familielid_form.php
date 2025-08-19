<?php
$locale = 'nl_NL'; // Hardcoded locale for simplicity
$dateFormat = ($locale === 'nl_NL') ? 'dd-mm-yyyy' : 'mm/dd/yyyy';
?>
<div class="form-container">
    <h4>Nieuw familielid toevoegen</h4>
    <form method="POST" action="/Ledenadministratie/index.php" class="form">
        <input type="hidden" name="action" value="add_familielid">
        <input type="hidden" name="familie_id" value="<?php echo $edit_familie['id']; ?>">
        <div class="form-group">
            <label for="familielid_naam">Naam:</label>
            <input type="text" id="familielid_naam" name="familielid_naam" required>
        </div>
        <div class="form-group">
            <label for="familielid_geboortedatum">Geboortedatum:</label>
            <input type="date" id="familielid_geboortedatum" name="familielid_geboortedatum" required>
            <small style="color: #666;">Formaat: dd-mm-yyyy</small>
        </div>
        <div class="form-group">
            <label for="soort_familielid_add">Soort familielid (optional):</label>
            <input type="text" id="soort_familielid_add" name="soort_familielid" placeholder="bijv. vader, moeder, zoon, dochter">
        </div>
        <div class="form-group">
            <label for="stalling_add">Stalling (aantal boten, 0-3):</label>
            <input type="number" id="stalling_add" name="stalling" min="0" max="3" value="0" required>
        </div>
        <button type="submit" name="add_familielid" class="btn">Toevoegen</button>
    </form>
</div>
