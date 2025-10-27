<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/booking-manager.php";

$bookingManager = new BookingManager();
$user = $_SESSION['user'];
$bookings = $bookingManager->getBookingsByCustomer($user['person_ID']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>My Bookings</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="my-bookings.php" style="color: white; margin-right: 15px;">My Bookings</a>
                <a href="../public/browse-packages.php" style="color: white; margin-right: 15px;">Browse Packages</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <?php if (empty($bookings)): ?>
            <div style="text-align: center; padding: 60px; background: #f5f5f5;">
                <h2>No Bookings Yet</h2>
                <p>You haven't made any bookings yet.</p>
                <a href="../public/browse-packages.php" 
                   style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: #1976d2; color: white; text-decoration: none;">
                    Browse Tour Packages
                </a>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; background: white;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Booking ID</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Package</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Schedule</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">PAX</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Status</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                #<?= $booking['booking_ID'] ?>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <strong><?= htmlspecialchars($booking['tourPackage_Name']) ?></strong>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <?php if ($booking['schedule_StartDateTime']): ?>
                                    <?= date('M d, Y h:i A', strtotime($booking['schedule_StartDateTime'])) ?>
                                <?php else: ?>
                                    TBA
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <?= $booking['booking_PAX'] ?>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <span style="padding: 6px 12px; background: 
                                    <?= $booking['booking_Status'] === 'confirmed' ? '#4caf50' : 
                                        ($booking['booking_Status'] === 'pending' ? '#ff9800' : 
                                        ($booking['booking_Status'] === 'completed' ? '#9c27b0' : 
                                        ($booking['booking_Status'] === 'cancelled' ? '#f44336' : '#757575'))) ?>; 
                                    color: white; font-size: 12px; border-radius: 3px;">
                                    <?= htmlspecialchars(ucfirst($booking['booking_Status'])) ?>
                                </span>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <a href="booking-details.php?id=<?= $booking['booking_ID'] ?>" 
                                   style="padding: 6px 12px; background: #1976d2; color: white; text-decoration: none; display: inline-block; margin-right: 5px;">
                                    View
                                </a>
                                <?php if ($booking['booking_Status'] === 'pending' || $booking['booking_Status'] === 'confirmed'): ?>
                                    <a href="cancel-booking.php?id=<?= $booking['booking_ID'] ?>" 
                                       onclick="return confirm('Are you sure you want to cancel this booking?')"
                                       style="padding: 6px 12px; background: #f44336; color: white; text-decoration: none; display: inline-block;">
                                        Cancel
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
