<?php
session_start();
if (!isset($_SESSION['user'])){ header('Location: index.php'); exit; }
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
<h1>Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($user['username']) ?>.</p>
<p>Roles: <?= htmlspecialchars(implode(', ', $user['roles'])) ?></p>
<ul>
    <li><a href="guides.php">Browse Guides</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</body>
</html>
