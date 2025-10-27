<?php
session_start();
require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

if ($search) {
    $spots = $tourManager->searchTourSpots($search);
} elseif ($category) {
    $spots = $tourManager->getTourSpotsByCategory($category);
} else {
    $spots = $tourManager->getAllTourSpots();
}

$categories = $tourManager->getSpotCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Tourist Spots - Tourismo Zamboanga</title>
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

        <h2>Browse Tourist Spots</h2>

        <div style="background: #f5f5f5; padding: 15px; margin-bottom: 20px;">
            <form method="get" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="Search spots..." 
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
                
                <a href="browse-spots.php" style="padding: 8px 20px; background: #757575; color: white; text-decoration: none; display: inline-block;">
                    Clear
                </a>
            </form>
        </div>

        <?php if (empty($spots)): ?>
            <p style="text-align: center; padding: 40px; background: #f5f5f5;">
                No tourist spots found. Try a different search.
            </p>
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
                        
                        <p><?= htmlspecialchars($spot['spots_Description']) ?></p>
                        
                        <p><strong>Location:</strong> <?= htmlspecialchars($spot['spots_Address']) ?></p>

                        <?php if ($spot['spots_GoogleLink']): ?>
                            <p>
                                <a href="<?= htmlspecialchars($spot['spots_GoogleLink']) ?>" target="_blank" 
                                   style="color: #1976d2;">
                                    View on Google Maps
                                </a>
                            </p>
                        <?php endif; ?>

                        <a href="spot-details.php?id=<?= $spot['spots_ID'] ?>" 
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
