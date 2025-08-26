<div class="section">    
    <?php if (isset($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Add User Form -->
     <h4>Gebruiker toevoegen</h4>
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
     <h3>Gebruikers</h3>
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
            $usersExist = false;
            $usersIsArray = false;
            $usersHasData = false;
            
            if (isset($users)) {
                $usersExist = true;
                if (is_array($users)) {
                    $usersIsArray = true;
                    if (count($users) > 0) {
                        $usersHasData = true;
                    }
                }
            }
            
            $shouldShowUsers = false;
            if ($usersExist && $usersIsArray && $usersHasData) {
                $shouldShowUsers = true;
            }
            
            if ($shouldShowUsers): 
                foreach ($users as $user): 
            ?>
            <tr>
                <td><?php 
                    echo htmlspecialchars($user['id'] ?? ''); 
                ?></td>
                <td><?php 
                    echo htmlspecialchars($user['username'] ?? ''); 
                ?></td>
                <td><?php 
                    echo htmlspecialchars($user['role'] ?? ''); 
                ?></td>
                <td><?php 
                    echo htmlspecialchars($user['description'] ?? ''); 
                ?></td>
                <td>
                    <!-- Edit Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" value="<?php 
                            $userIdForForm = '';
                            if (isset($user['id'])) {
                                $userIdForForm = $user['id'];
                            }
                            echo htmlspecialchars($userIdForForm); 
                        ?>">
                        <input type="text" name="username" value="<?php 
                            echo htmlspecialchars($user['username'] ?? ''); 
                        ?>" required style="width: 240px;">
                        <input type="password" name="password" placeholder="Nieuw wachtwoord" style="width: 240px;">
                        <select name="role" required style="width: 240px;">
                            <option value="admin" <?php 
                                $userRoleFromData = $user['role'] ?? '';
                                if ($userRoleFromData === 'admin') {
                                    echo 'selected';
                                }
                            ?>>Admin</option>
                            <option value="secretary" <?php 
                                if ($userRoleFromData === 'secretary') {
                                    echo 'selected';
                                }
                            ?>>Secretaris</option>
                            <option value="treasurer" <?php 
                                if ($userRoleFromData === 'treasurer') {
                                    echo 'selected';
                                }
                            ?>>Penningmeester</option>
                        </select>
                        <input type="text" name="description" placeholder="Beschrijving" value="<?php 
                            $descriptionForForm = '';
                            if (isset($user['description'])) {
                                $descriptionForForm = $user['description'];
                            }
                            echo htmlspecialchars($descriptionForForm); 
                        ?>" style="width: 240px;">
                        <button type="submit" style="width: 240px;">Bijwerken</button>
                    </form>
                    
                    <!-- Delete Form - Hidden for admin's own account and main admin -->
                    <?php 
                    $userCanBeDeleted = true;
                    $userUsernameFromData = $user['username'] ?? '';
                    
                    // Check if this is current user
                    if ($userUsernameFromData === $currentUsername) {
                        $userCanBeDeleted = false;
                    }
                    
                    // Check if this is main admin
                    if ($userUsernameFromData === 'admin') {
                        $userCanBeDeleted = false;
                    }
                    
                    if ($userCanBeDeleted): 
                    ?>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" value="<?php 
                            echo htmlspecialchars($user['id'] ?? ''); 
                        ?>">
                        <button type="submit" class="delete-btn" style="width: 240px;">Verwijderen</button>
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
                    <?php 
                    if (!$usersExist): ?>
                        Fout: Gebruikersdata niet geladen
                    <?php elseif (!$usersIsArray): ?>
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