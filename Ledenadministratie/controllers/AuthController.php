<?php

class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        error_log("AuthController: Constructor called");
    }

    public function login($username, $password) {
        error_log("AuthController: Login attempt for user: " . $username);
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                error_log("AuthController: No user found with username: " . $username);
                return false;
            }

            error_log("AuthController: User found - ID: " . $user['id'] . ", first_login: " . ($user['first_login'] ?? 'NULL'));

            // Check voor eerste login met plain text wachtwoord
            if (isset($user['first_login']) && $user['first_login'] == 1) {
                error_log("AuthController: First login detected, comparing passwords");
                error_log("AuthController: Expected: '" . $user['password'] . "', Got: '" . $password . "'");
                
                if ($password === $user['password']) {
                    error_log("AuthController: Plain text password match - updating to hashed");
                    
                    // Hash het wachtwoord en update de gebruiker
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $this->pdo->prepare("UPDATE users SET password = ?, first_login = 0 WHERE id = ?");
                    $result = $updateStmt->execute([$hashedPassword, $user['id']]);
                    
                    if ($result) {
                        error_log("AuthController: Password updated successfully");
                    } else {
                        error_log("AuthController: Failed to update password");
                    }
                    
                    // Set session variables
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    error_log("AuthController: Session variables set - loggedin: " . $_SESSION['loggedin'] . ", role: " . $_SESSION['role']);
                    return true;
                } else {
                    error_log("AuthController: Plain text password mismatch");
                    return false;
                }
            }
            
            // Normale login check met gehashed wachtwoord
            if (!isset($user['first_login']) || $user['first_login'] == 0) {
                error_log("AuthController: Normal login check with hashed password");
                if (password_verify($password, $user['password'])) {
                    error_log("AuthController: Hashed password verified successfully");
                    
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    return true;
                } else {
                    error_log("AuthController: Hashed password verification failed");
                    return false;
                }
            }
            
        } catch (Exception $e) {
            error_log("AuthController: Database error: " . $e->getMessage());
            return false;
        }
        
        error_log("AuthController: Login failed for user: " . $username);
        return false;
    }

    public function logout() {
        session_destroy();
        header('Location: /Ledenadministratie/index.php');
        exit;
    }
}
?>