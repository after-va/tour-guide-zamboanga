<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Guide') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/guide-manager.php";
require_once "../../classes/tour-manager.php";

$guideManager = new GuideManager();
$tourManager = new TourManager();

$user = $_SESSION['user'];
$guideProfile = $guideManager->getGuideProfile($user['person_ID']);
$guideStats = $guideManager->getGuideStats($user['person_ID']);
$schedules = $tourManager->getSchedulesByGuide($user['person_ID']);
$offerings = $guideManager->getGuidePackageOfferings($user['person_ID']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Tourismo Zamboanga - Guide Dashboard</h1>
            <p>Welcome, <?= htmlspecialchars($user['full_name']) ?>!</p>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="my-schedules.php" style="color: white; margin-right: 15px;">My Schedules</a>
                <a href="my-offerings.php" style="color: white; margin-right: 15px;">My Offerings</a>
                <a href="availability.php" style="color: white; margin-right: 15px;">Availability</a>
                <a href="../../account/switch-role.php" style="color: white; margin-right: 15px;">Switch Role</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: #e3f2fd; padding: 20px; border-left: 4px solid #1976d2;">
                <h3 style="margin: 0 0 10px 0;">Total Schedules</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= $guideStats['total_schedules'] ?? 0 ?>
                </p>
            </div>
            
            <div style="background: #e8f5e9; padding: 20px; border-left: 4px solid #4caf50;">
                <h3 style="margin: 0 0 10px 0;">Total Bookings</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= $guideStats['total_bookings'] ?? 0 ?>
                </p>
            </div>
            
            <div style="background: #f3e5f5; padding: 20px; border-left: 4px solid #9c27b0;">
                <h3 style="margin: 0 0 10px 0;">Completed Tours</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= $guideStats['completed_bookings'] ?? 0 ?>
                </p>
            </div>
            
            <div style="background: #fff3e0; padding: 20px; border-left: 4px solid #ff9800;">
                <h3 style="margin: 0 0 10px 0;">Average Rating</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= isset($guideStats['avg_rating']) && $guideStats['avg_rating'] ? number_format($guideStats['avg_rating'], 1) : 'N/A' ?>
                    <?php if (isset($guideStats['avg_rating']) && $guideStats['avg_rating']): ?>
                        <span style="font-size: 16px;">/5.0</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 30px;">
            <h2>Upcoming Schedules</h2>
            <?php 
            $upcomingSchedules = array_filter($schedules, function($s) {
                return strtotime($s['schedule_StartDateTime']) > time();
            });
            ?>
            
            <?php if (empty($upcomingSchedules)): ?>
                <p style="text-align: center; padding: 40px; background: #f5f5f5;">
                    No upcoming schedules. <a href="my-schedules.php" style="color: #1976d2;">Create a new schedule</a>
                </p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Package</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Date & Time</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Bookings</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Capacity</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($upcomingSchedules, 0, 5) as $schedule): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($schedule['tourPackage_Name']) ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= date('M d, Y h:i A', strtotime($schedule['schedule_StartDateTime'])) ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= $schedule['booked_count'] ?? 0 ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= $schedule['schedule_Capacity'] ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <a href="schedule-details.php?id=<?= $schedule['schedule_ID'] ?>" 
                                       style="color: #1976d2; text-decoration: none;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div style="background: white; border: 1px solid #ddd; padding: 20px;">
            <h2>My Package Offerings</h2>
            <?php if (empty($offerings)): ?>
                <p style="text-align: center; padding: 40px; background: #f5f5f5;">
                    No package offerings yet. <a href="my-offerings.php" style="color: #1976d2;">Create an offering</a>
                </p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($offerings as $offering): ?>
                        <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                            <h3><?= htmlspecialchars($offering['tourPackage_Name']) ?></h3>
                            <p><?= htmlspecialchars($offering['tourPackage_Description']) ?></p>
                            
                            <div style="margin: 10px 0; padding: 10px; background: white;">
                                <strong>Price:</strong> PHP <?= number_format($offering['offering_price'], 2) ?><br>
                                <?php if ($offering['price_per_person']): ?>
                                    <strong>Per Person:</strong> PHP <?= number_format($offering['price_per_person'], 2) ?><br>
                                <?php endif; ?>
                                <strong>PAX:</strong> <?= $offering['min_pax'] ?> - <?= $offering['max_pax'] ?? 'Unlimited' ?><br>
                                <strong>Duration:</strong> <?= htmlspecialchars($offering['tourPackage_Duration']) ?>
                            </div>

                            <a href="edit-offering.php?id=<?= $offering['offering_ID'] ?>" 
                               style="display: inline-block; margin-top: 10px; padding: 8px 15px; background: #1976d2; color: white; text-decoration: none;">
                                Edit Offering
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
