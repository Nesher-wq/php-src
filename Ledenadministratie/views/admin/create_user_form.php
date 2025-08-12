<div class="section">
    <h3>Nieuwe gebruiker aanmaken</h3>
    <form method="POST" action="">
        <div class="form-group">
            <label for="new_username">Username:</label>
            <input type="text" id="new_username" name="new_username" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">Password:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        
        <div class="form-group">
            <label for="new_role">Rol:</label>
            <select id="new_role" name="new_role" required>
                <?php 
                $all_roles = array_unique(array_merge($allowed_roles, ['secretary']));
                foreach ($all_roles as $role): ?>
                    <option value="<?php echo $role; ?>"><?php echo ucfirst($role); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="new_description">Beschrijving (optioneel):</label>
            <input type="text" id="new_description" name="new_description">
        </div>
        
        <button type="submit" name="create_user" class="btn">Aanmaken</button>
    </form>
</div>
