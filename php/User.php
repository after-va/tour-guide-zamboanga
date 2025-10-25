<?php

require_once "Database.php";

class User extends Database {
    
    // Login user
    public function login($username, $password) {
        $sql = "SELECT ul.*, p.person_ID,
                       CONCAT(n.name_first, ' ', n.name_last) as full_name
                FROM User_Login ul
                INNER JOIN Person p ON ul.person_ID = p.person_ID
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                WHERE ul.username = :username AND ul.is_active = 1";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":username", $username);
        
        if ($query->execute()) {
            $user = $query->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login
                $this->updateLastLogin($user['person_ID']);
                
                // Log activity
                $this->logActivity($user['person_ID'], 'login', 'User logged in');
                
                // Get user roles
                $user['roles'] = $this->getUserRoles($user['login_ID']);
                
                return $user;
            }
        }
        return null;
    }
    
    // Update last login time
    private function updateLastLogin($person_ID) {
        $sql = "UPDATE User_Login SET last_login = NOW() WHERE person_ID = :person_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        return $query->execute();
    }
    
    // Log activity
    public function logActivity($user_ID, $action, $description, $ip_address = null, $user_agent = null) {
        $sql = "INSERT INTO Activity_Log (user_ID, action, description, ip_address, user_agent) 
                VALUES (:user_ID, :action, :description, :ip_address, :user_agent)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        $query->bindParam(":action", $action);
        $query->bindParam(":description", $description);
        $query->bindParam(":ip_address", $ip_address);
        $query->bindParam(":user_agent", $user_agent);
        
        return $query->execute();
    }
    
    // Get user by ID
    public function getUserById($person_ID) {
        $sql = "SELECT p.*, n.*, ci.*, a.*, ph.*, ul.username, ul.last_login, ul.login_ID
                FROM Person p
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
                LEFT JOIN Address_Info a ON ci.address_ID = a.address_ID
                LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
                LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
                WHERE p.person_ID = :person_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        
        if ($query->execute()) {
            $user = $query->fetch();
            if ($user && isset($user['login_ID'])) {
                $user['roles'] = $this->getUserRoles($user['login_ID']);
            }
            return $user;
        }
        return null;
    }
    
    // Get all users
    public function getAllUsers() {
        $sql = "SELECT p.person_ID, 
                       CONCAT(n.name_first, ' ', n.name_last) as full_name,
                       ci.contactinfo_email,
                       ph.phone_number,
                       ul.username,
                       ul.last_login,
                       ul.is_active,
                       ul.login_ID,
                       GROUP_CONCAT(r.role_name SEPARATOR ', ') as roles
                FROM Person p
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
                LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
                LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
                LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID AND ar.is_active = 1
                LEFT JOIN Role_Info r ON ar.role_ID = r.role_ID
                GROUP BY p.person_ID, ul.login_ID
                ORDER BY p.person_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Update user status
    public function updateUserStatus($person_ID, $is_active) {
        $sql = "UPDATE User_Login SET is_active = :is_active WHERE person_ID = :person_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":is_active", $is_active);
        $query->bindParam(":person_ID", $person_ID);
        return $query->execute();
    }
    
    // Change password
    public function changePassword($person_ID, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE User_Login SET password_hash = :password_hash WHERE person_ID = :person_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":password_hash", $password_hash);
        $query->bindParam(":person_ID", $person_ID);
        return $query->execute();
    }
    
    // Check if username exists
    public function usernameExists($username) {
        $sql = "SELECT COUNT(*) as count FROM User_Login WHERE username = :username";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":username", $username);
        
        if ($query->execute()) {
            $result = $query->fetch();
            return $result['count'] > 0;
        }
        return false;
    }
    
    // Get user roles
    public function getUserRoles($login_ID) {
        $sql = "SELECT ar.account_role_ID, ar.role_ID, r.role_name, ar.role_rating_score, ar.is_active
                FROM Account_Role ar
                INNER JOIN Role_Info r ON ar.role_ID = r.role_ID
                WHERE ar.login_ID = :login_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":login_ID", $login_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Add role to user account
    public function addUserRole($login_ID, $role_ID) {
        $sql = "INSERT INTO Account_Role (login_ID, role_ID) VALUES (:login_ID, :role_ID)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":login_ID", $login_ID);
        $query->bindParam(":role_ID", $role_ID);
        return $query->execute();
    }
    
    // Check if user has specific role
    public function hasRole($login_ID, $role_ID) {
        $sql = "SELECT COUNT(*) as count FROM Account_Role 
                WHERE login_ID = :login_ID AND role_ID = :role_ID AND is_active = 1";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":login_ID", $login_ID);
        $query->bindParam(":role_ID", $role_ID);
        
        if ($query->execute()) {
            $result = $query->fetch();
            return $result['count'] > 0;
        }
        return false;
    }
}
