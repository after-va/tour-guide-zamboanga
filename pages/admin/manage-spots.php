<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$user = $_SESSION['user'];

$spots = $tourManager->getAllTourSpots();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tourist Spots - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Manage Tourist Spots</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="manage-packages.php" style="color: white; margin-right: 15px;">Packages</a>
                <a href="manage-spots.php" style="color: white; margin-right: 15px;">Spots</a>
                <a href="manage-bookings.php" style="color: white; margin-right: 15px;">Bookings</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <div style="margin-bottom: 20px;">
            <a href="add-spot.php" 
               style="display: inline-block; padding: 12px 24px; background: #4caf50; color: white; text-decoration: none;">
                ➕ Add New Spot
            </a>
        </div>

        <?php if (empty($spots)): ?>
            <div style="text-align: center; padding: 60px; background: #f5f5f5;">
                <h2>No Tourist Spots</h2>
                <p>Start by adding your first tourist spot.</p>
                <a href="add-spot.php" 
                   style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: #4caf50; color: white; text-decoration: none;">
                    Add Spot
                </a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                <?php foreach ($spots as $spot): ?>
                    <div style="border: 1px solid #ddd; padding: 15px; background: white;">
                        <h3><?= htmlspecialchars($spot['spots_Name']) ?></h3>
                        
                        <?php if ($spot['spots_category']): ?>
                            <span style="display: inline-block; padding: 4px 8px; background: #e3f2fd; color: #1976d2; font-size: 12px; margin-bottom: 10px;">
                                <?= htmlspecialchars($spot['spots_category']) ?>
                            </span>
                        <?php endif; ?>
                        
                        <p><?= htmlspecialchars(substr($spot['spots_Description'], 0, 150)) ?>...</p>
                        
                        <p><strong>Location:</strong> <?= htmlspecialchars($spot['spots_Address']) ?></p>

                        <div style="margin-top: 15px; display: flex; gap: 10px;">
                            <a href="../public/spot-details.php?id=<?= $spot['spots_ID'] ?>" 
                               style="padding: 8px 15px; background: #1976d2; color: white; text-decoration: none; flex: 1; text-align: center;">
                                View
                            </a>
                            <a href="edit-spot.php?id=<?= $spot['spots_ID'] ?>" 
                               style="padding: 8px 15px; background: #ff9800; color: white; text-decoration: none; flex: 1; text-align: center;">
                                Edit
                            </a>
                            <a href="delete-spot.php?id=<?= $spot['spots_ID'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this spot?')"
                               style="padding: 8px 15px; background: #f44336; color: white; text-decoration: none; flex: 1; text-align: center;">
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
