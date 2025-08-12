
<?php

require_once __DIR__ . '/../models/Familielid.php';

class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createUser($username, $password, $role, $description = '') {
        // Controleer of username al bestaat
        $stmt = $this->pdo->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            return false;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $role, $description]);
        return $stmt->rowCount() > 0;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT id, username, role, description FROM users ORDER BY role, username ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($userId) {
        $stmt = $this->pdo->prepare("SELECT id, username, role, description FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateUser($userId, $username, $password, $role, $description = '') {
        // Haal de huidige gebruiker op
        $stmt = $this->pdo->prepare("SELECT username, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // De admin gebruiker mag niet van naam of rol veranderen
        if ($user['username'] === 'admin') {
            // Voor admin: alleen wachtwoord en beschrijving mogen worden aangepast
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("UPDATE users SET password = ?, description = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $description, $userId]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE users SET description = ? WHERE id = ?");
                $stmt->execute([$description, $userId]);
            }
            return true;
        }

        // Check of nieuwe username al bestaat bij andere gebruiker
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Normale update voor andere gebruikers
        if (empty($password)) {
            // Update zonder wachtwoord
            $stmt = $this->pdo->prepare("UPDATE users SET username = ?, role = ?, description = ? WHERE id = ?");
            $stmt->execute([$username, $role, $description, $userId]);
        } else {
            // Update met wachtwoord
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET username = ?, password = ?, role = ?, description = ? WHERE id = ?");
            $stmt->execute([$username, $hashed_password, $role, $description, $userId]);
        }
        return true;
    }
    
    public function deleteUser($userId) {
        // Haal de te verwijderen gebruiker op
        $stmt = $this->pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Voorkom dat een gebruiker zichzelf verwijdert
        if (isset($_SESSION['user_id']) && $user['id'] == $_SESSION['user_id']) {
            return false;
        }

        // De admin gebruiker mag niet verwijderd worden
        if ($user['username'] === 'admin') {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        return $stmt->rowCount() > 0;
    }
}