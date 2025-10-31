<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}
require_once "../../classes/tourist.php";
require_once "../../classes/payment.php"
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
  <form action="method-save.php" method="POST" enctype="multipart/form-data">
    <h2>Add Payment Method</h2>

      <label>Method Name:</label>
      <input type="text" name="method_name" placeholder="e.g., GCash" required>

      <label>Type:</label>
      <select name="method_type">
        <option value="E-Wallet">E-Wallet</option>
        <option value="Bank Transfer">Bank Transfer</option>
        <option value="Online Payment">Online Payment</option>
        <option value="Card">Card</option>
        <option value="Offline">Offline</option>
      </select>

      <label>Account Name:</label>
      <input type="text" name="method_account_name" placeholder="Juan Dela Cruz">

      <label>Account Number / Email:</label>
      <input type="text" name="method_account_number" placeholder="09171234567 or paypal@email.com">

      <label>Upload QR Code (Optional):</label>
      <input type="file" name="method_qr_code">

      <label>Processing Fee (% or Fixed):</label>
      <input type="number" step="0.01" name="method_processing_fee" value="0.00">

      <label>Status:</label>
      <select name="method_status">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>

      <label>Description:</label>
      <textarea name="method_description" rows="3" placeholder="Additional details or instructions"></textarea>

    <button type="submit">Save Method</button>
  </form>


    
    
</body>
</html>