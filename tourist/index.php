<?php
session_start();
require_once "../php/User.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $user = new User();
    $result = $user->login($username, $password);
    
    if ($result && isset($result['roles'])) {
        // Check if user has tourist role (role_ID = 3)
        $hasTouristRole = false;
        $touristAccountRoleID = null;
        foreach ($result['roles'] as $role) {
            if ($role['role_ID'] == 3 && $role['is_active'] == 1) {
                $hasTouristRole = true;
                $touristAccountRoleID = $role['account_role_ID'];
                break;
            }
        }
        
        if ($hasTouristRole) {
            $_SESSION['user_id'] = $result['person_ID'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['login_id'] = $result['login_ID'];
            $_SESSION['role_id'] = 3;
            $_SESSION['account_role_id'] = $touristAccountRoleID;
            $_SESSION['full_name'] = $result['full_name'];
            $_SESSION['roles'] = $result['roles'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials or not a tourist account";
        }
    } else {
        $error = "Invalid credentials or not a tourist account";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tourist Login - Tour Guide System</title>
</head>
<body>
    <h1>Tourist Login</h1>
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
