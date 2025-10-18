<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/Schedule.php";

$schedule = new Schedule();
$schedules = $schedule->getAllSchedules();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Schedules - Admin</title>
</head>
<body>
    <h1>Manage Schedules</h1>
    
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
    
    <h2>All Schedules</h2>
    <p><a href="add-schedule.php">Add New Schedule</a></p>
    
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Package</th>
            <th>Spot</th>
            <th>Guide</th>
            <th>Start Date/Time</th>
            <th>End Date/Time</th>
            <th>Capacity</th>
            <th>Bookings</th>
            <th>Total PAX</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($schedules as $s): ?>
        <tr>
            <td><?php echo $s['schedule_ID']; ?></td>
            <td><?php echo $s['tourPackage_Name']; ?></td>
            <td><?php echo $s['spots_Name']; ?></td>
            <td><?php echo $s['guide_name'] ?? 'Not Assigned'; ?></td>
            <td><?php echo $s['schedule_StartDateTime']; ?></td>
            <td><?php echo $s['schedule_EndDateTime']; ?></td>
            <td><?php echo $s['schedule_Capacity']; ?></td>
            <td><?php echo $s['total_bookings']; ?></td>
            <td><?php echo $s['total_pax'] ?? 0; ?></td>
            <td>
                <a href="edit-schedule.php?id=<?php echo $s['schedule_ID']; ?>">Edit</a> |
                <a href="delete-schedule.php?id=<?php echo $s['schedule_ID']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
