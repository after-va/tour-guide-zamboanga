<?php
session_start();
require_once __DIR__ . '/classes/auth.php';

$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user = $auth->login($username, $password);
    if ($user){
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials or inactive account.';
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Tour Guide System</title></head>
<body>
    <h1>Login</h1>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Username</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p><a href="register_tourist.php">Register as Tourist</a></p>
</body>
</html>
