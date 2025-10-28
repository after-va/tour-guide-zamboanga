<?php
// Minimal endpoint to verify/reject guide licenses. Returns JSON.
require_once __DIR__ . '/../classes/guide.php';
session_start();

header('Content-Type: application/json');

$guide = new Guide();

$post = $_POST;
$license_ID = isset($post['license_ID']) ? (int)$post['license_ID'] : 0;
$action = isset($post['action']) ? $post['action'] : '';

if ($license_ID <= 0 || !in_array($action, ['verify', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Determine verifier person ID from session if available; fallback to 1
$verifier = $_SESSION['person_ID'] ?? 1;

if ($action === 'verify') {
    $ok = $guide->verifyLicense($license_ID, $verifier, 'verified', 'active');
} else {
    // reject
    $ok = $guide->verifyLicense($license_ID, $verifier, 'rejected', 'revoked');
}

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Operation completed']);
} else {
    echo json_encode(['success' => false, 'message' => $guide->getLastError()]);
}
