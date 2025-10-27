<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid package ID.";
    header('Location: manage-packages.php');
    exit;
}

$packageId = $_GET['id'];

// Delete the package
$result = $tourManager->deleteTourPackage($packageId);

if ($result) {
    $_SESSION['success'] = "Tour package deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete tour package. It may be associated with active bookings.";
}

header('Location: manage-packages.php');
exit;
?>