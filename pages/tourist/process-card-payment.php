<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once "../../classes/booking-manager.php";

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$transaction_id = $data['transaction_id'] ?? 0;
$amount = $data['amount'] ?? 0;

if (!$transaction_id || !$amount) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// In a real implementation, you would:
// 1. Integrate with a payment gateway (e.g., Stripe)
// 2. Process the payment through the gateway
// 3. Handle the gateway's response
// 4. Update the transaction status accordingly

// For demonstration, we'll simulate a successful payment
$bookingManager = new BookingManager();
$result = $bookingManager->updateTransactionStatus(
    $transaction_id,
    'completed',
    json_encode([
        'gateway' => 'demo',
        'amount' => $amount,
        'processed_at' => date('Y-m-d H:i:s')
    ])
);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Failed to process payment']);
}