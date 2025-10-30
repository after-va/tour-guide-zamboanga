<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}
require_once "../../classes/booking.php";
require_once "../../classes/tourist.php";
$tourist_ID = $_SESSION['user']['account_ID'];
$toristObj = new Tourist();
$bookingObj = new Booking();


?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="booking.php">My Bookings</a> |
        <a href="tour-packages.php">View Tour Packages</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>My Bookings</h2>
    
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
    
    <p><a href="tour-packages-browse.php">Browse Tour Packages</a></p>
    
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Package Name</th>
            <th>Description</th>
            <th>Schedule Days</th>
            <th>Tour Guide</th>
            <th>Status</th>
            <th>Tour Spots</th>
            <th>Actions</th>
        </tr>
        
        <tr>
            <td><?php  ?></td>
            <td><?php  ?></td>
            <td><?php  ?></td>
            <td><?php  ?></td>
            <td><?php  ?></td>
            <td><?php  ?></td>
            <td><?php  ?></td>
            <td>
                <a href="booking-edit.php?id=<?php  ?>">Edit</a> |
                <a href="booking-cancel.php?id=<?php  ?>" onclick="return confirm('Are you sure?')">Cancel</a> | <a href="booking-view.php?id=<?php  ?>">View</a>
            </td>
        </tr>
        <?php ?>
    </table>
    
</body>
</html>