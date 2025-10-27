<?php
session_start();
require_once "../classes/database.php";
require_once "../classes/guide.php";

$guide = new Guide();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_first = trim($_POST['name_first'] ?? '');
    $name_last = trim($_POST['name_last'] ?? '');
    $name_middle = trim($_POST['name_middle'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $contactinfo_email = trim($_POST['contactinfo_email'] ?? '');
    $person_nationality = trim($_POST['person_nationality'] ?? '');
    $person_gender = trim($_POST['person_gender'] ?? '');
    $person_dateofbirth = trim($_POST['person_dateofbirth'] ?? '');
    
    if (empty($name_first) || empty($name_last) || empty($username) || empty($password) || empty($contactinfo_email)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // For simplicity, using minimal data for guide registration
        $result = $guide->addGuide(
            $name_first, '', $name_middle, $name_last, '',
            '', '', 1, // Minimal address data
            1, '0000000000', // Minimal phone data
            '', 1, '0000000000', '', // Minimal emergency data
            $contactinfo_email,
            $person_nationality, $person_gender, '', $person_dateofbirth,
            $username, $password
        );
        
        if ($result) {
            $success = 'Registration successful! You can now login.';
            header('Location: ../index.php');
            exit;
        } else {
            $error = 'Registration failed. Username may already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Registration - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Tourismo Zamboanga</h1>
        <h2>Guide Registration</h2>
        
        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 10px; margin-bottom: 15px; border: 1px solid #ef5350;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; margin-bottom: 15px; border: 1px solid #4caf50;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <h3>Personal Information</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="name_first">First Name: *</label><br>
                <input type="text" id="name_first" name="name_first" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="name_middle">Middle Name:</label><br>
                <input type="text" id="name_middle" name="name_middle" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="name_last">Last Name: *</label><br>
                <input type="text" id="name_last" name="name_last" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="person_gender">Gender:</label><br>
                <select id="person_gender" name="person_gender" style="width: 100%; padding: 8px;">
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="person_dateofbirth">Date of Birth:</label><br>
                <input type="date" id="person_dateofbirth" name="person_dateofbirth" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="person_nationality">Nationality:</label><br>
                <input type="text" id="person_nationality" name="person_nationality" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <h3>Contact Information</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="contactinfo_email">Email: *</label><br>
                <input type="email" id="contactinfo_email" name="contactinfo_email" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <h3>Account Information</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="username">Username: *</label><br>
                <input type="text" id="username" name="username" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="password">Password: *</label><br>
                <input type="password" id="password" name="password" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
                <small>Minimum 6 characters</small>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="confirm_password">Confirm Password: *</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 10px; background: #1976d2; color: white; border: none; cursor: pointer;">
                Register as Guide
            </button>
        </form>
        
        <hr style="margin: 20px 0;">
        
        <p style="text-align: center;">
            Already have an account? <a href="../index.php">Login here</a>
        </p>
    </div>
</body>
</html>
