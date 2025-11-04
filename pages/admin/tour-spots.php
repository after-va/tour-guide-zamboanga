<?php
session_start();


require_once "../../classes/tour-manager.php";

$tourSpot = new TourManager();
$spots = $tourSpot->getAllSpots();
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
        <a href="tour-spots.php">Tour Spots</a> |
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
    
    <p><a href="add-tour-spots.php">Add New Spot</a></p>
    
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php $no = 1; foreach ($spots as $s):  ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $s['spots_name']; ?></td>
            <td><?= $s['spots_description']; ?></td>
            <td><?= $s['spots_category']; ?></td>
            <td><?= $s['spots_address']; ?></td>
            <td>
                <a href="tour-spots-edit.php?id=<?= $s['spots_ID']; ?>">Edit</a> |
                <a href="tour-spots-delete.php?id=<?= $s['spots_ID']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
