<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Guide') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/guide-manager.php";

$guideManager = new GuideManager();
$user = $_SESSION['user'];
$guideId = $user['user_id'];

// Check if schedule ID and package ID are provided
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['package']) || empty($_GET['package'])) {
    $_SESSION['error'] = "Invalid schedule ID or package ID.";
    header('Location: manage-packages.php');
    exit;
}

$scheduleId = $_GET['id'];
$packageId = $_GET['package'];

// Delete the schedule
$result = $guideManager->deleteSchedule($scheduleId, $guideId);

if ($result) {
    $_SESSION['success'] = "Schedule deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete schedule. It may have bookings or you don't have permission.";
}

header('Location: package-schedule.php?id=' . $packageId);
exit;
?>