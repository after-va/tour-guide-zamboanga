<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if user is logged in
if (!isset($_SESSION['login_ID'])) {
    header("Location: ../index.php");
    exit;
}

require_once "../classes/database.php";
require_once "../classes/guide.php";

$db = new Database();
$guideObj = new Guide();
$connection = $db->connect();

$login_ID = $_SESSION['login_ID'];
$message = "";
$error = "";

// Get user's current roles
$sql_roles = "SELECT ar.account_role_ID, ar.role_ID, ar.is_approved, ri.role_name 
              FROM Account_Role ar
              JOIN Role_Info ri ON ar.role_ID = ri.role_ID
              WHERE ar.login_ID = :login_ID
              ORDER BY ar.role_ID";
$query_roles = $connection->prepare($sql_roles);
$query_roles->bindParam(":login_ID", $login_ID, PDO::PARAM_INT);
$query_roles->execute();
$user_roles = $query_roles->fetchAll(PDO::FETCH_ASSOC);

// Handle role switch request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'request_guide') {
        // Tourist requesting to become a guide
        if ($guideObj->requestGuideRole($login_ID, $connection)) {
            $message = "Guide role request submitted! Your request is pending admin approval.";
            // Refresh roles
            $query_roles->execute();
            $user_roles = $query_roles->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = $guideObj->getLastError();
        }
    } elseif ($action === 'switch_role') {
        // Switch to a different role
        $target_role_id = $_POST['target_role_id'] ?? null;
        
        if ($target_role_id) {
            // Check if user has this role and it's approved
            $sql_check = "SELECT account_role_ID, is_approved FROM Account_Role 
                         WHERE login_ID = :login_ID AND role_ID = :role_ID";
            $query_check = $connection->prepare($sql_check);
            $query_check->bindParam(":login_ID", $login_ID, PDO::PARAM_INT);
            $query_check->bindParam(":role_ID", $target_role_id, PDO::PARAM_INT);
            $query_check->execute();
            
            $role_data = $query_check->fetch(PDO::FETCH_ASSOC);
            
            if ($role_data) {
                if ($role_data['is_approved'] == 0 && $target_role_id == 2) {
                    $error = "Your guide role is still pending admin approval.";
                } else {
                    // Update session with new role
                    $_SESSION['role_ID'] = $target_role_id;
                    $_SESSION['account_role_ID'] = $role_data['account_role_ID'];
                    $message = "Role switched successfully!";
                    
                    // Redirect to appropriate dashboard
                    if ($target_role_id == 3) {
                        header("Location: ../dashboard/tourist-dashboard.php");
                        exit;
                    } elseif ($target_role_id == 2) {
                        header("Location: ../dashboard/guide-dashboard.php");
                        exit;
                    }
                }
            } else {
                $error = "You don't have access to this role.";
            }
        }
    }
}

// Get current role from session
$current_role_id = $_SESSION['role_ID'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Switch Account Role</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
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
        .role-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .role-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 10px 0;
            background: white;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .role-info {
            flex: 1;
        }
        .role-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .role-status {
            font-size: 0.9em;
            color: #666;
        }
        .status-approved {
            color: #27ae60;
            font-weight: bold;
        }
        .status-pending {
            color: #f39c12;
            font-weight: bold;
        }
        .status-current {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.85em;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        .btn-request {
            background: #27ae60;
        }
        .btn-request:hover {
            background: #229954;
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
        <h1>Account Roles</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="role-section">
            <h2>Your Roles</h2>
            
            <?php if (empty($user_roles)): ?>
                <p>No roles found.</p>
            <?php else: ?>
                <?php foreach ($user_roles as $role): ?>
                    <div class="role-item">
                        <div class="role-info">
                            <div class="role-name"><?= htmlspecialchars($role['role_name']) ?></div>
                            <div class="role-status">
                                <?php if ($role['role_ID'] == 2): // Guide role ?>
                                    <?php if ($role['is_approved'] == 0): ?>
                                        <span class="status-pending">⏳ Pending Approval</span>
                                    <?php else: ?>
                                        <span class="status-approved">✓ Approved</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="status-approved">✓ Active</span>
                                <?php endif; ?>
                                
                                <?php if ($role['role_ID'] == $current_role_id): ?>
                                    <span class="status-current">Current</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <form method="POST" style="display: inline;">
                            <?php if ($role['role_ID'] != $current_role_id): ?>
                                <?php if ($role['role_ID'] == 2 && $role['is_approved'] == 0): ?>
                                    <button type="button" disabled>Pending Approval</button>
                                <?php else: ?>
                                    <input type="hidden" name="action" value="switch_role">
                                    <input type="hidden" name="target_role_id" value="<?= $role['role_ID'] ?>">
                                    <button type="submit">Switch to <?= htmlspecialchars($role['role_name']) ?></button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="button" disabled>Current Role</button>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="role-section">
            <h2>Request New Role</h2>
            
            <?php 
            // Check if user already has a guide role
            $has_guide_role = false;
            foreach ($user_roles as $role) {
                if ($role['role_ID'] == 2) {
                    $has_guide_role = true;
                    break;
                }
            }
            ?>
            
            <?php if (!$has_guide_role): ?>
                <p>Want to become a tour guide? Submit a request below.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="request_guide">
                    <button type="submit" class="btn-request">Request Guide Role</button>
                </form>
            <?php else: ?>
                <p>You already have a guide role request or account.</p>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="javascript:history.back()">← Back</a>
        </div>
    </div>
</body>
</html>
