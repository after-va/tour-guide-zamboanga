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
    $_SESSION['error'] = "Invalid spot ID.";
    header('Location: manage-spots.php');
    exit;
}

$spotId = $_GET['id'];

// Delete the spot
$result = $tourManager->deleteTourSpot($spotId);

if ($result) {
    $_SESSION['success'] = "Tour spot deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete tour spot. It may be associated with active packages.";
}

header('Location: manage-spots.php');
exit;
?>