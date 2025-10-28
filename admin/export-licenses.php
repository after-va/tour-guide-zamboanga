<?php
require_once __DIR__ . '/../classes/guide.php';

$guide = new Guide();
$licenses = $guide->getAllLicenses();
if ($licenses === false) {
    http_response_code(500);
    echo "Failed to fetch licenses: " . $guide->getLastError();
    exit;
}

$filename = 'guide_licenses_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
// Header row
fputcsv($out, ['license_ID','guide_ID','license_number','license_type','issue_date','expiry_date','status','verification_status','verified_by','verified_at','created_at','updated_at']);

foreach ($licenses as $row) {
    fputcsv($out, [
        $row['license_ID'] ?? '',
        $row['guide_ID'] ?? '',
        $row['license_number'] ?? '',
        $row['license_type'] ?? '',
        $row['issue_date'] ?? '',
        $row['expiry_date'] ?? '',
        $row['status'] ?? '',
        $row['verification_status'] ?? '',
        $row['verified_by'] ?? '',
        $row['verified_at'] ?? '',
        $row['created_at'] ?? '',
        $row['updated_at'] ?? ''
    ]);
}

fclose($out);
exit;
