<?php
ob_clean();
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['account_ID']) || !is_numeric($_SESSION['account_ID'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$account_ID = (int)$_SESSION['account_ID'];
require_once __DIR__ . '/../../../../classes/activity-log.php';

try {
    $activityObj = new ActivityLogs();
    $success = $activityObj->markTouristNotificationsAsViewed($account_ID);
    echo json_encode(['success' => (bool)$success]);
} catch (Throwable $e) {
    error_log('mark_notifications_read error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false]);
}
exit;