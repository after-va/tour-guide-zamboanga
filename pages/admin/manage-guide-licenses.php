<?php
require_once __DIR__ . '/../../classes/guide.php';
session_start();

$guide = new Guide();
$pending = $guide->listPendingLicenses();
if ($pending === false) {
    $error = $guide->getLastError();
    $pending = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Guide Licenses (Admin)</title>
</head>
<body>
    <h1>Pending Guide Licenses</h1>
    <div style="margin-bottom:12px;">
        <a href="/admin/export-licenses.php">Download CSV of Licenses</a>
    </div>
    <?php if (!empty($error)): ?>
        <div style="color:red"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>License ID</th>
                <th>Guide ID</th>
                <th>Guide Name</th>
                <th>License Number</th>
                <th>Type</th>
                <th>Issued</th>
                <th>Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="licenses-tbody">
            <?php foreach ($pending as $row): ?>
            <tr id="row-<?= (int)$row['license_ID'] ?>">
                <td><?= (int)$row['license_ID'] ?></td>
                <td><?= (int)$row['guide_ID'] ?></td>
                <td><?= htmlspecialchars(($row['name_first'] ?? '') . ' ' . ($row['name_last'] ?? '')) ?></td>
                <td><?= htmlspecialchars($row['license_number']) ?></td>
                <td><?= htmlspecialchars($row['license_type'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['issue_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['expiry_date'] ?? '') ?></td>
                <td>
                    <button data-id="<?= (int)$row['license_ID'] ?>" class="verify">Verify</button>
                    <button data-id="<?= (int)$row['license_ID'] ?>" class="reject">Reject</button>
                    <button data-copy="<?= htmlspecialchars($row['license_number']) ?>" class="copy">Copy</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    async function postAction(licenseID, action) {
        const form = new FormData();
        form.append('license_ID', licenseID);
        form.append('action', action);

        const res = await fetch('/admin/verify-license.php', {
            method: 'POST',
            body: form
        });
        return res.json();
    }

    document.addEventListener('click', function(e){
        if (e.target.matches('.verify') || e.target.matches('.reject')) {
            const id = e.target.getAttribute('data-id');
            const action = e.target.matches('.verify') ? 'verify' : 'reject';
            if (!confirm('Proceed with ' + action + ' for license ' + id + '?')) return;
            e.target.disabled = true;
            postAction(id, action).then(json => {
                if (json.success) {
                    const row = document.getElementById('row-' + id);
                    if (row) row.parentNode.removeChild(row);
                    alert('Success: ' + (json.message || 'Done'));
                } else {
                    alert('Error: ' + (json.message || 'Unknown'));
                    e.target.disabled = false;
                }
            }).catch(err => {
                alert('Request failed: ' + err);
                e.target.disabled = false;
            });
        }

        // Copy license number to clipboard
        if (e.target.matches('.copy')) {
            const license = e.target.getAttribute('data-copy');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(license).then(() => {
                    alert('License copied: ' + license);
                }).catch(err => alert('Copy failed: ' + err));
            } else {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = license;
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    alert('License copied: ' + license);
                } catch (err) {
                    alert('Copy failed: ' + err);
                }
                document.body.removeChild(ta);
            }
        }
    });
    </script>
</body>
</html>
