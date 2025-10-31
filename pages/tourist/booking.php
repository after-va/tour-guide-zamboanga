<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/booking.php";
require_once "../../classes/tourist.php";

$tourist_ID = $_SESSION['user']['account_ID'];
$touristObj = new Tourist();
$bookingObj = new Booking();


$bookings = $bookingObj->viewBookingByTourist($tourist_ID);
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
        <div class="alert-success">
            <?= htmlspecialchars($_SESSION['success']); ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-error">
            <?= htmlspecialchars($_SESSION['error']); ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <p><a href="tour-packages-browse.php">Browse Tour Packages</a></p>
    <p><a href="booking-history.php">View Booking History</a></p>

    <?php if (!empty($bookings)): ?>
        <table border = 1>
            <tr>
                <th>No.</th>
                <th>Package Name</th>
                <th>Description</th>
                <th>Schedule Days</th>
                <th>Tour Guide</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Tour Spots</th>
                <th>Actions</th>
            </tr>

            <?php $no = 1; foreach ($bookings as $i => $booking){ 
                    if($booking['booking_status'] == 'Pending for Payment' || $booking['booking_status'] == 'Pending for Approval'){?>
                <tr>
                    <td><?= $no++;?></td>
                    <td><?= htmlspecialchars($booking['tourpackage_name']) ?></td>
                    <td><?= htmlspecialchars($booking['tourpackage_desc']) ?></td>
                    <td><?= htmlspecialchars($booking['schedule_days']) ?> days</td>
                    <td><?= htmlspecialchars($booking['guide_name']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_start_date']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_end_date']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_status']) ?></td>
                    <td><?= htmlspecialchars($booking['tour_spots'] ?? '—') ?></td>
                    <?php if ($booking['booking_status'] =='Pending for Payment'){ ?>
                    <td>
                        <a href="payment.php?id=<?= $booking['booking_ID'] ?>">Proceed to Payment</a> |
                        <a href="booking-cancel.php?id=<?= $booking['booking_ID'] ?>" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a> |
                        <a href="booking-view.php?id=<?= $booking['booking_ID'] ?>">View</a>
                    </td>
                    <?php }else if ($booking['booking_status'] =='Pending for Approval' || $booking['booking_status'] =='In Progress'){ ?>
                    <td>
                        <a href="booking-cancel.php?id=<?= $booking['booking_ID'] ?>" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a> |
                        <a href="booking-view.php?id=<?= $booking['booking_ID'] ?>">View</a>
                    </td>
                    <?php } ?>
                    
                </tr>
                
            <?php }} ?>
        </table>
    <?php else: ?>
        <p><em>You currently have no bookings.</em></p>
    <?php endif; ?>

    <!-- 'Pending for Payment','Pending for Approval','Approved','In Progress','Completed','Cancelled','Refunded','Failed','Rejected by the Guide','Booking Expired — Payment Not Completed','Booking Expired — Guide Did Not Confirm in Time' -->
</body>
</html>
