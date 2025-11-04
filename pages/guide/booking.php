<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tour Guide') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended'){
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending'){
    header('Location: account-pending.php');
}
require_once "../../classes/guide.php";
require_once "../../classes/booking.php";

$bookingObj = new Booking();
$guideObj = new Guide();

$guide_ID = $guideObj->getGuide_ID($_SESSION['user']['account_ID']);

$bookings = $bookingObj->getBookingByGuideID($guide_ID);
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
        <a href="booking.php">Bookings</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="account-change.php">Change to Tourist</a>
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
    <p><?= $guide_ID ?></p>
    <?php if (!empty($bookings)): ?>
        <table border = 1>
            <tr>
                <th>No.</th>
                <th>Package Name</th>
                <th>Description</th>
                <th>Schedule Days</th>
                <th>Tourist </th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Tour Spots</th>
                <th>Actions</th>
            </tr>

            <?php $no = 1; foreach ($bookings as $i => $booking){ 
                    if($booking['booking_status'] == 'Pending for Payment' || $booking['booking_status'] == 'Pending for Approval'|| $booking['booking_status'] == 'Approved' ){?>
                <tr>
                    <td><?= $no++;?></td>
                    <td><?= htmlspecialchars($booking['tourpackage_name']) ?></td>
                    <td><?= htmlspecialchars($booking['tourpackage_desc']) ?></td>
                    <td><?= htmlspecialchars($booking['schedule_days']) ?> days</td>
                    <td><?= htmlspecialchars($booking['tourist_name']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_start_date']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_end_date']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_status']) ?></td>
                    <td><?= htmlspecialchars($booking['tour_spots'] ?? '—') ?></td>
                    <?php if ($booking['booking_status'] =='Pending for Payment'){ ?>
                    <td>
                        <a href="booking-view.php?id=<?= $booking['booking_ID'] ?? '' ?>">View Details</a>
                    </td>
                    <?php }else if ($booking['booking_status'] =='Pending for Approval'){ ?>
                    <td>
                        <a href="booking-approve.php?id=<?= $booking['booking_ID'] ?? '' ?>" onclick="return confirm('Are you sure you want to Approve this booking?')">Approve</a> |
                        <a href="booking-view.php?id=<?= $booking['booking_ID'] ?? '' ?>">Reject</a>
                    </td>
                    <?php } else { ?>
                    <td>

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
