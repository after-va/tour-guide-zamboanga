<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";
require_once "../../classes/booking-manager.php";
require_once "../../classes/guide-manager.php";

$tourManager = new TourManager();
$bookingManager = new BookingManager();
$guideManager = new GuideManager();

$user = $_SESSION['user'];

// Get statistics
$db = new Database();
$conn = $db->connect();

$stats = [];

// Total packages
$query = $conn->query("SELECT COUNT(*) as count FROM Tour_Package");
$stats['total_packages'] = $query->fetch(PDO::FETCH_ASSOC)['count'];

// Total spots
$query = $conn->query("SELECT COUNT(*) as count FROM Tour_Spots");
$stats['total_spots'] = $query->fetch(PDO::FETCH_ASSOC)['count'];

// Total bookings
$query = $conn->query("SELECT COUNT(*) as count FROM Booking");
$stats['total_bookings'] = $query->fetch(PDO::FETCH_ASSOC)['count'];

// Total users
$query = $conn->query("SELECT COUNT(*) as count FROM User_Login");
$stats['total_users'] = $query->fetch(PDO::FETCH_ASSOC)['count'];

// Recent bookings
$query = $conn->query("SELECT b.*, tp.tourPackage_Name, 
                       CONCAT(n.name_first, ' ', n.name_last) as customer_name
                       FROM Booking b
                       LEFT JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                       LEFT JOIN Person p ON b.customer_ID = p.person_ID
                       LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                       ORDER BY b.booking_ID DESC
                       LIMIT 10");
$recentBookings = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Tourismo Zamboanga - Admin Dashboard</h1>
            <p>Welcome, <?= htmlspecialchars($user['full_name']) ?>!</p>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="manage-packages.php" style="color: white; margin-right: 15px;">Packages</a>
                <a href="manage-spots.php" style="color: white; margin-right: 15px;">Spots</a>
                <a href="manage-bookings.php" style="color: white; margin-right: 15px;">Bookings</a>
                <a href="manage-users.php" style="color: white; margin-right: 15px;">Users</a>
                <a href="../../admin/approve-guides.php" style="color: white; margin-right: 15px;">Approve Guides</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: #e3f2fd; padding: 20px; border-left: 4px solid #1976d2;">
                <h3 style="margin: 0 0 10px 0;">Total Packages</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;"><?= $stats['total_packages'] ?></p>
                <a href="manage-packages.php" style="color: #1976d2; text-decoration: none;">Manage &rarr;</a>
            </div>
            
            <div style="background: #e8f5e9; padding: 20px; border-left: 4px solid #4caf50;">
                <h3 style="margin: 0 0 10px 0;">Tourist Spots</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;"><?= $stats['total_spots'] ?></p>
                <a href="manage-spots.php" style="color: #4caf50; text-decoration: none;">Manage &rarr;</a>
            </div>
            
            <div style="background: #fff3e0; padding: 20px; border-left: 4px solid #ff9800;">
                <h3 style="margin: 0 0 10px 0;">Total Bookings</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;"><?= $stats['total_bookings'] ?></p>
                <a href="manage-bookings.php" style="color: #ff9800; text-decoration: none;">Manage &rarr;</a>
            </div>
            
            <div style="background: #f3e5f5; padding: 20px; border-left: 4px solid #9c27b0;">
                <h3 style="margin: 0 0 10px 0;">Total Users</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;"><?= $stats['total_users'] ?></p>
                <a href="manage-users.php" style="color: #9c27b0; text-decoration: none;">Manage &rarr;</a>
            </div>
        </div>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 30px;">
            <h2>Recent Bookings</h2>
            <?php if (empty($recentBookings)): ?>
                <p style="text-align: center; padding: 40px; background: #f5f5f5;">
                    No bookings yet.
                </p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">ID</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Customer</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Package</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">PAX</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Status</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">#<?= $booking['booking_ID'] ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($booking['customer_name']) ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($booking['tourPackage_Name']) ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= $booking['booking_PAX'] ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <span style="padding: 4px 8px; background: 
                                        <?= $booking['booking_Status'] === 'confirmed' ? '#4caf50' : 
                                            ($booking['booking_Status'] === 'pending' ? '#ff9800' : '#757575') ?>; 
                                        color: white; font-size: 12px;">
                                        <?= htmlspecialchars(ucfirst($booking['booking_Status'])) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <a href="booking-details.php?id=<?= $booking['booking_ID'] ?>" 
                                       style="color: #1976d2; text-decoration: none;">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="background: white; border: 1px solid #ddd; padding: 20px;">
                <h3>Quick Actions</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="add-package.php" style="color: #1976d2; text-decoration: none;">
                            ➕ Add New Tour Package
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="add-spot.php" style="color: #1976d2; text-decoration: none;">
                            ➕ Add New Tourist Spot
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="manage-bookings.php" style="color: #1976d2; text-decoration: none;">
                            📋 View All Bookings
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="manage-users.php" style="color: #1976d2; text-decoration: none;">
                            👥 Manage Users
                        </a>
                    </li>
                </ul>
            </div>

            <div style="background: white; border: 1px solid #ddd; padding: 20px;">
                <h3>System Information</h3>
                <p><strong>System:</strong> Tourismo Zamboanga Tour Guide System</p>
                <p><strong>Version:</strong> 1.0.0</p>
                <p><strong>Database:</strong> MySQL</p>
                <p><strong>Server Time:</strong> <?= date('F d, Y h:i A') ?></p>
            </div>
        </div>
    </div>
</body>
</html>
