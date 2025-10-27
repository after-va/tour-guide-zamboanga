<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['login_ID']) || ($_SESSION['role_ID'] ?? null) != 1) {
    header("Location: ../index.php");
    exit;
}

require_once "../classes/database.php";

$db = new Database();
$connection = $db->connect();

$message = "";
$error = "";

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $account_role_id = $_POST['account_role_id'] ?? null;
    
    if ($account_role_id) {
        try {
            if ($action === 'approve') {
                $sql = "UPDATE Account_Role SET is_approved = 1 WHERE account_role_ID = :account_role_id AND role_ID = 2";
                $query = $connection->prepare($sql);
                $query->bindParam(":account_role_id", $account_role_id, PDO::PARAM_INT);
                
                if ($query->execute()) {
                    $message = "Guide request approved successfully!";
                } else {
                    $error = "Failed to approve guide request.";
                }
            } elseif ($action === 'reject') {
                $sql = "DELETE FROM Account_Role WHERE account_role_ID = :account_role_id AND role_ID = 2";
                $query = $connection->prepare($sql);
                $query->bindParam(":account_role_id", $account_role_id, PDO::PARAM_INT);
                
                if ($query->execute()) {
                    $message = "Guide request rejected and removed.";
                } else {
                    $error = "Failed to reject guide request.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Get pending guide requests
$sql = "SELECT ar.account_role_ID, ar.created_at, ul.login_ID, ul.username, 
               p.person_ID, ni.name_first, ni.name_last, ci.contactinfo_email
        FROM Account_Role ar
        JOIN User_Login ul ON ar.login_ID = ul.login_ID
        JOIN Person p ON ul.person_ID = p.person_ID
        JOIN Name_Info ni ON p.name_ID = ni.name_ID
        JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
        WHERE ar.role_ID = 2 AND ar.is_approved = 0
        ORDER BY ar.created_at DESC";

$query = $connection->prepare($sql);
$query->execute();
$pending_guides = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Guide Requests - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
        .error {
            background: #fde8e8;
            color: #c62828;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
        }
        .info-box {
            background: #e3f2fd;
            color: #1565c0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #1976d2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #2c3e50;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        .btn-approve {
            background: #27ae60;
            color: white;
        }
        .btn-approve:hover {
            background: #229954;
        }
        .btn-reject {
            background: #e74c3c;
            color: white;
        }
        .btn-reject:hover {
            background: #c0392b;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Guide Registration Requests</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="info-box">
            <strong>ℹ️ Info:</strong> Review and approve or reject pending guide registration requests below.
        </div>

        <?php if (empty($pending_guides)): ?>
            <div class="empty-state">
                <p>No pending guide requests at this time.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_guides as $guide): ?>
                        <tr>
                            <td><?= htmlspecialchars($guide['name_first'] . ' ' . $guide['name_last']) ?></td>
                            <td><?= htmlspecialchars($guide['username']) ?></td>
                            <td><?= htmlspecialchars($guide['contactinfo_email']) ?></td>
                            <td><?= date('M d, Y H:i', strtotime($guide['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="account_role_id" value="<?= $guide['account_role_ID'] ?>">
                                        <button type="submit" class="btn-approve">Approve</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="account_role_id" value="<?= $guide['account_role_ID'] ?>">
                                        <button type="submit" class="btn-reject" onclick="return confirm('Are you sure you want to reject this request?');">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="back-link">
            <a href="javascript:history.back()">← Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>
