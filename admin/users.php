<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/User.php";

$user = new User();
$users = $user->getAllUsers();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin</title>
</head>
<body>
    <h1>Manage Users</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="bookings.php">Bookings</a> |
        <a href="users.php">Users</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="tour-spots.php">Tour Spots</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>All Users</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Username</th>
            <th>Last Login</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?php echo $u['person_ID']; ?></td>
            <td><?php echo $u['full_name']; ?></td>
            <td><?php echo $u['role_name']; ?></td>
            <td><?php echo $u['contactinfo_email'] ?? 'N/A'; ?></td>
            <td><?php echo $u['phone_number'] ?? 'N/A'; ?></td>
            <td><?php echo $u['username'] ?? 'N/A'; ?></td>
            <td><?php echo $u['last_login'] ?? 'Never'; ?></td>
            <td><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></td>
            <td>
                <a href="user-details.php?id=<?php echo $u['person_ID']; ?>">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
