<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/booking-manager.php";

$bookingManager = new BookingManager();
$user = $_SESSION['user'];
$booking_id = $_GET['id'] ?? 0;

if (!$booking_id) {
    header('Location: my-bookings.php');
    exit;
}

$booking = $bookingManager->getBookingDetails($booking_id);

if (!$booking || $booking['customer_ID'] != $user['person_ID']) {
    header('Location: my-bookings.php');
    exit;
}

$companions = $bookingManager->getBookingCompanions($booking_id);
$payment = $bookingManager->getPaymentByBooking($booking_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 900px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Booking Details</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="my-bookings.php" style="color: white; margin-right: 15px;">My Bookings</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <a href="my-bookings.php" style="display: inline-block; margin-bottom: 20px; color: #1976d2;">
            &larr; Back to My Bookings
        </a>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Booking #<?= $booking['booking_ID'] ?></h2>
                <span style="padding: 8px 16px; background: 
                    <?= $booking['booking_Status'] === 'confirmed' ? '#4caf50' : 
                        ($booking['booking_Status'] === 'pending' ? '#ff9800' : 
                        ($booking['booking_Status'] === 'completed' ? '#9c27b0' : 
                        ($booking['booking_Status'] === 'cancelled' ? '#f44336' : '#757575'))) ?>; 
                    color: white; font-size: 14px; border-radius: 3px;">
                    <?= htmlspecialchars(ucfirst($booking['booking_Status'])) ?>
                </span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h3>Tour Package</h3>
                    <p><strong><?= htmlspecialchars($booking['tourPackage_Name']) ?></strong></p>
                    
                    <h3>Tourist</h3>
                    <p><?= htmlspecialchars($booking['tourist_name']) ?></p>
                    
                    <h3>Number of Persons</h3>
                    <p><?= $booking['booking_PAX'] ?> PAX</p>
                </div>
                
                <div>
                    <h3>Schedule</h3>
                    <p>
                        <?php if ($booking['schedule_StartDateTime']): ?>
                            <strong>Start:</strong> <?= date('F d, Y h:i A', strtotime($booking['schedule_StartDateTime'])) ?><br>
                            <?php if ($booking['schedule_EndDateTime']): ?>
                                <strong>End:</strong> <?= date('F d, Y h:i A', strtotime($booking['schedule_EndDateTime'])) ?><br>
                            <?php endif; ?>
                        <?php else: ?>
                            TBA
                        <?php endif; ?>
                    </p>
                    
                    <h3>Meeting Spot</h3>
                    <p><?= htmlspecialchars($booking['schedule_MeetingSpot'] ?? 'TBA') ?></p>
                    
                    <h3>Tour Guide</h3>
                    <p><?= htmlspecialchars($booking['guide_name'] ?? 'TBA') ?></p>
                </div>
            </div>

            <?php if ($booking['spots_Name']): ?>
                <div style="margin-top: 20px; padding: 15px; background: #f5f5f5;">
                    <h3>Main Tourist Spot</h3>
                    <p><strong><?= htmlspecialchars($booking['spots_Name']) ?></strong></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($companions)): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h3>Companions</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Name</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companions as $companion): ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?= htmlspecialchars($companion['companion_name']) ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?= htmlspecialchars($companion['companioncategory_name']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($payment): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h3>Payment Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p><strong>Amount:</strong> PHP <?= number_format($payment['paymentinfo_Amount'], 2) ?></p>
                    <p><strong>Payment Date:</strong> <?= date('F d, Y', strtotime($payment['paymentinfo_Date'])) ?></p>
                </div>
                <div>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment['method_name'] ?? 'N/A') ?></p>
                    <p><strong>Transaction Reference:</strong> <?= htmlspecialchars($payment['transaction_reference'] ?? 'N/A') ?></p>
                    <p>
                        <strong>Payment Status:</strong> 
                        <span style="padding: 4px 8px; background: 
                            <?= $payment['transaction_status'] === 'completed' ? '#4caf50' : 
                                ($payment['transaction_status'] === 'pending' ? '#ff9800' : '#f44336') ?>; 
                            color: white; font-size: 12px;">
                            <?= htmlspecialchars(ucfirst($payment['transaction_status'] ?? 'N/A')) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($booking['booking_Status'] === 'pending' || $booking['booking_Status'] === 'confirmed'): ?>
        <div style="text-align: center; padding: 20px;">
            <a href="cancel-booking.php?id=<?= $booking['booking_ID'] ?>" 
               onclick="return confirm('Are you sure you want to cancel this booking?')"
               style="padding: 12px 24px; background: #f44336; color: white; text-decoration: none; display: inline-block;">
                Cancel Booking
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
