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
