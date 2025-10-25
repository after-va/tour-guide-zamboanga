<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/CustomPackage.php";
require_once "../php/Payment.php";
require_once "../php/Booking.php";
require_once "../php/Schedule.php";
require_once "../php/Notification.php";

$customPackage = new CustomPackage();
$payment = new Payment();
$booking = new Booking();
$schedule = new Schedule();
$notification = new Notification();

$request_ID = $_GET['request_id'] ?? 0;

$request = $customPackage->getRequestById($request_ID);
if (!$request || $request['tourist_ID'] != $_SESSION['user_id'] || $request['request_status'] != 'accepted') {
    header("Location: my-requests.php");
    exit();
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'process_payment') {
    $db = $payment->connect();
    $db->beginTransaction();
    
    try {
        // Create a schedule for this custom package
        $scheduleData = [
            'tourPackage_ID' => $request['tourPackage_ID'],
            'guide_ID' => $request['guide_ID'],
            'schedule_StartDateTime' => $_POST['confirmed_date'] . ' ' . $_POST['confirmed_time'],
            'schedule_EndDateTime' => null, // Will be calculated based on duration
            'schedule_Capacity' => $request['number_of_pax'],
            'schedule_MeetingSpot' => $_POST['meeting_spot']
        ];
        
        $schedule_ID = $schedule->createSchedule(
            $scheduleData['tourPackage_ID'],
            $scheduleData['guide_ID'],
            $scheduleData['schedule_StartDateTime'],
            $scheduleData['schedule_EndDateTime'],
            $scheduleData['schedule_Capacity'],
            $scheduleData['schedule_MeetingSpot']
        );
        
        if (!$schedule_ID) {
            throw new Exception("Failed to create schedule");
        }
        
        // Create booking
        $booking_ID = $booking->createBooking(
            $_SESSION['user_id'],
            $schedule_ID,
            $request['tourPackage_ID'],
            $request['number_of_pax'],
            []
        );
        
        if (!$booking_ID) {
            throw new Exception("Failed to create booking");
        }
        
        // Create payment record
        $payment_amount = $_POST['payment_amount'];
        $payment_ID = $payment->createPayment($booking_ID, $payment_amount);
        
        if (!$payment_ID) {
            throw new Exception("Failed to create payment");
        }
        
        // Process payment transaction
        $transaction_data = [
            'method_ID' => $_POST['payment_method'],
            'transaction_reference' => 'TXN-' . time() . '-' . $booking_ID,
            'transaction_status' => 'completed', // In real scenario, this would be 'pending' until payment gateway confirms
            'payment_gateway' => $_POST['payment_gateway'] ?? 'Manual'
        ];
        
        $transaction_ID = $payment->createPaymentTransaction($payment_ID, $transaction_data);
        
        if (!$transaction_ID) {
            throw new Exception("Failed to create transaction");
        }
        
        // Update booking status to confirmed
        $booking->updateBookingStatus($booking_ID, 'Confirmed', $_SESSION['user_id'], 'Payment completed');
        
        // Update custom package request status to completed
        $updateSql = "UPDATE Custom_Package_Request SET request_status = 'completed' WHERE request_ID = :request_ID";
        $updateQuery = $db->prepare($updateSql);
        $updateQuery->bindParam(":request_ID", $request_ID);
        $updateQuery->execute();
        
        // Notify guide
        $notification->createNotification(
            $request['guide_ID'],
            'payment_received',
            'Payment Received for Custom Package',
            $_SESSION['full_name'] . ' has completed payment for "' . $request['request_title'] . '". Booking confirmed!',
            'guide/my-bookings.php?id=' . $booking_ID
        );
        
        $db->commit();
        
        $success = true;
        $booking_reference = $booking_ID;
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Payment processing failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 800px; margin: 0 auto; }
        .success-box { background: #d4edda; color: #155724; padding: 30px; margin: 20px 0; border-radius: 8px; text-align: center; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .payment-summary { background: #f0f0f0; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .summary-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .summary-item:last-child { border-bottom: none; font-weight: bold; font-size: 18px; }
        .form-group { margin: 20px 0; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        .btn { padding: 12px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 16px; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .request-info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment for Custom Package</h1>
        
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="my-requests.php">My Requests</a>
            <a href="my-bookings.php">My Bookings</a>
            <a href="logout.php">Logout</a>
        </nav>
        
        <?php if (isset($success) && $success): ?>
            <div class="success-box">
                <h2>✓ Payment Successful!</h2>
                <p>Your booking has been confirmed.</p>
                <p><strong>Booking Reference:</strong> #<?php echo $booking_reference; ?></p>
                <br>
                <a href="my-bookings.php" class="btn btn-success">View My Bookings</a>
                <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
            </div>
        <?php else: ?>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="request-info">
                <h2><?php echo htmlspecialchars($request['request_title']); ?></h2>
                <p><strong>Tour Guide:</strong> <?php echo htmlspecialchars($request['guide_name']); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($request['preferred_duration']); ?></p>
                <p><strong>Number of PAX:</strong> <?php echo $request['number_of_pax']; ?></p>
            </div>
            
            <h2>Booking & Payment Details</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="process_payment">
                
                <div class="form-group">
                    <label>Confirmed Date: *</label>
                    <input type="date" name="confirmed_date" 
                           value="<?php echo $request['preferred_date'] ?: date('Y-m-d', strtotime('+3 days')); ?>" 
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    <small>Select the date for your tour</small>
                </div>
                
                <div class="form-group">
                    <label>Start Time: *</label>
                    <input type="time" name="confirmed_time" value="08:00" required>
                </div>
                
                <div class="form-group">
                    <label>Meeting Spot: *</label>
                    <input type="text" name="meeting_spot" required placeholder="e.g., Hotel Lobby, City Hall, etc.">
                </div>
                
                <div class="form-group">
                    <label>Payment Amount (₱): *</label>
                    <input type="number" name="payment_amount" step="0.01" min="1" required 
                           placeholder="Enter agreed amount with guide">
                    <small>Enter the amount you agreed upon with the guide</small>
                </div>
                
                <div class="form-group">
                    <label>Payment Method: *</label>
                    <select name="payment_method" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="1">Credit/Debit Card</option>
                        <option value="2">GCash</option>
                        <option value="3">PayMaya</option>
                        <option value="4">Bank Transfer</option>
                        <option value="5">Cash</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Payment Gateway:</label>
                    <select name="payment_gateway">
                        <option value="Manual">Manual Payment</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Stripe">Stripe</option>
                        <option value="GCash">GCash</option>
                        <option value="PayMaya">PayMaya</option>
                    </select>
                </div>
                
                <div class="payment-summary">
                    <h3>Payment Summary</h3>
                    <div class="summary-item">
                        <span>Package Request:</span>
                        <span><?php echo htmlspecialchars($request['request_title']); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Tour Guide:</span>
                        <span><?php echo htmlspecialchars($request['guide_name']); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Number of PAX:</span>
                        <span><?php echo $request['number_of_pax']; ?> person(s)</span>
                    </div>
                    <div class="summary-item">
                        <span>Budget Range:</span>
                        <span><?php echo htmlspecialchars($request['budget_range']); ?></span>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Process Payment & Confirm Booking</button>
                    <a href="my-requests.php" class="btn btn-secondary">Cancel</a>
                </div>
                
                <p style="margin-top: 20px; color: #666; font-size: 14px;">
                    <strong>Note:</strong> By proceeding with payment, you agree to the terms and conditions. 
                    Your booking will be confirmed once payment is processed.
                </p>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
