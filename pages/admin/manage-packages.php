<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$user = $_SESSION['user'];

$packages = $tourManager->getAllTourPackages();

// Handle recommendation actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $packageId = (int)$_GET['id'];
    
    if ($_GET['action'] === 'recommend') {
        if ($tourManager->recommendPackage($packageId)) {
            $_SESSION['success_message'] = "Package has been recommended to guides.";
        } else {
            $_SESSION['error_message'] = "Failed to recommend package.";
        }
    } elseif ($_GET['action'] === 'unrecommend') {
        if ($tourManager->unrecommendPackage($packageId)) {
            $_SESSION['success_message'] = "Package has been removed from recommendations.";
        } else {
            $_SESSION['error_message'] = "Failed to remove package from recommendations.";
        }
    }
    
    header('Location: manage-packages.php');
    exit;
}

// Check recommendation status for each package
foreach ($packages as &$package) {
    $package['is_recommended'] = $tourManager->isPackageRecommended($package['tourPackage_ID']);
}
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
            <h1>Manage Tour Packages</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="manage-packages.php" style="color: white; margin-right: 15px;">Packages</a>
                <a href="manage-spots.php" style="color: white; margin-right: 15px;">Spots</a>
                <a href="manage-bookings.php" style="color: white; margin-right: 15px;">Bookings</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <div style="margin-bottom: 20px;">
            <a href="add-package.php" 
               style="display: inline-block; padding: 12px 24px; background: #4caf50; color: white; text-decoration: none;">
                ➕ Add New Package
            </a>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin-bottom: 20px; border: 1px solid #4caf50;">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (empty($packages)): ?>
            <div style="text-align: center; padding: 60px; background: #f5f5f5;">
                <h2>No Tour Packages</h2>
                <p>Start by adding your first tour package.</p>
                <a href="add-package.php" 
                   style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: #4caf50; color: white; text-decoration: none;">
                    Add Package
                </a>
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
                                    <a href="edit-package.php?id=<?= $package['tourPackage_ID'] ?>" 
                                       style="color: #ff9800; text-decoration: none; margin-right: 10px;">
                                        Edit
                                    </a>
                                    <a href="delete-package.php?id=<?= $package['tourPackage_ID'] ?>" 
                                       onclick="return confirm('Are you sure you want to delete this package?')"
                                       style="color: #f44336; text-decoration: none; margin-right: 10px;">
                                        Delete
                                    </a>
                                    <?php if ($package['is_recommended']): ?>
                                        <a href="manage-packages.php?action=unrecommend&id=<?= $package['tourPackage_ID'] ?>" 
                                           style="color: #f44336; text-decoration: none;">
                                            Unrecommend
                                        </a>
                                    <?php else: ?>
                                        <a href="manage-packages.php?action=recommend&id=<?= $package['tourPackage_ID'] ?>" 
                                           style="color: #4caf50; text-decoration: none;">
                                            Recommend
                                        </a>
                                    <?php endif; ?>
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
