<div class="section">
    <h3>Gebruikers</h3>
    
    <!-- Add User Form -->
    <form method="POST" class="form-inline">
        <input type="hidden" name="action" value="add_user">
        <input type="text" name="username" placeholder="Gebruikersnaam" required>
        <input type="password" name="password" placeholder="Wachtwoord" required>
        <select name="role" required>
            <option value="">Selecteer rol</option>
            <option value="admin">Admin</option>
            <option value="secretary">Secretaris</option>
            <option value="treasurer">Penningmeester</option>
        </select>
        <input type="text" name="description" placeholder="Beschrijving">
        <button type="submit">Toevoegen</button>
    </form>

    <!-- Users Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Gebruikersnaam</th>
                <th>Rol</th>
                <th>Beschrijving</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Veiligheidscheck voor $users variabele
            if (isset($users) && is_array($users) && count($users) > 0): 
                foreach ($users as $user): 
            ?>
            <tr>
                <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($user['username'] ?? '') ?></td>
                <td><?= htmlspecialchars($user['role'] ?? '') ?></td>
                <td><?= htmlspecialchars($user['description'] ?? '') ?></td>
                <td>
                    <!-- Edit Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                        <select name="role" required>
                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="secretary" <?= ($user['role'] ?? '') === 'secretary' ? 'selected' : '' ?>>Secretaris</option>
                            <option value="treasurer" <?= ($user['role'] ?? '') === 'treasurer' ? 'selected' : '' ?>>Penningmeester</option>
                        </select>
                        <input type="text" name="description" value="<?= htmlspecialchars($user['description'] ?? '') ?>">
                        <button type="submit">Bijwerken</button>
                    </form>
                    
                    <!-- Delete Form - Hidden for admin's own account -->
                    <?php if ($user['username'] !== $currentUsername): ?>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">
                        <button type="submit" class="delete-btn">Verwijderen</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php 
                endforeach; 
            else: 
            ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">
                    <?php if (!isset($users)): ?>
                        Fout: Gebruikersdata niet geladen
                    <?php elseif (!is_array($users)): ?>
                        Fout: Ongeldige gebruikersdata
                    <?php else: ?>
                        Geen gebruikers gevonden
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>