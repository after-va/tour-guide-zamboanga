<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/booking-manager.php";
require_once "../../classes/tour-manager.php";

$bookingManager = new BookingManager();
$tourManager = new TourManager();

$booking_id = $_GET['booking_id'] ?? 0;
if (!$booking_id) {
    header('Location: ../public/browse-packages.php');
    exit;
}

// Get booking details
$booking = $bookingManager->getBookingById($booking_id);
if (!$booking || $booking['customer_ID'] != $_SESSION['user']['person_ID']) {
    header('Location: ../public/browse-packages.php');
    exit;
}

// Get payment details
$payment = $bookingManager->getPaymentDetailsByBooking($booking_id);

// Handle payment confirmation (e.g., for cash payments)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $transaction_id = $_POST['transaction_id'] ?? 0;
    
    // Only allow confirmation for cash payments
    if ($payment['method_type'] === 'cash') {
        $result = $bookingManager->updateTransactionStatus(
            $transaction_id,
            'completed',
            json_encode(['confirmed_by' => $_SESSION['user']['person_ID']])
        );
        
        if ($result) {
            header("Location: my-bookings.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Confirmation - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h1>Payment Confirmation</h1>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h2>Booking Details</h2>
            <div style="margin-bottom: 15px;">
                <strong>Package:</strong> <?= htmlspecialchars($booking['tourPackage_Name']) ?><br>
                <strong>Schedule:</strong> <?= date('F d, Y h:i A', strtotime($booking['schedule_StartDateTime'])) ?><br>
                <strong>Number of Persons:</strong> <?= $booking['booking_PAX'] ?><br>
                <strong>Status:</strong> <?= ucfirst($booking['booking_Status']) ?>
            </div>

            <h2>Payment Details</h2>
            <div style="margin-bottom: 15px;">
                <strong>Amount:</strong> PHP <?= number_format($payment['paymentinfo_Amount'], 2) ?><br>
                <strong>Payment Method:</strong> <?= htmlspecialchars($payment['method_name']) ?><br>
                <strong>Transaction Reference:</strong> <?= htmlspecialchars($payment['transaction_reference']) ?><br>
                <strong>Status:</strong> <?= ucfirst($payment['transaction_status']) ?>
            </div>

            <?php if ($payment['method_type'] === 'cash' && $payment['transaction_status'] === 'pending'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #e3f2fd;">
                    <h3>Cash Payment Instructions</h3>
                    <p>Please make your cash payment at our office:</p>
                    <p>
                        Tourismo Zamboanga Office<br>
                        123 Main Street, Zamboanga City<br>
                        Operating Hours: 9:00 AM - 5:00 PM
                    </p>
                    
                    <form method="post" style="margin-top: 15px;">
                        <input type="hidden" name="transaction_id" value="<?= $payment['transaction_ID'] ?>">
                        <button type="submit" name="confirm_payment" 
                                style="background: #1976d2; color: white; padding: 10px 20px; border: none;">
                            I Have Made the Payment
                        </button>
                    </form>
                </div>
            <?php elseif ($payment['method_type'] === 'bank'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #e3f2fd;">
                    <h3>Bank Transfer Instructions</h3>
                    <p>Please transfer the payment to:</p>
                    <p>
                        Bank: Sample Bank<br>
                        Account Name: Tourismo Zamboanga<br>
                        Account Number: 1234-5678-9012<br>
                        Reference: <?= htmlspecialchars($payment['transaction_reference']) ?>
                    </p>
                    <p>After making the transfer, please email the proof of payment to support@tourismozamboanga.com</p>
                </div>
            <?php elseif ($payment['method_type'] === 'ewallet'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #e3f2fd;">
                    <h3>E-Wallet Payment Instructions</h3>
                    <p>Please send your payment to:</p>
                    <p>
                        <?= htmlspecialchars($payment['method_name']) ?> Number: 0917-123-4567<br>
                        Account Name: Tourismo Zamboanga<br>
                        Reference: <?= htmlspecialchars($payment['transaction_reference']) ?>
                    </p>
                    <p>After sending the payment, please email the screenshot to support@tourismozamboanga.com</p>
                </div>
            <?php elseif ($payment['method_type'] === 'card'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #e3f2fd;">
                    <h3>Card Payment</h3>
                    <p>You will be redirected to our secure payment gateway to complete your payment.</p>
                    <button onclick="processCardPayment()" 
                            style="background: #1976d2; color: white; padding: 10px 20px; border: none;">
                        Proceed to Payment Gateway
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px;">
            <a href="my-bookings.php">View My Bookings</a>
        </div>
    </div>

    <?php if ($payment['method_type'] === 'card'): ?>
    <script>
        function processCardPayment() {
            // Here you would integrate with your payment gateway (e.g., Stripe)
            // For demonstration, we'll simulate a successful payment
            alert('In a real implementation, this would connect to a payment gateway.');
            
            // Simulate successful payment
            fetch('process-card-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    transaction_id: '<?= $payment['transaction_ID'] ?>',
                    amount: '<?= $payment['paymentinfo_Amount'] ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'my-bookings.php';
                } else {
                    alert('Payment failed. Please try again.');
                }
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>