<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once "../../classes/booking-manager.php";

$method_id = intval($_GET['method_id'] ?? 0);
$amount = floatval($_GET['amount'] ?? 0);

if (!$method_id || !$amount) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$bookingManager = new BookingManager();
$total = $bookingManager->calculateTotalWithFee($amount, $method_id);
$fee = $total - $amount;

echo json_encode([
    'base_amount' => $amount,
    'fee' => $fee,
    'total' => $total
]);