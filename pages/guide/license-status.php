<?php
require_once __DIR__ . '/../../classes/guide.php';
session_start();

$guide = new Guide();

// Determine guide_ID: prefer GET param, else session person_ID
$guide_ID = isset($_GET['guide_ID']) ? (int)$_GET['guide_ID'] : (int)($_SESSION['person_ID'] ?? 0);

$licenses = [];
if ($guide_ID > 0) {
    $licenses = $guide->getAllLicenses($guide_ID);
    if ($licenses === false) $licenses = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My License Status</title>
</head>
<body>
    <h1>Guide License Status</h1>
    <?php if ($guide_ID <= 0): ?>
        <div>Please log in or provide a guide_ID in query string.</div>
    <?php else: ?>
        <?php if (empty($licenses)): ?>
            <div>No licenses found for guide ID <?= $guide_ID ?></div>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>License ID</th>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Issue</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Verification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $l): ?>
                    <tr>
                        <td><?= (int)$l['license_ID'] ?></td>
                        <td><?= htmlspecialchars($l['license_number']) ?></td>
                        <td><?= htmlspecialchars($l['license_type'] ?? '') ?></td>
                        <td><?= htmlspecialchars($l['issue_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($l['expiry_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($l['status']) ?></td>
                        <td><?= htmlspecialchars($l['verification_status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
