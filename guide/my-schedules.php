<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

require_once "../php/Guide.php";

$guide = new Guide();
$mySchedules = $guide->getGuideSchedules($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Schedules - Guide</title>
</head>
<body>
    <h1>My Schedules</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="my-schedules.php">My Schedules</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>All My Schedules</h2>
    
    <?php if (count($mySchedules) > 0): ?>
        <table border="1">
            <tr>
                <th>Schedule ID</th>
                <th>Package</th>
                <th>Spot</th>
                <th>Start Date/Time</th>
                <th>End Date/Time</th>
                <th>Capacity</th>
                <th>Total Bookings</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($mySchedules as $s): ?>
            <tr>
                <td><?php echo $s['schedule_ID']; ?></td>
                <td><?php echo $s['tourPackage_Name']; ?></td>
                <td><?php echo $s['spots_Name']; ?></td>
                <td><?php echo $s['schedule_StartDateTime']; ?></td>
                <td><?php echo $s['schedule_EndDateTime']; ?></td>
                <td><?php echo $s['schedule_Capacity']; ?></td>
                <td><?php echo $s['total_bookings']; ?></td>
                <td><a href="schedule-details.php?id=<?php echo $s['schedule_ID']; ?>">View Details</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No schedules assigned yet.</p>
    <?php endif; ?>
</body>
</html>
