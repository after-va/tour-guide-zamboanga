<?php
session_start();

require_once "../../config/database.php";
require_once "../../classes/auth.php";

// Create a new Auth class instance to manage users
$auth = new Auth();
$users = $auth->getAllUsers();

// Check for session messages
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Clear session messages
if (isset($_SESSION['success'])) unset($_SESSION['success']);
if (isset($_SESSION['error'])) unset($_SESSION['error']);
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
        <a href="manage-users.php">Users</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="tour-spots.php">Tour Spots</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?= $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
            <?= $error_message; ?>
        </div>
    <?php endif; ?>
    
    <h2>All Users</h2>
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Password</th>
            <th>Role</th>
            <th>Tourist Profile</th>
            <th>Guide Profile</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php $no = 1; if (!empty($users)){ foreach ($users as $u){ ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $u['full_name']; ?></td>
            <td><?= $u['username']; ?></td>
            <td><?= str_repeat('*', 8); ?></td>
            <td><?= $u['role']; ?></td>
            <td><?php if($u['role_ID'] == 3) { echo 'Yes';} else {echo 'No';}?></td>
            <td><?php if($u['role_ID'] == 2) { echo 'Yes';} else {echo 'No';} ?></td>
            <td><?= $u['status']; ?></td>
            <td>
                <a href="manage-user-edit.php?id=<?= $u['user_ID']; ?>">Edit</a> |
                <a href="manage-user-delete.php?id=<?= $u['user_ID']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a> |
                <a href="user-details.php?id=<?= $u['person_ID']; ?>">View</a>
            </td>
        </tr>
        <?php }} ?>
    </table>
</body>
</html>
