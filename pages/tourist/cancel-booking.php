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

$booking = $bookingManager->getBookingById($booking_id);

if (!$booking || $booking['customer_ID'] != $user['person_ID']) {
    header('Location: my-bookings.php');
    exit;
}

if ($booking['booking_Status'] !== 'pending' && $booking['booking_Status'] !== 'confirmed') {
    header('Location: booking-details.php?id=' . $booking_id);
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason'] ?? '');
    
    if (empty($reason)) {
        $error = 'Please provide a reason for cancellation.';
    } else {
        $result = $bookingManager->cancelBooking($booking_id, $user['person_ID'], $reason);
        
        if ($result) {
            $_SESSION['success_message'] = 'Booking cancelled successfully.';
            header('Location: my-bookings.php');
            exit;
        } else {
            $error = 'Failed to cancel booking. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 600px; margin: 50px auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Cancel Booking</h1>
        </header>

        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 15px; margin-bottom: 20px; border: 1px solid #ef5350;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; margin-bottom: 20px;">
            <strong>Warning:</strong> You are about to cancel Booking #<?= $booking_id ?>. This action cannot be undone.
        </div>

        <form method="post" style="background: white; border: 1px solid #ddd; padding: 20px;">
            <div style="margin-bottom: 15px;">
                <label for="reason">Reason for Cancellation:</label><br>
                <textarea id="reason" name="reason" rows="4" required 
                          style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" 
                        style="flex: 1; padding: 12px; background: #f44336; color: white; border: none; cursor: pointer;">
                    Confirm Cancellation
                </button>
                <a href="booking-details.php?id=<?= $booking_id ?>" 
                   style="flex: 1; padding: 12px; background: #757575; color: white; text-decoration: none; text-align: center; display: block;">
                    Go Back
                </a>
            </div>
        </form>
    </div>
</body>
</html>
