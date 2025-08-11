<?php
// Dashboard voor admin
?><!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="/Ledenadministratie/assets/style.css">

</head>
<body>
    <nav class="navbar">
        <h1>Dashboard (Admin)</h1>
        <a href="index.php?action=change_password" style="margin-right:10px;">Change password</a>
        <a href="?action=logout">Uitloggen</a>
    </nav>
    <div class="container">
        <div class="welcome-section">
            <h2>Hallo, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        </div>
        <?php if (isset($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h3>Gebruikers</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Beschrijving</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userController->getAllUsers() as $user): ?>
                        <?php 
                            $isMainAdmin = ($user['username'] === 'admin' && $user['role'] === 'admin');
                            $isAdminUser = ($user['role'] === 'admin' && $user['username'] !== 'admin');
                            $rowClass = $isMainAdmin ? 'main-admin' : ($isAdminUser ? 'admin-user' : '');
                            // Bewerken mag altijd, ook voor hoofdadmin (voor wachtwoord wijzigen)
                            $canEditUser = true;
                            // Verwijderen mag nooit voor jezelf, ook niet als admin
                            $canDeleteUser = ($user['id'] != $_SESSION['user_id'] && 
                                            ($_SESSION['username'] === 'admin' || $user['role'] !== 'admin') && 
                                            !$isMainAdmin);
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($user['username']); ?>
                                <?php if ($isMainAdmin): ?>
                                    <span class="info-badge">Hoofdadmin</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['description']); ?></td>
                            <td class="action-cell">
                                <?php if ($canEditUser): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="edit_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="show_edit_form" class="action-btn edit-btn">Bewerken</button>
                                </form>
                                <?php else: ?>
                                <button type="button" class="action-btn edit-btn" style="opacity: 0.5; cursor: not-allowed;" 
                                        title="Alleen de hoofdadmin kan andere admins bewerken">Bewerken</button>
                                <?php endif; ?>
                                
                                <?php if ($canDeleteUser): ?>
                                <form method="POST" action="" onsubmit="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="action-btn delete-btn">Verwijderen</button>
                                </form>
                                <?php else: ?>
                                <button type="button" class="action-btn delete-btn" style="opacity: 0.5; cursor: not-allowed;" 
                                        title="<?php echo $isMainAdmin ? 'De hoofdadmin kan niet worden verwijderd' : 'Je kunt jezelf niet verwijderen of je hebt niet de juiste rechten'; ?>">
                                    Verwijderen
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($edit_user)): ?>
        <?php $isMainAdmin = ($edit_user['username'] === 'admin' && $edit_user['role'] === 'admin'); ?>
        <div class="section">
            <h3>Gebruiker bewerken</h3>
            <?php if ($isMainAdmin): ?>
            <div class="message info">
                <strong>Let op:</strong> Dit is de hoofdadmin. De gebruikersnaam en rol kunnen niet worden gewijzigd.
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="edit_user_id" value="<?php echo $edit_user['id']; ?>">
                
                <div class="form-group">
                    <label for="edit_username">Username:</label>
                    <input type="text" id="edit_username" name="edit_username" 
                           value="<?php echo htmlspecialchars($edit_user['username']); ?>" 
                           <?php echo $isMainAdmin ? 'readonly class="protected-field"' : 'required'; ?>>
                    <?php if ($isMainAdmin): ?>
                    <small>De gebruikersnaam van de hoofdadmin kan niet worden gewijzigd</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Nieuw wachtwoord (leeg laten om ongewijzigd te houden):</label>
                    <input type="password" id="edit_password" name="edit_password">
                </div>
                
                <div class="form-group">
                    <label for="edit_role">Rol:</label>
                    <select id="edit_role" name="edit_role" <?php echo $isMainAdmin ? 'disabled class="protected-field"' : 'required'; ?>>
                        <?php 
                        foreach ($allowed_roles as $role): ?>
                            <option value="<?php echo $role; ?>" <?php echo ($edit_user['role'] === $role) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($role); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isMainAdmin): ?>
                    <input type="hidden" name="edit_role" value="admin">
                    <small>De rol van de hoofdadmin kan niet worden gewijzigd</small>
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
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</body>
</html>
