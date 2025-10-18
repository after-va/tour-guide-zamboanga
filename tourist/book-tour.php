<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/Schedule.php";
require_once "../php/Booking.php";

$schedule = new Schedule();
$booking = new Booking();

if (!isset($_GET['schedule_id'])) {
    header("Location: browse-tours.php");
    exit();
}

$schedule_ID = $_GET['schedule_id'];
$scheduleDetails = $schedule->getScheduleById($schedule_ID);

if (!$scheduleDetails) {
    header("Location: browse-tours.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_PAX = $_POST['booking_PAX'];
    
    if ($schedule->hasCapacity($schedule_ID, $booking_PAX)) {
        $result = $booking->createBooking(
            $_SESSION['user_id'],
            $schedule_ID,
            $scheduleDetails['tourPackage_ID'],
            $booking_PAX
        );
        
        if ($result) {
            header("Location: my-bookings.php?success=1");
            exit();
        } else {
            $error = "Booking failed. Please try again.";
        }
    } else {
        $error = "Not enough capacity for your booking.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Tour - Tourist</title>
</head>
<body>
    <h1>Book Tour</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="browse-tours.php">Browse Tours</a> |
        <a href="my-bookings.php">My Bookings</a> |
        <a href="profile.php">Profile</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <h2>Tour Details</h2>
    <p><strong>Package:</strong> <?php echo $scheduleDetails['tourPackage_Name']; ?></p>
    <p><strong>Description:</strong> <?php echo $scheduleDetails['tourPackage_Description']; ?></p>
    <p><strong>Spot:</strong> <?php echo $scheduleDetails['spots_Name']; ?></p>
    <p><strong>Address:</strong> <?php echo $scheduleDetails['spots_Address']; ?></p>
    <p><strong>Guide:</strong> <?php echo $scheduleDetails['guide_name'] ?? 'Not Assigned'; ?></p>
    <p><strong>Start Date/Time:</strong> <?php echo $scheduleDetails['schedule_StartDateTime']; ?></p>
    <p><strong>End Date/Time:</strong> <?php echo $scheduleDetails['schedule_EndDateTime']; ?></p>
    <p><strong>Available Slots:</strong> <?php echo $scheduleDetails['schedule_Capacity'] - ($scheduleDetails['total_pax'] ?? 0); ?></p>
    
    <h3>Book This Tour</h3>
    <form method="POST">
        <label>Number of Persons (PAX):</label><br>
        <input type="number" name="booking_PAX" min="1" max="<?php echo $scheduleDetails['schedule_Capacity'] - ($scheduleDetails['total_pax'] ?? 0); ?>" required><br><br>
        
        <button type="submit">Confirm Booking</button>
        <a href="browse-tours.php">Cancel</a>
    </form>
</body>
</html>
