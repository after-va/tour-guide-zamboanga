<?php
session_start();
if (!isset($_SESSION['user'])){ header('Location: index.php'); exit; }
require_once __DIR__ . '/classes/guide.php';
$g = new Guide();
$list = $g->listGuides();
?>
<!DOCTYPE html>
<html>
<head><title>Guides</title></head>
<body>
<h1>Available Guides</h1>
<p><a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a></p>
<?php if (!$list): ?>
    <p>No guides found.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Rating</th>
    </tr>
    <?php foreach ($list as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['phone_number'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['rating'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>
