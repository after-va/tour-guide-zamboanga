<?php
// Simple CLI test script for Guide license methods
require_once __DIR__ . '/../classes/guide.php';

$guide = new Guide();

echo "Testing Guide license utilities\n";

// List pending licenses
$pending = $guide->listPendingLicenses();
if ($pending === false) {
    echo "Failed to list pending licenses: " . $guide->getLastError() . "\n";
} else {
    echo "Pending licenses found: " . count($pending) . "\n";
    foreach (array_slice($pending, 0, 5) as $row) {
        echo "- License ID: {$row['license_ID']}, number: {$row['license_number']}, guide_ID: {$row['guide_ID']}, name: {$row['name_first']} {$row['name_last']}\n";
    }
}

// If there's a pending license, try verifying (dry-run via prompt)
if (!empty($pending) && is_array($pending)) {
    $first = $pending[0];
    $license_ID = $first['license_ID'];

    echo "\nDo you want to try verifying license ID {$license_ID}? type 'yes' to proceed: ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    if (strtolower($line) === 'yes') {
        // For this test, verifier_person_ID will be set to 1 (assumes admin exists)
        $verifier = 1;
        $ok = $guide->verifyLicense($license_ID, $verifier, 'verified', 'active');
        if ($ok) {
            echo "License {$license_ID} marked as verified and active.\n";
        } else {
            echo "Failed to verify license: " . $guide->getLastError() . "\n";
        }
    } else {
        echo "Skipping verification test.\n";
    }
}

echo "Test complete.\n";
