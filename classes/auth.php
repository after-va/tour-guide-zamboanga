<?php

require_once __DIR__ . "/../config/database.php";

class Auth extends Database {
    public $username = "";
    public $password = "";

    
    public function login($username, $password) {
        $sql = "
            SELECT 
                ul.user_ID, 
                ul.user_password, 
                r.role_name,
                r.role_ID, 
                ai.account_status
            FROM 
                User_Login ul
            JOIN 
                Account_Info ai ON ul.user_ID = ai.user_ID
            JOIN 
                Role r ON ai.role_ID = r.role_ID
            WHERE 
                ul.user_username = :username 
            LIMIT 1
        ";
        
        try {
            $pdo = $this->connect();
            $query = $pdo->prepare($sql);
            $query->bindParam(':username', $username);
            $query->execute();

            if ($query->rowCount() === 1) {
                $user = $query->fetch();

                if ($password === $user['user_password']) {
                    return [
                        "success" => true,
                        "user_ID" => $user['user_ID'],
                        "user_username" => $username,
                        "role_name" => $user['role_name'],
                        "role_ID" => $user['role_ID'],
                        "account_status" => $user['account_status']
                    ];
                } else {
                    // Password does not match
                    return [
                        "success" => false,
                        "message" => "Invalid username or password." // Generic error for security
                    ];
                }
            } else {
                // User not found
                return [
                    "success" => false,
                    "message" => "Invalid username or password." // Generic error for security
                ];
            }
        } catch (PDOException $e) {
            // Log the error and return a generic failure message
            error_log("Login PDO Error: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "An error occurred during login. Please try again."
            ];
        }
    }
    
}
