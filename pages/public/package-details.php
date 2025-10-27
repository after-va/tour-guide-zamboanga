<?php
session_start();
require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$package_id = $_GET['id'] ?? 0;

if (!$package_id) {
    header('Location: browse-packages.php');
    exit;
}

$package = $tourManager->getPackageWithDetails($package_id);

if (!$package) {
    header('Location: browse-packages.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($package['tourPackage_Name']) ?> - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Tourismo Zamboanga</h1>
            <nav>
                <a href="../../index.php" style="color: white; margin-right: 15px;">Home</a>
                <a href="browse-packages.php" style="color: white; margin-right: 15px;">Tour Packages</a>
                <a href="browse-spots.php" style="color: white; margin-right: 15px;">Tourist Spots</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="../../logout.php" style="color: white;">Logout</a>
                <?php else: ?>
                    <a href="../../index.php" style="color: white;">Login</a>
                <?php endif; ?>
            </nav>
        </header>

        <a href="browse-packages.php" style="display: inline-block; margin-bottom: 20px; color: #1976d2;">
            &larr; Back to Packages
        </a>

        <h1><?= htmlspecialchars($package['tourPackage_Name']) ?></h1>

        <div style="background: #f5f5f5; padding: 20px; margin-bottom: 20px;">
            <h3>Package Information</h3>
            <p><?= htmlspecialchars($package['tourPackage_Description']) ?></p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                <div>
                    <strong>Duration:</strong><br>
                    <?= htmlspecialchars($package['tourPackage_Duration']) ?>
                </div>
                <div>
                    <strong>Capacity:</strong><br>
                    <?= htmlspecialchars($package['tourPackage_Capacity']) ?> persons
                </div>
                <?php if (isset($package['rating']['average_rating']) && $package['rating']['average_rating']): ?>
                <div>
                    <strong>Rating:</strong><br>
                    <?= number_format($package['rating']['average_rating'], 1) ?>/5.0 
                    (<?= $package['rating']['total_ratings'] ?> reviews)
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($package['spots'])): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h3>Included Tourist Spots</h3>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($package['spots'] as $spot): ?>
                    <div style="padding: 15px; background: #f9f9f9; border-left: 4px solid #1976d2;">
                        <h4 style="margin: 0 0 10px 0;"><?= htmlspecialchars($spot['spots_Name']) ?></h4>
                        <p style="margin: 0;"><?= htmlspecialchars($spot['spots_Description']) ?></p>
                        <p style="margin: 5px 0 0 0;"><strong>Location:</strong> <?= htmlspecialchars($spot['spots_Address']) ?></p>
                        <?php if ($spot['spots_GoogleLink']): ?>
                            <a href="<?= htmlspecialchars($spot['spots_GoogleLink']) ?>" target="_blank" style="color: #1976d2;">
                                View on Google Maps
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($package['schedules'])): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h3>Available Schedules</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Date & Time</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Guide</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Meeting Spot</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Available Slots</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($package['schedules'] as $schedule): ?>
                        <?php 
                        $available = $schedule['available_slots'] ?? 0;
                        if ($available > 0):
                        ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?= date('M d, Y h:i A', strtotime($schedule['schedule_StartDateTime'])) ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?= htmlspecialchars($schedule['guide_name'] ?? 'TBA') ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?= htmlspecialchars($schedule['schedule_MeetingSpot']) ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?= $available ?> / <?= $schedule['schedule_Capacity'] ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php if (isset($_SESSION['user'])): ?>
                                    <a href="../tourist/book-tour.php?schedule_id=<?= $schedule['schedule_ID'] ?>" 
                                       style="padding: 6px 12px; background: #4caf50; color: white; text-decoration: none; display: inline-block;">
                                        Book Now
                                    </a>
                                <?php else: ?>
                                    <a href="../../index.php" style="padding: 6px 12px; background: #757575; color: white; text-decoration: none; display: inline-block;">
                                        Login to Book
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if (!empty($package['reviews'])): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px;">
            <h3>Reviews</h3>
            <?php foreach ($package['reviews'] as $review): ?>
                <div style="padding: 15px; margin-bottom: 15px; background: #f9f9f9; border-left: 4px solid #ffc107;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <strong><?= htmlspecialchars($review['rater_name'] ?? 'Anonymous') ?></strong>
                        <span style="color: #f57c00;">
                            <?= str_repeat('★', (int)$review['rating_value']) ?>
                            <?= str_repeat('☆', 5 - (int)$review['rating_value']) ?>
                            <?= number_format($review['rating_value'], 1) ?>
                        </span>
                    </div>
                    <p style="margin: 0;"><?= htmlspecialchars($review['rating_description']) ?></p>
                    <small style="color: #757575;">
                        <?= date('M d, Y', strtotime($review['rating_date'])) ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
