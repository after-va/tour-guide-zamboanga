<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/Booking.php";
require_once "../php/User.php";
require_once "../php/TourPackage.php";

$booking = new Booking();
$user = new User();
$tourPackage = new TourPackage();

$total_bookings = count($booking->getAllBookings());
$total_users = count($user->getAllUsers());
$total_packages = count($tourPackage->getAllTourPackages());
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Tour Guide System</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
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
    
    <h2>System Overview</h2>
    <p>Total Bookings: <?php echo $total_bookings; ?></p>
    <p>Total Users: <?php echo $total_users; ?></p>
    <p>Total Tour Packages: <?php echo $total_packages; ?></p>
    
    <h3>Quick Actions</h3>
    <ul>
        <li><a href="bookings.php">View All Bookings</a></li>
        <li><a href="users.php">Manage Users</a></li>
        <li><a href="tour-packages.php">Manage Tour Packages</a></li>
        <li><a href="tour-spots.php">Manage Tour Spots</a></li>
        <li><a href="schedules.php">Manage Schedules</a></li>
    </ul>
</body>
</html>
