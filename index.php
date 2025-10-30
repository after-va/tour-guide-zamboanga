<?php
ob_start();
session_start();
require_once "config/database.php";
require_once "classes/auth.php";

$authObj = new Auth();
$auth = [];
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $auth["username"] = trim(htmlspecialchars($_POST["username"]));
    $auth["password"] = trim(htmlspecialchars($_POST["password"]));

    $user = $authObj->login($auth["username"], $auth["password"]);

    if ($user && isset($user["role_ID"])) {
        $_SESSION["user"] = $user;
        $_SESSION["account_ID"] = $user["account_ID"];  // ✅ add this line
        $_SESSION["role_ID"] = $user["role_ID"];
        $_SESSION["username"] = $user["user_username"];
        $role = $user["role_ID"];

        if ($role == 1) {
            header('Location: pages/admin/dashboard.php');
            exit();
        } elseif ($role == 2) {
            header('Location: pages/guide/dashboard.php');
            exit();
        } else {
            header('Location: pages/tourist/dashboard.php');
            exit();
        }

    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($auth["username"] ?? '') ?>"><br><br>
        <input type="password" name="password" placeholder="Password"><br><br>
        <button type="submit">Login</button>
    </form>
    
        <a href="registration/tourist-registration.php">Register as A Tourist</a>
</body>
</html>
