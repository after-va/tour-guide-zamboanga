<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/Booking.php";

$booking = new Booking();
$myBookings = $booking->getBookingsByCustomer($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - Tourist</title>
</head>
<body>
    <h1>My Bookings</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="browse-tours.php">Browse Tours</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>All My Bookings</h2>
    
    <?php if (count($myBookings) > 0): ?>
        <table border="1">
            <tr>
                <th>Booking ID</th>
                <th>Package</th>
                <th>Spot</th>
                <th>Guide</th>
                <th>Schedule</th>
                <th>Meeting Spot</th>
                <th>PAX</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($myBookings as $b): ?>
            <tr>
                <td><?php echo $b['booking_ID']; ?></td>
                <td><?php echo $b['tourPackage_Name']; ?></td>
                <td><?php echo $b['spots_Name']; ?></td>
                <td><?php echo $b['guide_name'] ?? 'Not Assigned'; ?></td>
                <td><?php echo $b['schedule_StartDateTime']; ?></td>
                <td><?php echo $b['schedule_MeetingSpot']; ?></td>
                <td><?php echo $b['booking_PAX']; ?></td>
                <td><?php echo $b['booking_Status']; ?></td>
                <td><?php echo $b['paymentinfo_Amount'] ? 'PHP ' . number_format($b['paymentinfo_Amount'], 2) : 'Not Paid'; ?></td>
                <td>
                    <a href="booking-details.php?id=<?php echo $b['booking_ID']; ?>">View</a>
                    <?php if ($b['booking_Status'] == 'Pending'): ?>
                        | <a href="cancel-booking.php?id=<?php echo $b['booking_ID']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no bookings yet.</p>
        <p><a href="browse-tours.php">Browse available tours</a></p>
    <?php endif; ?>
</body>
</html>
