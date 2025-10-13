<?php
/**
 * Authentication Functions
 * Tourismo Zamboanga System
 */

require_once 'config.php';
require_once 'functions.php';

/**
 * Register new user
 */
function register_user($data) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert name info
        $sql_name = "INSERT INTO Name_Info (name_first, name_last) VALUES (?, ?)";
        $stmt_name = $conn->prepare($sql_name);
        $stmt_name->bind_param("ss", $data['first_name'], $data['last_name']);
        $stmt_name->execute();
        $name_id = $conn->insert_id;
        
        // Insert phone number
        $sql_phone = "INSERT INTO Phone_Number (countrycode_ID, phone_number) VALUES (?, ?)";
        $stmt_phone = $conn->prepare($sql_phone);
        $countrycode_id = 1; // Default Philippines
        $stmt_phone->bind_param("is", $countrycode_id, $data['phone']);
        $stmt_phone->execute();
        $phone_id = $conn->insert_id;
        
        // Insert contact info
        $sql_contact = "INSERT INTO Contact_Info (phone_ID, contactinfo_email) VALUES (?, ?)";
        $stmt_contact = $conn->prepare($sql_contact);
        $stmt_contact->bind_param("is", $phone_id, $data['email']);
        $stmt_contact->execute();
        $contactinfo_id = $conn->insert_id;
        
        // Insert person
        $sql_person = "INSERT INTO Person (role_ID, name_ID, contactinfo_ID, person_Nationality) 
                      VALUES (?, ?, ?, ?)";
        $stmt_person = $conn->prepare($sql_person);
        $stmt_person->bind_param("iiis", $data['role_id'], $name_id, $contactinfo_id, $data['nationality']);
        $stmt_person->execute();
        $person_id = $conn->insert_id;
        
        // Insert login credentials (separate table - you may need to create this)
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql_login = "INSERT INTO User_Login (person_ID, username, password_hash) VALUES (?, ?, ?)";
        $stmt_login = $conn->prepare($sql_login);
        $stmt_login->bind_param("iss", $person_id, $data['email'], $password_hash);
        $stmt_login->execute();
        
        // Commit transaction
        $conn->commit();
        
        return ['success' => true, 'person_id' => $person_id];
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Login user
 */
function login_user($email, $password) {
    global $conn;
    
    // Get user credentials
    $sql = "SELECT ul.*, p.person_ID, p.role_ID, p.name_ID 
            FROM User_Login ul
            JOIN Person p ON ul.person_ID = p.person_ID
            WHERE ul.username = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['person_ID'];
            $_SESSION['role_id'] = $row['role_ID'];
            $_SESSION['name_id'] = $row['name_ID'];
            $_SESSION['email'] = $email;
            $_SESSION['logged_in'] = true;
            
            // Log activity
            log_activity($row['person_ID'], 'login', 'User logged in');
            
            return ['success' => true, 'role_id' => $row['role_ID']];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid credentials'];
}

/**
 * Logout user
 */
function logout_user() {
    if (isset($_SESSION['user_id'])) {
        log_activity($_SESSION['user_id'], 'logout', 'User logged out');
    }
    
    session_unset();
    session_destroy();
    
    return ['success' => true];
}

/**
 * Check authentication
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('../tourist-login.html');
    }
}

/**
 * Check admin authentication
 */
function require_admin() {
    if (!is_logged_in() || !is_admin()) {
        redirect('../admin/admin-login.html');
    }
}

/**
 * Check guide authentication
 */
function require_guide() {
    if (!is_logged_in() || !is_guide()) {
        redirect('../guide/guide-login.html');
    }
}

/**
 * Check tourist authentication
 */
function require_tourist() {
    if (!is_logged_in() || !is_tourist()) {
        redirect('../tourist-login.html');
    }
}

/**
 * Change password
 */
function change_password($user_id, $old_password, $new_password) {
    global $conn;
    
    // Verify old password
    $sql = "SELECT password_hash FROM User_Login WHERE person_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($old_password, $row['password_hash'])) {
            // Update password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE User_Login SET password_hash = ? WHERE person_ID = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $new_hash, $user_id);
            
            if ($stmt_update->execute()) {
                log_activity($user_id, 'password_change', 'Password changed successfully');
                return ['success' => true];
            }
        } else {
            return ['success' => false, 'message' => 'Incorrect old password'];
        }
    }
    
    return ['success' => false, 'message' => 'User not found'];
}

/**
 * Reset password (forgot password)
 */
function reset_password($email) {
    global $conn;
    
    // Check if email exists
    $sql = "SELECT p.person_ID FROM Person p
            JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
            WHERE ci.contactinfo_email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Generate reset token
        $reset_token = generate_random_string(32);
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store reset token
        $sql_token = "INSERT INTO Password_Reset (person_ID, reset_token, expires_at) 
                     VALUES (?, ?, ?)";
        $stmt_token = $conn->prepare($sql_token);
        $stmt_token->bind_param("iss", $row['person_ID'], $reset_token, $expires_at);
        $stmt_token->execute();
        
        // Send reset email
        $reset_link = SITE_URL . "reset-password.php?token=" . $reset_token;
        $subject = "Password Reset Request";
        $message = "Click this link to reset your password: " . $reset_link;
        
        send_email($email, $subject, $message);
        
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Email not found'];
}

?>
