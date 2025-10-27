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

// Get guide ID from the session
$guideId = $user['user_id'];

// Get packages assigned to this guide
$packages = $guideManager->getGuidePackages($guideId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>My Tour Packages</h1>
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

        <?php if (empty($packages)): ?>
            <div style="text-align: center; padding: 60px; background: #f5f5f5;">
                <h2>No Tour Packages Assigned</h2>
                <p>You don't have any tour packages assigned to you yet.</p>
            </div>
        <?php else: ?>
            <div style="background: white; border: 1px solid #ddd;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">No.</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Package Name</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Description</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Duration</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Capacity</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($packages as $package): ?>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #ddd;"><?= $no++ ?></td>
                                <td style="padding: 12px; border: 1px solid #ddd;">
                                    <strong><?= htmlspecialchars($package['tourPackage_Name']) ?></strong>
                                </td>
                                <td style="padding: 12px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars(substr($package['tourPackage_Description'], 0, 100)) ?>...
                                </td>
                                <td style="padding: 12px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($package['tourPackage_Duration']) ?>
                                </td>
                                <td style="padding: 12px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($package['tourPackage_Capacity']) ?>
                                </td>
                                <td style="padding: 12px; border: 1px solid #ddd;">
                                    <a href="../public/package-details.php?id=<?= $package['tourPackage_ID'] ?>" 
                                       style="color: #1976d2; text-decoration: none; margin-right: 10px;">
                                        View
                                    </a>
                                    <a href="package-schedule.php?id=<?= $package['tourPackage_ID'] ?>" 
                                       style="color: #ff9800; text-decoration: none;">
                                        Manage Schedule
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>