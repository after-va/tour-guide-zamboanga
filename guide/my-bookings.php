<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

require_once "../php/Booking.php";

$booking = new Booking();
$myBookings = $booking->getBookingsByGuide($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - Guide</title>
</head>
<body>
    <h1>My Bookings</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="my-schedules.php">My Schedules</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>All Bookings for My Tours</h2>
    
    <?php if (count($myBookings) > 0): ?>
        <table border="1">
            <tr>
                <th>Booking ID</th>
                <th>Tourist Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Package</th>
                <th>Spot</th>
                <th>Schedule</th>
                <th>Meeting Spot</th>
                <th>PAX</th>
                <th>Status</th>
            </tr>
            <?php foreach ($myBookings as $b): ?>
            <tr>
                <td><?php echo $b['booking_ID']; ?></td>
                <td><?php echo $b['tourist_name']; ?></td>
                <td><?php echo $b['tourist_email']; ?></td>
                <td><?php echo $b['tourist_phone'] ?? 'N/A'; ?></td>
                <td><?php echo $b['tourPackage_Name']; ?></td>
                <td><?php echo $b['spots_Name']; ?></td>
                <td><?php echo $b['schedule_StartDateTime']; ?></td>
                <td><?php echo $b['schedule_MeetingSpot']; ?></td>
                <td><?php echo $b['booking_PAX']; ?></td>
                <td><?php echo $b['booking_Status']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No bookings yet.</p>
    <?php endif; ?>
</body>
</html>
