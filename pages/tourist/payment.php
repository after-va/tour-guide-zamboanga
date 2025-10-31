<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}
require_once "../../classes/tourist.php";
$tourist_ID = $_SESSION['user']['account_ID'];
$torustObj = new Tourist();
$booking_ID = intval($_GET['id']);


?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
</head>
<body>
    <h1>Payment</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="booking.php">My Bookings</a> |
        <a href="tour-packages.php">View Tour Packages</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2></h2>

    <form action="process-payment.php" method="POST" class="payment-form">
  <h2>Booking Payment Form</h2>

  <!-- Booking Selection -->
  <label for="booking_ID">Booking ID:</label>
  <select name="booking_ID" id="booking_ID" required>
    <option value="">Select Booking</option>
    <!-- Dynamically load from Booking table -->
  </select>

  <!-- Payment Info -->
  <h3>Payment Details</h3>
  <label for="paymentinfo_total_amount">Total Amount (₱):</label>
  <input type="number" step="0.01" name="paymentinfo_total_amount" id="paymentinfo_total_amount" required>

  <label for="paymentinfo_date">Payment Date:</label>
  <input type="date" name="paymentinfo_date" id="paymentinfo_date" required>

  <!-- Transaction Info -->
  <h3>Transaction Details</h3>
  <label for="method_ID">Payment Method:</label>
  <select name="method_ID" id="method_ID" required>
    <option value="">Select Method</option>
    <option value="1">GCash</option>
    <option value="2">PayPal</option>
    <option value="3">Credit Card</option>
    <!-- Dynamically load from Method table -->
  </select>

  <label for="transaction_reference">Transaction Reference:</label>
  <input type="text" name="transaction_reference" id="transaction_reference" placeholder="e.g. G123456789" required>

  <label for="transaction_status">Transaction Status:</label>
  <select name="transaction_status" id="transaction_status" required>
    <option value="Pending">Pending</option>
    <option value="Completed">Completed</option>
    <option value="Failed">Failed</option>
  </select>

  <button type="submit">Submit Payment</button>
</form>

    
    
</body>
</html>