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

if (!isset($_SESSION['pending_booking']) || !isset($_GET['schedule_id'])) {
    header('Location: ../public/browse-packages.php');
    exit;
}

$schedule_id = $_GET['schedule_id'];
$schedule = $tourManager->getScheduleById($schedule_id);

if (!$schedule) {
    header('Location: ../public/browse-packages.php');
    exit;
}

$error = '';
$success = '';
$paymentMethods = $bookingManager->getAllPaymentMethods();

// Process booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_PAX = intval($_POST['booking_PAX'] ?? 1);
    $method_ID = intval($_POST['method_ID'] ?? 0);
    $payment_amount = floatval($_POST['payment_amount'] ?? 0);
    
    // Validate inputs
    if ($booking_PAX < 1) {
        $error = 'Number of persons must be at least 1.';
    } elseif (!$method_ID) {
        $error = 'Please select a payment method.';
    } elseif ($payment_amount <= 0) {
        $error = 'Invalid payment amount.';
    } else {
        // Calculate total with processing fee
        $total_with_fee = $bookingManager->calculateTotalWithFee($payment_amount, $method_ID);
        
        // Create booking with payment
        $result = $bookingManager->createBookingWithPayment(
            $_SESSION['user']['person_ID'],
            $schedule_id,
            $schedule['tourPackage_ID'],
            $booking_PAX,
            $total_with_fee,
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
            
            // Redirect to payment confirmation
            header("Location: payment-confirmation.php?booking_id=" . $result['booking_ID']);
            exit;
        } else {
            $error = 'Failed to create booking. Please try again.';
        }
    }
}

// Get price information
$pricing = $tourManager->getPackagePricing($schedule['tourPackage_ID']);
$base_price = $pricing['base_price'] ?? 0;
$price_per_person = $pricing['price_per_person'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Summary - Tourismo Zamboanga</title>
    <script>
        function updateTotal() {
            const pax = parseInt(document.getElementById('booking_PAX').value) || 1;
            const basePrice = <?= $base_price ?>;
            const pricePerPerson = <?= $price_per_person ?>;
            const methodId = document.getElementById('method_ID').value;
            
            // Calculate subtotal
            const subtotal = basePrice + (pricePerPerson * pax);
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            
            // Get processing fee
            fetch(`get-processing-fee.php?method_id=${methodId}&amount=${subtotal}`)
                .then(response => response.json())
                .then(data => {
                    const total = data.total;
                    const fee = data.fee;
                    
                    document.getElementById('processing_fee').textContent = fee.toFixed(2);
                    document.getElementById('total').textContent = total.toFixed(2);
                    document.getElementById('payment_amount').value = total.toFixed(2);
                });
        }

        function addCompanion() {
            const container = document.getElementById('companions');
            const index = container.children.length;
            
            const div = document.createElement('div');
            div.style.marginBottom = '10px';
            div.innerHTML = `
                <input type="text" name="companions[${index}][name]" placeholder="Companion Name" required>
                <select name="companions[${index}][category]" required>
                    <option value="">Select Category</option>
                    <?php foreach ($companionCategories as $category): ?>
                    <option value="<?= $category['companioncategory_ID'] ?>">
                        <?= htmlspecialchars($category['companioncategory_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" onclick="this.parentElement.remove();">Remove</button>
            `;
            
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h1>Booking Summary</h1>
        
        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 10px; margin-bottom: 20px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
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
            <div style="margin-bottom: 15px;">
                <label for="booking_PAX">Number of Persons (PAX):</label><br>
                <input type="number" id="booking_PAX" name="booking_PAX" min="1" 
                       max="<?= $schedule['schedule_Capacity'] ?>" value="1" required
                       onchange="updateTotal()">
            </div>

            <div style="margin-bottom: 15px;">
                <h3>Add Companions</h3>
                <div id="companions"></div>
                <button type="button" onclick="addCompanion()">+ Add Companion</button>
            </div>

            <div style="margin-bottom: 15px;">
                <h3>Price Breakdown</h3>
                <div style="background: #f5f5f5; padding: 10px;">
                    <div>Base Price: PHP <?= number_format($base_price, 2) ?></div>
                    <div>Price per Person: PHP <?= number_format($price_per_person, 2) ?></div>
                    <div>Subtotal: PHP <span id="subtotal">0.00</span></div>
                    <div>Processing Fee: PHP <span id="processing_fee">0.00</span></div>
                    <div style="font-weight: bold; margin-top: 10px;">
                        Total: PHP <span id="total">0.00</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="method_ID">Payment Method:</label><br>
                <select id="method_ID" name="method_ID" required onchange="updateTotal()">
                    <option value="">Select Payment Method</option>
                    <?php foreach ($paymentMethods as $method): ?>
                        <option value="<?= $method['method_ID'] ?>">
                            <?= htmlspecialchars($method['method_name']) ?> 
                            (<?= $method['processing_fee'] ?>% fee)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" id="payment_amount" name="payment_amount" value="0">

            <div style="margin-top: 20px;">
                <button type="submit" style="background: #1976d2; color: white; padding: 10px 20px; border: none;">
                    Proceed to Payment
                </button>
                <a href="../public/browse-packages.php" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        // Initialize total on page load
        updateTotal();
    </script>
</body>
</html>