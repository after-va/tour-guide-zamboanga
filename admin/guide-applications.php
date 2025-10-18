<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/Database.php";

$success = "";
$error = "";

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $certification_ID = $_POST['certification_ID'];
    $action = $_POST['action'];
    $guide_ID = $_POST['guide_ID'];
    
    $db = new Database();
    $conn = $db->connect();
    
    if ($action == 'approve') {
        $sql = "UPDATE Guide_Certification 
                SET status = 'verified', 
                    verified_by = :admin_id, 
                    verified_at = NOW() 
                WHERE certification_ID = :certification_ID";
        $query = $conn->prepare($sql);
        $query->bindParam(":admin_id", $_SESSION['user_id']);
        $query->bindParam(":certification_ID", $certification_ID);
        
        if ($query->execute()) {
            $success = "Tour guide application approved successfully!";
        } else {
            $error = "Failed to approve application.";
        }
    } elseif ($action == 'reject') {
        $sql = "UPDATE Guide_Certification 
                SET status = 'rejected', 
                    verified_by = :admin_id, 
                    verified_at = NOW() 
                WHERE certification_ID = :certification_ID";
        $query = $conn->prepare($sql);
        $query->bindParam(":admin_id", $_SESSION['user_id']);
        $query->bindParam(":certification_ID", $certification_ID);
        
        if ($query->execute()) {
            $success = "Tour guide application rejected.";
        } else {
            $error = "Failed to reject application.";
        }
    }
}

// Get all guide applications
$db = new Database();
$conn = $db->connect();

$sql = "SELECT gc.*, 
               CONCAT(n.name_first, ' ', n.name_last) as guide_name,
               ci.contactinfo_email,
               ph.phone_number,
               p.person_ID
        FROM Guide_Certification gc
        INNER JOIN Person p ON gc.guide_ID = p.person_ID
        INNER JOIN Name_Info n ON p.name_ID = n.name_ID
        LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
        LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
        ORDER BY 
            CASE gc.status
                WHEN 'pending' THEN 1
                WHEN 'verified' THEN 2
                WHEN 'rejected' THEN 3
                WHEN 'expired' THEN 4
            END,
            gc.created_at DESC";

$query = $conn->prepare($sql);
$query->execute();
$applications = $query->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tour Guide Applications - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9em;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        .badge-verified {
            background-color: #28a745;
            color: white;
        }
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        .badge-expired {
            background-color: #6c757d;
            color: white;
        }
        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        nav {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 2em;
        }
        .stat-box p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .stat-pending {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
        }
        .stat-verified {
            background-color: #d4edda;
            border: 2px solid #28a745;
        }
        .stat-rejected {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
        }
    </style>
</head>
<body>
    <h1>Tour Guide Applications</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="bookings.php">Bookings</a> |
        <a href="users.php">Users</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="tour-spots.php">Tour Spots</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="guide-applications.php">Guide Applications</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php
    $pending_count = 0;
    $verified_count = 0;
    $rejected_count = 0;
    
    foreach ($applications as $app) {
        if ($app['status'] == 'pending') $pending_count++;
        if ($app['status'] == 'verified') $verified_count++;
        if ($app['status'] == 'rejected') $rejected_count++;
    }
    ?>
    
    <div class="stats">
        <div class="stat-box stat-pending">
            <h3><?php echo $pending_count; ?></h3>
            <p>Pending Applications</p>
        </div>
        <div class="stat-box stat-verified">
            <h3><?php echo $verified_count; ?></h3>
            <p>Verified Guides</p>
        </div>
        <div class="stat-box stat-rejected">
            <h3><?php echo $rejected_count; ?></h3>
            <p>Rejected Applications</p>
        </div>
    </div>
    
    <h2>All Applications</h2>
    
    <?php if (empty($applications)): ?>
        <p>No tour guide applications yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Guide Name</th>
                    <th>Certification Type</th>
                    <th>Certification #</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?php echo htmlspecialchars($app['guide_name']); ?></td>
                    <td><?php echo htmlspecialchars($app['certification_type'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($app['certification_number'] ?? 'N/A'); ?></td>
                    <td><?php echo $app['issue_date'] ? date('M d, Y', strtotime($app['issue_date'])) : 'N/A'; ?></td>
                    <td><?php echo $app['expiry_date'] ? date('M d, Y', strtotime($app['expiry_date'])) : 'N/A'; ?></td>
                    <td>
                        <?php echo htmlspecialchars($app['contactinfo_email'] ?? 'N/A'); ?><br>
                        <small><?php echo htmlspecialchars($app['phone_number'] ?? 'N/A'); ?></small>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $app['status']; ?>">
                            <?php echo strtoupper($app['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($app['status'] == 'pending'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="certification_ID" value="<?php echo $app['certification_ID']; ?>">
                                <input type="hidden" name="guide_ID" value="<?php echo $app['person_ID']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this guide application?')">Approve</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="certification_ID" value="<?php echo $app['certification_ID']; ?>">
                                <input type="hidden" name="guide_ID" value="<?php echo $app['person_ID']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this guide application?')">Reject</button>
                            </form>
                        <?php else: ?>
                            <span style="color: #999;">No actions</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
