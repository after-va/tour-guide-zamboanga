<?php
// Admin Manage Users - Approve Guides and manage guide licenses
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/guide.php";
require_once "../../classes/database.php";

$guideObj = new Guide();
$db = new Database();
$connection = $db->connect();

$message = '';
$error = '';

// Handle POST actions: approve, reject, verify_license
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $account_role_id = intval($_POST['account_role_id'] ?? 0);
    $license_id = intval($_POST['license_id'] ?? 0);

    try {
        if ($action === 'approve' && $account_role_id) {
            $sql = "UPDATE Account_Role SET is_approved = 1 WHERE account_role_ID = :account_role_id AND role_ID = 2";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':account_role_id', $account_role_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $message = 'Guide account approved.';
            } else {
                $error = 'Failed to approve guide account.';
            }
        } elseif ($action === 'reject' && $account_role_id) {
            // Reject: remove the Account_Role entry (keeps user account, removes guide role request)
            $sql = "DELETE FROM Account_Role WHERE account_role_ID = :account_role_id AND role_ID = 2";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':account_role_id', $account_role_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $message = 'Guide request rejected and removed.';
            } else {
                $error = 'Failed to reject guide request.';
            }
        } elseif ($action === 'verify_license' && $license_id) {
            // Mark license verified and activate
            // verifier person is current admin's person_ID -- try to resolve from session login_ID
            $verifier_person_ID = $_SESSION['person_ID'] ?? null;
            if (!$verifier_person_ID) {
                // attempt to lookup
                $sql = "SELECT p.person_ID FROM User_Login ul JOIN Person p ON ul.person_ID = p.person_ID WHERE ul.login_ID = :login_ID";
                $q = $connection->prepare($sql);
                $q->bindParam(':login_ID', $_SESSION['login_ID'], PDO::PARAM_INT);
                $q->execute();
                $row = $q->fetch(PDO::FETCH_ASSOC);
                $verifier_person_ID = $row['person_ID'] ?? null;
            }

            if (!$verifier_person_ID) {
                $error = 'Unable to determine verifier identity.';
            } else {
                $res = $guideObj->verifyLicense($license_id, $verifier_person_ID, 'verified', 'active');
                if ($res) {
                    $message = 'License verified and activated.';
                } else {
                    $error = 'Failed to verify license: ' . $guideObj->getLastError();
                }
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch guide accounts (both pending and all) with license status
$sql = "SELECT ar.account_role_ID, ar.is_approved, ar.created_at as requested_at,
               ul.login_ID, ul.username, p.person_ID, ni.name_first, ni.name_last, ci.contactinfo_email,
               gl.license_ID, gl.license_number, gl.verification_status, gl.status as license_status
        FROM Account_Role ar
        JOIN User_Login ul ON ar.login_ID = ul.login_ID
        JOIN Person p ON ul.person_ID = p.person_ID
        LEFT JOIN Name_Info ni ON p.name_ID = ni.name_ID
        LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
        LEFT JOIN Guide_License gl ON gl.guide_ID = p.person_ID AND gl.created_at = (
            SELECT MAX(created_at) FROM Guide_License gl2 WHERE gl2.guide_ID = p.person_ID
        )
        WHERE ar.role_ID = 2
        ORDER BY ar.is_approved ASC, ar.created_at DESC";

$stmt = $connection->prepare($sql);
$stmt->execute();
$guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Guides - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 25px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #2c3e50; color: white; }
        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-approve { background: #27ae60; color: white; }
        .btn-reject { background: #e74c3c; color: white; }
        .btn-verify { background: #1976d2; color: white; }
        .status-pending { color: #d35400; font-weight: bold; }
        .status-active { color: #27ae60; font-weight: bold; }
        .status-verified { color: #117a8b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Guide Accounts</h1>
        <?php if ($message): ?><div style="background:#e8f5e9;padding:10px;border-left:4px solid #4caf50;"><?=htmlspecialchars($message)?></div><?php endif; ?>
        <?php if ($error): ?><div style="background:#fde8e8;padding:10px;border-left:4px solid #e74c3c;"><?=htmlspecialchars($error)?></div><?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Requested</th>
                    <th>Account Approved</th>
                    <th>License</th>
                    <th>License Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guides as $g): ?>
                    <tr>
                        <td><?=htmlspecialchars($g['name_first'] . ' ' . $g['name_last'])?></td>
                        <td><?=htmlspecialchars($g['username'])?></td>
                        <td><?=htmlspecialchars($g['contactinfo_email'])?></td>
                        <td><?=htmlspecialchars(date('M d, Y H:i', strtotime($g['requested_at'])))?></td>
                        <td>
                            <?php if ($g['is_approved']): ?>
                                <span class="status-active">Approved</span>
                            <?php else: ?>
                                <span class="status-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?=htmlspecialchars($g['license_number'] ?? '—')?></td>
                        <td>
                            <?php if (empty($g['license_ID'])): ?>
                                —
                            <?php else: ?>
                                <?=htmlspecialchars($g['verification_status'] . ' / ' . ($g['license_status'] ?? ''))?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <?php if (!$g['is_approved']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="account_role_id" value="<?=intval($g['account_role_ID'])?>">
                                        <button class="btn btn-approve" type="submit">Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="account_role_id" value="<?=intval($g['account_role_ID'])?>">
                                        <button class="btn btn-reject" type="submit" onclick="return confirm('Reject this guide request?');">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#888;">—</span>
                                <?php endif; ?>

                                <?php if (!empty($g['license_ID']) && $g['verification_status'] !== 'verified'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="verify_license">
                                        <input type="hidden" name="license_id" value="<?=intval($g['license_ID'])?>">
                                        <button class="btn btn-verify" type="submit">Verify License</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin-top:16px;"><a href="../../admin/dashboard.php">← Back to Admin Dashboard</a></p>
    </div>
</body>
</html>