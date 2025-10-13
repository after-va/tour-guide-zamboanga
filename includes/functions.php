<?php
/**
 * Common Functions
 * Tourismo Zamboanga System
 */

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
}

/**
 * Check if user is tour guide
 */
function is_guide() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2;
}

/**
 * Check if user is tourist
 */
function is_tourist() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3;
}

/**
 * Redirect to specific page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Generate random string
 */
function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Format currency
 */
function format_currency($amount) {
    return '₱' . number_format($amount, 2);
}

/**
 * Format date
 */
function format_date($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime, $format = 'F d, Y h:i A') {
    return date($format, strtotime($datetime));
}

/**
 * Calculate age from birthdate
 */
function calculate_age($birthdate) {
    $dob = new DateTime($birthdate);
    $now = new DateTime();
    $age = $now->diff($dob);
    return $age->y;
}

/**
 * Send email notification
 */
function send_email($to, $subject, $message) {
    $headers = "From: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Upload file
 */
function upload_file($file, $target_dir = '../uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    $file_name = basename($file["name"]);
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_file_name;
    
    // Check if file type is allowed
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'file_name' => $new_file_name, 'file_path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'Upload failed'];
    }
}

/**
 * Get user role name
 */
function get_role_name($role_id) {
    $roles = [
        1 => 'Admin',
        2 => 'Tour Guide',
        3 => 'Tourist'
    ];
    return isset($roles[$role_id]) ? $roles[$role_id] : 'Unknown';
}

/**
 * Get booking status badge
 */
function get_status_badge($status) {
    $badges = [
        'pending' => '<span class="badge-admin warning">Pending</span>',
        'confirmed' => '<span class="badge-admin success">Confirmed</span>',
        'completed' => '<span class="badge-admin info">Completed</span>',
        'cancelled' => '<span class="badge-admin danger">Cancelled</span>'
    ];
    return isset($badges[$status]) ? $badges[$status] : $status;
}

/**
 * Get payment status badge
 */
function get_payment_status_badge($status) {
    $badges = [
        'pending' => '<span class="payment-status pending">Pending</span>',
        'paid' => '<span class="payment-status paid">Paid</span>',
        'failed' => '<span class="payment-status failed">Failed</span>'
    ];
    return isset($badges[$status]) ? $badges[$status] : $status;
}

/**
 * Log activity
 */
function log_activity($user_id, $action, $description) {
    global $conn;
    
    $sql = "INSERT INTO activity_log (user_id, action, description, created_at) 
            VALUES (?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $description);
    return $stmt->execute();
}

/**
 * Get user full name
 */
function get_user_full_name($person_id) {
    global $conn;
    
    $sql = "SELECT CONCAT(name_first, ' ', name_last) as full_name 
            FROM Name_Info 
            WHERE name_ID = (SELECT name_ID FROM Person WHERE person_ID = ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $person_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['full_name'];
    }
    
    return 'Unknown';
}

/**
 * Calculate total bookings for guide
 */
function get_guide_total_bookings($guide_id) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total 
            FROM Booking b
            JOIN Schedule s ON b.schedule_ID = s.schedule_ID
            WHERE s.guide_ID = ? AND b.booking_Status = 'completed'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $guide_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['total'];
    }
    
    return 0;
}

/**
 * Calculate guide earnings
 */
function get_guide_earnings($guide_id, $period = 'all') {
    global $conn;
    
    $date_condition = "";
    if ($period == 'month') {
        $date_condition = "AND MONTH(p.paymentinfo_Date) = MONTH(CURRENT_DATE()) 
                          AND YEAR(p.paymentinfo_Date) = YEAR(CURRENT_DATE())";
    } elseif ($period == 'year') {
        $date_condition = "AND YEAR(p.paymentinfo_Date) = YEAR(CURRENT_DATE())";
    }
    
    $sql = "SELECT SUM(p.paymentinfo_Amount) as total_earnings
            FROM Payment_Info p
            JOIN Booking b ON p.booking_ID = b.booking_ID
            JOIN Schedule s ON b.schedule_ID = s.schedule_ID
            WHERE s.guide_ID = ? $date_condition";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $guide_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['total_earnings'] ?? 0;
    }
    
    return 0;
}

?>
