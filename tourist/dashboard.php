<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/Booking.php";
require_once "../php/Schedule.php";

$booking = new Booking();
$schedule = new Schedule();

$myBookings = $booking->getBookingsByCustomer($_SESSION['user_id']);
$availableSchedules = $schedule->getAvailableSchedules();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tourist Dashboard - Tour Guide System</title>
</head>
<body>
    <h1>Tourist Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="browse-tours.php">Browse Tours</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>My Bookings Summary</h2>
    <p>Total Bookings: <?php echo count($myBookings); ?></p>
    
    <h3>Recent Bookings</h3>
    <?php if (count($myBookings) > 0): ?>
        <table border="1">
            <tr>
                <th>Booking ID</th>
                <th>Package</th>
                <th>Guide</th>
                <th>Schedule</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach (array_slice($myBookings, 0, 5) as $b): ?>
            <tr>
                <td><?php echo $b['booking_ID']; ?></td>
                <td><?php echo $b['tourPackage_Name']; ?></td>
                <td><?php echo $b['guide_name'] ?? 'Not Assigned'; ?></td>
                <td><?php echo $b['schedule_StartDateTime']; ?></td>
                <td><?php echo $b['booking_Status']; ?></td>
                <td><a href="booking-details.php?id=<?php echo $b['booking_ID']; ?>">View</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No bookings yet.</p>
    <?php endif; ?>
    
    <h3>Available Tours</h3>
    <p>Check out our <a href="browse-tours.php">available tours</a> and book your next adventure!</p>
</body>
</html>
