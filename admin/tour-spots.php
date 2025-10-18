<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/TourSpot.php";

$tourSpot = new TourSpot();
$spots = $tourSpot->getAllTourSpots();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Tour Spots - Admin</title>
</head>
<body>
    <h1>Manage Tour Spots</h1>
    
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
    
    <h2>All Tour Spots</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <p><a href="add-tour-spot.php">Add New Spot</a></p>
    
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($spots as $s): ?>
        <tr>
            <td><?= $s['spots_ID']; ?></td>
            <td><?= $s['spots_Name']; ?></td>
            <td><?= $s['spots_Description']; ?></td>
            <td><?= $s['spots_category']; ?></td>
            <td><?= $s['spots_Address']; ?></td>
            <td>
                <a href="edit-tour-spot.php?id=<?= $s['spots_ID']; ?>">Edit</a> |
                <a href="delete-tour-spot.php?id=<?= $s['spots_ID']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
