<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

require_once "../php/Booking.php";
require_once "../php/Guide.php";

$booking = new Booking();
$guide = new Guide();

$myBookings = $booking->getBookingsByGuide($_SESSION['user_id']);
$mySchedules = $guide->getGuideSchedules($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guide Dashboard - Tour Guide System</title>
</head>
<body>
    <h1>Guide Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="my-schedules.php">My Schedules</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>Overview</h2>
    <p>Total Schedules: <?php echo count($mySchedules); ?></p>
    <p>Total Bookings: <?php echo count($myBookings); ?></p>
    
    <h3>Upcoming Schedules</h3>
    <?php if (count($mySchedules) > 0): ?>
        <table border="1">
            <tr>
                <th>Schedule ID</th>
                <th>Package</th>
                <th>Spot</th>
                <th>Start Date/Time</th>
                <th>Bookings</th>
                <th>Actions</th>
            </tr>
            <?php foreach (array_slice($mySchedules, 0, 5) as $s): ?>
            <tr>
                <td><?php echo $s['schedule_ID']; ?></td>
                <td><?php echo $s['tourPackage_Name']; ?></td>
                <td><?php echo $s['spots_Name']; ?></td>
                <td><?php echo $s['schedule_StartDateTime']; ?></td>
                <td><?php echo $s['total_bookings']; ?></td>
                <td><a href="schedule-details.php?id=<?php echo $s['schedule_ID']; ?>">View</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No schedules assigned yet.</p>
    <?php endif; ?>
    
    <h3>Recent Bookings</h3>
    <?php if (count($myBookings) > 0): ?>
        <table border="1">
            <tr>
                <th>Booking ID</th>
                <th>Tourist</th>
                <th>Package</th>
                <th>Schedule</th>
                <th>PAX</th>
                <th>Status</th>
            </tr>
            <?php foreach (array_slice($myBookings, 0, 5) as $b): ?>
            <tr>
                <td><?php echo $b['booking_ID']; ?></td>
                <td><?php echo $b['tourist_name']; ?></td>
                <td><?php echo $b['tourPackage_Name']; ?></td>
                <td><?php echo $b['schedule_StartDateTime']; ?></td>
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
