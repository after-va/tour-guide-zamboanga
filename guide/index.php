<?php
session_start();
require_once "../php/User.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $user = new User();
    $result = $user->login($username, $password);
    
    if ($result && $result['role_ID'] == 2) {
        $_SESSION['user_id'] = $result['person_ID'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['role_id'] = $result['role_ID'];
        $_SESSION['full_name'] = $result['full_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials or not a guide account";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guide Login - Tour Guide System</title>
</head>
<body>
    <h1>Guide Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Login</button>
    </form>
    
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
