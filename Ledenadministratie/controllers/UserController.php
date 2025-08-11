
<?php
// Directe toegang tot dit bestand blokkeren
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ../index.php');
    exit;
}

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
    
    public function updateUser($userId, $username, $password, $role, $description = '', $currentUsername = '') {
        // Haal de huidige gebruiker op om te controleren of het de hoofdadmin is
        $stmt = $this->pdo->prepare("SELECT username, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // De hoofdadmin (username='admin', role='admin') mag niet van naam of rol veranderen,
        // maar moet wel altijd zijn/haar eigen wachtwoord en beschrijving kunnen aanpassen
        if ($user['username'] === 'admin' && $user['role'] === 'admin') {
            if ($username !== 'admin' || $role !== 'admin') {
                // Alleen toestaan als alleen wachtwoord of beschrijving wordt aangepast
                if (!empty($password) || $description !== $user['description']) {
                    // Update alleen password en/of description
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
                // Anders: poging tot wijzigen van naam of rol, niet toestaan
                return false;
            } else {
                // Alleen wachtwoord en/of beschrijving worden aangepast
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
        }

        // Check of nieuwe username al bestaat bij andere gebruiker
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Admin-rol gebruikers mogen andere admin-rol gebruikers (inclusief zichzelf) aanpassen, behalve de hoofdadmin
        // Dus: als de te wijzigen gebruiker een admin is, mag iedereen met role=admin dit doen, behalve als username='admin'
        if ($user['role'] === 'admin' && $user['username'] !== 'admin') {
            // Alleen admin-rol gebruikers mogen dit
            if ($_SESSION['role'] !== 'admin') {
                return false;
            }
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
    
    public function deleteUser($userId, $currentUsername = '') {
        // Haal de te verwijderen gebruiker op
        $stmt = $this->pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Voorkom dat een gebruiker zichzelf verwijdert
        if (isset($_SESSION['user_id']) && $user['id'] == $_SESSION['user_id']) {
            return false;
        }

        // De hoofdadmin mag niet verwijderd worden
        if ($user['username'] === 'admin' && $user['role'] === 'admin') {
            return false;
        }

        // Alleen de hoofdadmin mag andere admin users verwijderen
        if ($user['role'] === 'admin' && $currentUsername !== 'admin') {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        return $stmt->rowCount() > 0;
    }
}
