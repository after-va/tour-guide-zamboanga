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

$schedule_id = $_GET['schedule_id'] ?? 0;
$user = $_SESSION['user'];
$error = '';
$success = '';

if (!$schedule_id) {
    header('Location: ../public/browse-packages.php');
    exit;
}

$schedule = $tourManager->getScheduleById($schedule_id);
if (!$schedule) {
    header('Location: ../public/browse-packages.php');
    exit;
}

$paymentMethods = $bookingManager->getAllPaymentMethods();
$companionCategories = $bookingManager->getCompanionCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_PAX = intval($_POST['booking_PAX'] ?? 1);
    $method_ID = intval($_POST['method_ID'] ?? 0);
    $payment_amount = floatval($_POST['payment_amount'] ?? 0);
    
    if ($booking_PAX < 1) {
        $error = 'Number of persons must be at least 1.';
    } elseif (!$method_ID) {
        $error = 'Please select a payment method.';
    } elseif ($payment_amount <= 0) {
        $error = 'Invalid payment amount.';
    } else {
        $result = $bookingManager->createBookingWithPayment(
            $user['person_ID'],
            $schedule_id,
            $schedule['tourPackage_ID'],
            $booking_PAX,
            $payment_amount,
            $method_ID
        );
        
        if ($result) {
            // Add companions if provided
            if (!empty($_POST['companions'])) {
                foreach ($_POST['companions'] as $companion) {
                    if (!empty($companion['name']) && !empty($companion['category'])) {
                        $bookingManager->addCompanionToBooking(
                            $result['booking_ID'],
                            $companion['name'],
                            $companion['category']
                        );
                    }
                }
            }
            
            $success = 'Booking created successfully! Reference: ' . $result['transaction_reference'];
            header('Location: booking-details.php?id=' . $result['booking_ID']);
            exit;
        } else {
            $error = 'Failed to create booking. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tour - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Book Tour</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="my-bookings.php" style="color: white; margin-right: 15px;">My Bookings</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 15px; margin-bottom: 20px; border: 1px solid #ef5350;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin-bottom: 20px; border: 1px solid #4caf50;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div style="background: #f5f5f5; padding: 20px; margin-bottom: 20px;">
            <h2><?= htmlspecialchars($schedule['tourPackage_Name']) ?></h2>
            <p><?= htmlspecialchars($schedule['tourPackage_Description']) ?></p>
            
            <div style="margin-top: 15px;">
                <strong>Schedule:</strong> <?= date('F d, Y h:i A', strtotime($schedule['schedule_StartDateTime'])) ?><br>
                <strong>Meeting Spot:</strong> <?= htmlspecialchars($schedule['schedule_MeetingSpot']) ?><br>
                <strong>Guide:</strong> <?= htmlspecialchars($schedule['guide_name'] ?? 'TBA') ?><br>
                <strong>Available Slots:</strong> <?= $schedule['schedule_Capacity'] ?>
            </div>
        </div>

        <form method="post" style="background: white; border: 1px solid #ddd; padding: 20px;">
            <h3>Booking Details</h3>
            
            <div style="margin-bottom: 15px;">
                <label for="booking_PAX">Number of Persons (PAX):</label><br>
                <input type="number" id="booking_PAX" name="booking_PAX" min="1" max="<?= $schedule['schedule_Capacity'] ?>" 
                       value="1" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="payment_amount">Payment Amount (PHP):</label><br>
                <input type="number" id="payment_amount" name="payment_amount" step="0.01" min="0.01" 
                       required style="width: 100%; padding: 8px; box-sizing: border-box;">
                <small>Enter the total amount you will pay for this booking.</small>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="method_ID">Payment Method:</label><br>
                <select id="method_ID" name="method_ID" required style="width: 100%; padding: 8px;">
                    <option value="">Select Payment Method</option>
                    <?php foreach ($paymentMethods as $method): ?>
                        <option value="<?= $method['method_ID'] ?>">
                            <?= htmlspecialchars($method['method_name']) ?>
                            <?php if ($method['processing_fee'] > 0): ?>
                                (Fee: PHP <?= number_format($method['processing_fee'], 2) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr style="margin: 20px 0;">

            <h3>Companions (Optional)</h3>
            <div id="companions-container">
                <div class="companion-row" style="margin-bottom: 10px; padding: 10px; background: #f9f9f9;">
                    <input type="text" name="companions[0][name]" placeholder="Companion Name" 
                           style="width: 60%; padding: 8px; margin-right: 5px;">
                    <select name="companions[0][category]" style="width: 35%; padding: 8px;">
                        <option value="">Category</option>
                        <?php foreach ($companionCategories as $category): ?>
                            <option value="<?= $category['companioncategory_ID'] ?>">
                                <?= htmlspecialchars($category['companioncategory_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <button type="button" onclick="addCompanion()" 
                    style="padding: 8px 15px; background: #757575; color: white; border: none; cursor: pointer; margin-bottom: 20px;">
                Add Another Companion
            </button>

            <hr style="margin: 20px 0;">

            <button type="submit" style="width: 100%; padding: 12px; background: #4caf50; color: white; border: none; cursor: pointer; font-size: 16px;">
                Confirm Booking
            </button>
        </form>
    </div>

    <script>
        let companionCount = 1;
        
        function addCompanion() {
            const container = document.getElementById('companions-container');
            const newRow = document.createElement('div');
            newRow.className = 'companion-row';
            newRow.style.cssText = 'margin-bottom: 10px; padding: 10px; background: #f9f9f9;';
            newRow.innerHTML = `
                <input type="text" name="companions[${companionCount}][name]" placeholder="Companion Name" 
                       style="width: 60%; padding: 8px; margin-right: 5px;">
                <select name="companions[${companionCount}][category]" style="width: 35%; padding: 8px;">
                    <option value="">Category</option>
                    <?php foreach ($companionCategories as $category): ?>
                        <option value="<?= $category['companioncategory_ID'] ?>">
                            <?= htmlspecialchars($category['companioncategory_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            `;
            container.appendChild(newRow);
            companionCount++;
        }
    </script>
</body>
</html>
