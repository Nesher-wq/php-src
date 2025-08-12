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
                    $isCurrentUser = ($user['id'] == $_SESSION['user_id']);
                    $canDeleteUser = !$isCurrentUser && ($user['username'] !== 'admin');
                ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['description']); ?></td>
                    <td class="action-cell">
                        <form method="POST" action="">
                            <input type="hidden" name="edit_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="show_edit_form" class="action-btn edit-btn">Bewerken</button>
                        </form>
                        
                        <?php if ($canDeleteUser): ?>
                        <form method="POST" action="" onsubmit="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?');">
                            <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="action-btn delete-btn">Verwijderen</button>
                        </form>
                        <?php else: ?>
                        <button type="button" class="action-btn delete-btn" style="opacity: 0.5; cursor: not-allowed;" 
                                title="<?php echo ($user['username'] === 'admin') ? 'De admin gebruiker kan niet worden verwijderd' : 'Je kunt jezelf niet verwijderen'; ?>">
                            Verwijderen
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
