<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/Payment.php";

$payment = new Payment();
$payments = $payment->getAllPayments();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Payments - Admin</title>
</head>
<body>
    <h1>Manage Payments</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="bookings.php">Bookings</a> |
        <a href="users.php">Users</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="tour-spots.php">Tour Spots</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>All Payments</h2>
    
    <table border="1">
        <tr>
            <th>Payment ID</th>
            <th>Booking ID</th>
            <th>Customer</th>
            <th>Package</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Method</th>
            <th>Status</th>
        </tr>
        <?php foreach ($payments as $p): ?>
        <tr>
            <td><?php echo $p['paymentinfo_ID']; ?></td>
            <td><?php echo $p['booking_ID']; ?></td>
            <td><?php echo $p['customer_name']; ?></td>
            <td><?php echo $p['tourPackage_Name']; ?></td>
            <td><?php echo number_format($p['paymentinfo_Amount'], 2); ?></td>
            <td><?php echo $p['paymentinfo_Date']; ?></td>
            <td><?php echo $p['method_name'] ?? 'N/A'; ?></td>
            <td><?php echo $p['transaction_status'] ?? 'N/A'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
