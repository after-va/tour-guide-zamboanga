<?php
require_once "database.php";

class Auth extends Database {
    
    public function login($username, $password) {
        try {
            $sql = "SELECT ul.*, p.person_ID, 
                    CONCAT(n.name_first, ' ', n.name_last) as full_name,
                    ar.account_role_ID, ar.role_ID, ri.role_name
                    FROM User_Login ul
                    INNER JOIN Person p ON ul.person_ID = p.person_ID
                    LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                    LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                    LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
                    WHERE ul.username = :username
                    LIMIT 1";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':username', $username);
            $query->execute();
            
            $user = $query->fetch(PDO::FETCH_ASSOC);
            
            // Debug logging
            error_log("Login attempt for username: " . $username);
            
            if (!$user) {
                error_log("User not found: " . $username);
                return false;
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                error_log("Password verification failed for: " . $username);
                return false;
            }
            
            if (!$user['role_name']) {
                error_log("User has no role assigned: " . $username . " (login_ID: " . $user['login_ID'] . ")");
                return false;
            }
            
            // Update last login
            $this->updateLastLogin($user['login_ID']);
            
            error_log("Login successful for: " . $username . " with role: " . $user['role_name']);
            
            return [
                'login_ID' => $user['login_ID'],
                'person_ID' => $user['person_ID'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'account_role_ID' => $user['account_role_ID'],
                'role_ID' => $user['role_ID'],
                'role_name' => $user['role_name']
            ];
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return false;
        }
    }

    private function updateLastLogin($login_ID) {
        try {
            $sql = "UPDATE User_Login SET last_login = NOW() WHERE login_ID = :login_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':login_ID', $login_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Update Last Login Error: " . $e->getMessage());
            return false;
        }
    }

    public function register($person_ID, $username, $password) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO User_Login (person_ID, username, password_hash) 
                    VALUES (:person_ID, :username, :password_hash)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':person_ID', $person_ID);
            $query->bindParam(':username', $username);
            $query->bindParam(':password_hash', $password_hash);
            
            if ($query->execute()) {
                return $this->connect()->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return false;
        }
    }

    public function changePassword($login_ID, $old_password, $new_password) {
        try {
            // Verify old password
            $sql = "SELECT password_hash FROM User_Login WHERE login_ID = :login_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':login_ID', $login_ID);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($old_password, $user['password_hash'])) {
                return false;
            }
            
            // Update password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE User_Login SET password_hash = :password_hash WHERE login_ID = :login_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':login_ID', $login_ID);
            $query->bindParam(':password_hash', $new_password_hash);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Change Password Error: " . $e->getMessage());
            return false;
        }
    }

    public function createPasswordResetToken($person_ID) {
        try {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $sql = "INSERT INTO Password_Reset (person_ID, reset_token, expires_at) 
                    VALUES (:person_ID, :reset_token, :expires_at)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':person_ID', $person_ID);
            $query->bindParam(':reset_token', $token);
            $query->bindParam(':expires_at', $expires_at);
            
            if ($query->execute()) {
                return $token;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Password Reset Token Error: " . $e->getMessage());
            return false;
        }
    }

    public function resetPassword($token, $new_password) {
        $db = $this->connect();
        $db->beginTransaction();

        try {
            // Verify token
            $sql = "SELECT * FROM Password_Reset 
                    WHERE reset_token = :token 
                    AND used = 0 
                    AND expires_at > NOW()";
            $query = $db->prepare($sql);
            $query->bindParam(':token', $token);
            $query->execute();
            $reset = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$reset) {
                $db->rollBack();
                return false;
            }
            
            // Update password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE User_Login 
                    SET password_hash = :password_hash 
                    WHERE person_ID = :person_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':password_hash', $password_hash);
            $query->bindParam(':person_ID', $reset['person_ID']);
            $query->execute();
            
            // Mark token as used
            $sql = "UPDATE Password_Reset SET used = 1 WHERE reset_ID = :reset_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':reset_ID', $reset['reset_ID']);
            $query->execute();
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Reset Password Error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }
}
