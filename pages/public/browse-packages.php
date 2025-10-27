<?php
session_start();
require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

if ($search || $category) {
    $packages = $tourManager->searchPackages($search, $category);
} else {
    $packages = $tourManager->getAllTourPackages();
}

$categories = $tourManager->getSpotCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Tour Packages - Tourismo Zamboanga</title>
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

        <h2>Browse Tour Packages</h2>

        <div style="background: #f5f5f5; padding: 15px; margin-bottom: 20px;">
            <form method="get" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="Search packages..." 
                       value="<?= htmlspecialchars($search) ?>" 
                       style="flex: 1; min-width: 200px; padding: 8px;">
                
                <select name="category" style="padding: 8px;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" style="padding: 8px 20px; background: #1976d2; color: white; border: none; cursor: pointer;">
                    Search
                </button>
                
                <a href="browse-packages.php" style="padding: 8px 20px; background: #757575; color: white; text-decoration: none; display: inline-block;">
                    Clear
                </a>
            </form>
        </div>

        <?php if (empty($packages)): ?>
            <p style="text-align: center; padding: 40px; background: #f5f5f5;">
                No tour packages found. Try a different search.
            </p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach ($packages as $package): ?>
                    <div style="border: 1px solid #ddd; padding: 15px; background: white;">
                        <h3><?= htmlspecialchars($package['tourPackage_Name']) ?></h3>
                        <p><?= htmlspecialchars($package['tourPackage_Description']) ?></p>
                        
                        <div style="margin: 10px 0; padding: 10px; background: #f5f5f5;">
                            <strong>Duration:</strong> <?= htmlspecialchars($package['tourPackage_Duration']) ?><br>
                            <strong>Capacity:</strong> <?= htmlspecialchars($package['tourPackage_Capacity']) ?> persons
                        </div>

                        <?php if ($package['spots_Name']): ?>
                            <p><strong>Main Spot:</strong> <?= htmlspecialchars($package['spots_Name']) ?></p>
                        <?php endif; ?>

                        <?php if (isset($package['avg_rating']) && $package['avg_rating']): ?>
                            <p>
                                <strong>Rating:</strong> 
                                <?= number_format($package['avg_rating'], 1) ?>/5.0 
                                (<?= $package['total_reviews'] ?? 0 ?> reviews)
                            </p>
                        <?php endif; ?>

                        <a href="package-details.php?id=<?= $package['tourPackage_ID'] ?>" 
                           style="display: inline-block; margin-top: 10px; padding: 8px 15px; background: #1976d2; color: white; text-decoration: none;">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
