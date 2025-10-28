<?php
// Minimal endpoint to add a license for an existing guide. Returns JSON.
require_once __DIR__ . '/../classes/guide.php';
session_start();

header('Content-Type: application/json');

$guide = new Guide();

$post = $_POST;
$guide_ID = isset($post['guide_ID']) ? (int)$post['guide_ID'] : 0;
$license_number = isset($post['license_number']) ? trim($post['license_number']) : '';
$license_type = isset($post['license_type']) ? trim($post['license_type']) : null;
$issue_date = isset($post['issue_date']) ? trim($post['issue_date']) : null;
$expiry_date = isset($post['expiry_date']) ? trim($post['expiry_date']) : null;
$issuing_authority = isset($post['issuing_authority']) ? trim($post['issuing_authority']) : null;

if ($guide_ID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: guide_ID']);
    exit;
}

// If license_number not provided, generate one
if ($license_number === '') {
    $license_number = $guide->generateLicenseNumber();
    if ($license_number === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to generate license number']);
        exit;
    }
}

$ok = $guide->addLicense($guide_ID, $license_number, $license_type, $issue_date, $expiry_date, $issuing_authority);
if ($ok) {
    echo json_encode(['success' => true, 'message' => 'License added']);
} else {
    echo json_encode(['success' => false, 'message' => $guide->getLastError()]);
}
