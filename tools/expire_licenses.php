<?php
// CLI script to expire due licenses. Run from CLI, suitable for cron.
require_once __DIR__ . '/../classes/guide.php';

$guide = new Guide();
$res = $guide->expireDueLicenses();
if ($res === false) {
    echo "Error: " . $guide->getLastError() . PHP_EOL;
    exit(1);
}

echo "Expired licenses updated: " . intval($res) . PHP_EOL;
exit(0);
