<?php
session_start();
require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$spot_id = $_GET['id'] ?? 0;

if (!$spot_id) {
    header('Location: browse-spots.php');
    exit;
}

$spot = $tourManager->getSpotWithDetails($spot_id);

if (!$spot) {
    header('Location: browse-spots.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($spot['spots_Name']) ?> - Tourismo Zamboanga</title>
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

        <a href="browse-spots.php" style="display: inline-block; margin-bottom: 20px; color: #1976d2;">
            &larr; Back to Spots
        </a>

        <h1><?= htmlspecialchars($spot['spots_Name']) ?></h1>

        <?php if ($spot['spots_category']): ?>
            <span style="display: inline-block; padding: 6px 12px; background: #e3f2fd; color: #1976d2; margin-bottom: 15px;">
                <?= htmlspecialchars($spot['spots_category']) ?>
            </span>
        <?php endif; ?>

        <div style="background: #f5f5f5; padding: 20px; margin-bottom: 20px;">
            <h3>About This Spot</h3>
            <p><?= htmlspecialchars($spot['spots_Description']) ?></p>
            
            <p><strong>Location:</strong> <?= htmlspecialchars($spot['spots_Address']) ?></p>

            <?php if ($spot['spots_GoogleLink']): ?>
                <p>
                    <a href="<?= htmlspecialchars($spot['spots_GoogleLink']) ?>" target="_blank" 
                       style="display: inline-block; padding: 10px 20px; background: #4caf50; color: white; text-decoration: none;">
                        View on Google Maps
                    </a>
                </p>
            <?php endif; ?>

            <?php if (isset($spot['rating']['average_rating']) && $spot['rating']['average_rating']): ?>
                <div style="margin-top: 15px; padding: 15px; background: white; border-left: 4px solid #ffc107;">
                    <strong>Rating:</strong> 
                    <?= str_repeat('★', round($spot['rating']['average_rating'])) ?>
                    <?= str_repeat('☆', 5 - round($spot['rating']['average_rating'])) ?>
                    <?= number_format($spot['rating']['average_rating'], 1) ?>/5.0 
                    (<?= $spot['rating']['total_ratings'] ?> reviews)
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($spot['packages'])): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h3>Tour Packages Including This Spot</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <?php foreach ($spot['packages'] as $package): ?>
                    <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                        <h4><?= htmlspecialchars($package['tourPackage_Name']) ?></h4>
                        <p><?= htmlspecialchars($package['tourPackage_Description']) ?></p>
                        <p>
                            <strong>Duration:</strong> <?= htmlspecialchars($package['tourPackage_Duration']) ?><br>
                            <strong>Capacity:</strong> <?= htmlspecialchars($package['tourPackage_Capacity']) ?> persons
                        </p>
                        <a href="package-details.php?id=<?= $package['tourPackage_ID'] ?>" 
                           style="display: inline-block; padding: 8px 15px; background: #1976d2; color: white; text-decoration: none;">
                            View Package
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($spot['reviews'])): ?>
        <div style="background: white; border: 1px solid #ddd; padding: 20px;">
            <h3>Reviews</h3>
            <?php foreach ($spot['reviews'] as $review): ?>
                <div style="padding: 15px; margin-bottom: 15px; background: #f9f9f9; border-left: 4px solid #ffc107;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <strong><?= htmlspecialchars($review['rater_name'] ?? 'Anonymous') ?></strong>
                        <span style="color: #f57c00;">
                            <?= str_repeat('★', (int)$review['rating_value']) ?>
                            <?= str_repeat('☆', 5 - (int)$review['rating_value']) ?>
                            <?= number_format($review['rating_value'], 1) ?>
                        </span>
                    </div>
                    <?php if ($review['rating_description']): ?>
                        <p style="margin: 0;"><?= htmlspecialchars($review['rating_description']) ?></p>
                    <?php endif; ?>
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
