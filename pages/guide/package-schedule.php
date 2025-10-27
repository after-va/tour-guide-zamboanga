<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Guide') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";
require_once "../../classes/guide-manager.php";

$tourManager = new TourManager();
$guideManager = new GuideManager();
$user = $_SESSION['user'];
$guideId = $user['user_id'];

// Check if package ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid package ID.";
    header('Location: manage-packages.php');
    exit;
}

$packageId = $_GET['id'];
$package = $tourManager->getTourPackageById($packageId);

if (!$package) {
    $_SESSION['error'] = "Package not found.";
    header('Location: manage-packages.php');
    exit;
}

// Handle form submission for adding/updating schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleDate = $_POST['schedule_date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $maxTourists = $_POST['max_tourists'];
    $notes = $_POST['notes'];
    
    $result = $guideManager->createOrUpdateSchedule(
        $packageId, 
        $guideId, 
        $scheduleDate, 
        $startTime, 
        $endTime, 
        $maxTourists, 
        $notes
    );
    
    if ($result) {
        $_SESSION['success'] = "Schedule updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update schedule.";
    }
    
    header('Location: package-schedule.php?id=' . $packageId);
    exit;
}

// Get existing schedules for this package and guide
$schedules = $guideManager->getSchedulesByPackageAndGuide($packageId, $guideId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Package Schedule - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Manage Schedule: <?= htmlspecialchars($package['tourPackage_Name']) ?></h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="manage-packages.php" style="color: white; margin-right: 15px;">My Packages</a>
                <a href="manage-bookings.php" style="color: white; margin-right: 15px;">My Bookings</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 20px;">
            <!-- Add/Edit Schedule Form -->
            <div style="flex: 1; background: white; padding: 20px; border: 1px solid #ddd;">
                <h2>Add New Schedule</h2>
                <form method="POST" action="">
                    <div style="margin-bottom: 15px;">
                        <label for="schedule_date" style="display: block; margin-bottom: 5px;">Date:</label>
                        <input type="date" id="schedule_date" name="schedule_date" required 
                               style="width: 100%; padding: 8px; border: 1px solid #ddd;" 
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="start_time" style="display: block; margin-bottom: 5px;">Start Time:</label>
                        <input type="time" id="start_time" name="start_time" required 
                               style="width: 100%; padding: 8px; border: 1px solid #ddd;">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="end_time" style="display: block; margin-bottom: 5px;">End Time:</label>
                        <input type="time" id="end_time" name="end_time" required 
                               style="width: 100%; padding: 8px; border: 1px solid #ddd;">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="max_tourists" style="display: block; margin-bottom: 5px;">Max Tourists:</label>
                        <input type="number" id="max_tourists" name="max_tourists" required 
                               style="width: 100%; padding: 8px; border: 1px solid #ddd;"
                               min="1" max="<?= $package['tourPackage_Capacity'] ?>" 
                               value="<?= $package['tourPackage_Capacity'] ?>">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="notes" style="display: block; margin-bottom: 5px;">Notes:</label>
                        <textarea id="notes" name="notes" 
                                  style="width: 100%; padding: 8px; border: 1px solid #ddd; height: 100px;"></textarea>
                    </div>
                    
                    <button type="submit" 
                            style="background: #4caf50; color: white; border: none; padding: 10px 20px; cursor: pointer;">
                        Add Schedule
                    </button>
                </form>
            </div>
            
            <!-- Existing Schedules -->
            <div style="flex: 1; background: white; padding: 20px; border: 1px solid #ddd;">
                <h2>Existing Schedules</h2>
                
                <?php if (empty($schedules)): ?>
                    <p>No schedules found for this package. Add your first schedule using the form.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Date</th>
                                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Time</th>
                                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Capacity</th>
                                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Bookings</th>
                                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?= date('M d, Y', strtotime($schedule['schedule_date'])) ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?= date('h:i A', strtotime($schedule['start_time'])) ?> - 
                                        <?= date('h:i A', strtotime($schedule['end_time'])) ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?= $schedule['max_tourists'] ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?= $schedule['booked_count'] ?? 0 ?> / <?= $schedule['max_tourists'] ?>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <?php if (($schedule['booked_count'] ?? 0) == 0): ?>
                                            <a href="delete-schedule.php?id=<?= $schedule['schedule_ID'] ?>&package=<?= $packageId ?>" 
                                               onclick="return confirm('Are you sure you want to delete this schedule?')"
                                               style="color: #f44336; text-decoration: none;">
                                                Delete
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #999;">Has Bookings</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>