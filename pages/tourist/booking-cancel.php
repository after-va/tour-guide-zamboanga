<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}
require_once "../../classes/booking.php";

if (isset($_GET['id']) && isset($_SESSION['user'])) {
    $booking_ID = $_GET['id'];
    $account_ID = $_SESSION['user']['account_ID']; // tourist account ID
    $bookingObj = new Booking();
    $results = $bookingObj->cancelBookingIfPendingForPayment($booking_ID, $account_ID);
    if ($results) {
        $_SESSION['success'] = "Booking successfully cancelled and logged.";
    } else {
        $_SESSION['error'] = "Failed to cancel booking.";
    }

    header("Location: booking.php");
    exit;
}
?>
