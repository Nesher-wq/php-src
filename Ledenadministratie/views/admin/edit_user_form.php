<div class="section">
    <h3>Gebruiker bewerken</h3>
    
    <form method="POST" action="">
        <input type="hidden" name="edit_user_id" value="<?php echo $edit_user['id']; ?>">
        
        <div class="form-group">
            <label for="edit_username">Username:</label>
            <input type="text" id="edit_username" name="edit_username" 
                   value="<?php echo htmlspecialchars($edit_user['username']); ?>" 
                   <?php echo ($edit_user['username'] === 'admin') ? 'readonly' : 'required'; ?>>
            <?php if ($edit_user['username'] === 'admin'): ?>
            <small>De gebruikersnaam van de admin kan niet worden gewijzigd</small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="edit_password">Nieuw wachtwoord (leeg laten om ongewijzigd te houden):</label>
            <input type="password" id="edit_password" name="edit_password">
        </div>
        
        <div class="form-group">
            <label for="edit_role">Rol:</label>
            <select id="edit_role" name="edit_role" <?php echo ($edit_user['username'] === 'admin') ? 'disabled' : 'required'; ?>>
                <?php 
                foreach ($allowed_roles as $role): ?>
                    <option value="<?php echo $role; ?>" <?php echo ($edit_user['role'] === $role) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($role); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($edit_user['username'] === 'admin'): ?>
            <input type="hidden" name="edit_role" value="admin">
            <small>De rol van de admin kan niet worden gewijzigd</small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="edit_description">Beschrijving:</label>
            <input type="text" id="edit_description" name="edit_description" value="<?php echo htmlspecialchars($edit_user['description']); ?>">
        </div>
        
        <button type="submit" name="update_user" class="btn">Bijwerken</button>
        <button type="submit" name="cancel_edit" class="btn btn-secondary">Annuleren</button>
    </form>
</div>
