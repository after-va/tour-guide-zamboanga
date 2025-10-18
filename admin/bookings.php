<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/Booking.php";

$booking = new Booking();
$bookings = $booking->getAllBookings();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings - Admin</title>
</head>
<body>
    <h1>Manage Bookings</h1>
    
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
    
    <h2>All Bookings</h2>
    <table border="1">
        <tr>
            <th>Booking ID</th>
            <th>Tourist</th>
            <th>Guide</th>
            <th>Package</th>
            <th>Spot</th>
            <th>Schedule</th>
            <th>PAX</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?php echo $b['booking_ID']; ?></td>
            <td><?php echo $b['tourist_name']; ?></td>
            <td><?php echo $b['guide_name'] ?? 'Not Assigned'; ?></td>
            <td><?php echo $b['tourPackage_Name']; ?></td>
            <td><?php echo $b['spots_Name']; ?></td>
            <td><?php echo $b['schedule_StartDateTime']; ?></td>
            <td><?php echo $b['booking_PAX']; ?></td>
            <td><?php echo $b['booking_Status']; ?></td>
            <td>
                <a href="booking-details.php?id=<?php echo $b['booking_ID']; ?>">View</a> |
                <a href="update-booking-status.php?id=<?php echo $b['booking_ID']; ?>">Update Status</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
