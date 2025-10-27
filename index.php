<?php
session_start();
require_once "classes/auth.php";

$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user = $auth->login($username, $password);
    if ($user){
        $_SESSION['user'] = $user;
        
        // Redirect based on role
        if ($user['role_name'] === 'Admin') {
            header('Location: pages/admin/dashboard.php');
        } elseif ($user['role_name'] === 'Guide') {
            header('Location: pages/guide/dashboard.php');
        } else {
            header('Location: pages/tourist/dashboard.php');
        }
        exit;
    } else {
        $error = 'Invalid credentials or inactive account.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h1>Tourismo Zamboanga</h1>
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 10px; margin-bottom: 15px; border: 1px solid #ef5350;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div style="margin-bottom: 15px;">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 10px; background: #1976d2; color: white; border: none; cursor: pointer;">
                Login
            </button>
        </form>
        
        <hr style="margin: 20px 0;">
        
        <p style="text-align: center;">
            <a href="registration/tourist-registration.php">Register as Tourist</a> | 
            <a href="registration/guide-registration.php">Register as Guide</a>
        </p>
        
        <p style="text-align: center;">
            <a href="pages/public/browse-packages.php">Browse Tour Packages</a>
        </p>
    </div>
</body>
</html>
