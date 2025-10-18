<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/Schedule.php";

$schedule = new Schedule();
$availableSchedules = $schedule->getAvailableSchedules();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Tours - Tourist</title>
</head>
<body>
    <h1>Browse Available Tours</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="browse-tours.php">Browse Tours</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>Available Tour Schedules</h2>
    
    <?php if (count($availableSchedules) > 0): ?>
        <table border="1">
            <tr>
                <th>Package</th>
                <th>Spot</th>
                <th>Guide</th>
                <th>Start Date/Time</th>
                <th>Duration</th>
                <th>Available Slots</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($availableSchedules as $s): ?>
            <tr>
                <td><?php echo $s['tourPackage_Name']; ?></td>
                <td><?php echo $s['spots_Name']; ?></td>
                <td><?php echo $s['guide_name'] ?? 'Not Assigned'; ?></td>
                <td><?php echo $s['schedule_StartDateTime']; ?></td>
                <td><?php echo date('H:i', strtotime($s['schedule_EndDateTime'])) - date('H:i', strtotime($s['schedule_StartDateTime'])); ?> hours</td>
                <td><?php echo $s['available_slots']; ?></td>
                <td><a href="book-tour.php?schedule_id=<?php echo $s['schedule_ID']; ?>">Book Now</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No available tours at the moment. Please check back later.</p>
    <?php endif; ?>
</body>
</html>
