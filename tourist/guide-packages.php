<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/CustomPackage.php";
require_once "../php/Guide.php";

$customPackage = new CustomPackage();
$guide = new Guide();

$guide_id = $_GET['guide_id'] ?? 0;
$guideInfo = $guide->getGuideById($guide_id);

if (!$guideInfo) {
    header("Location: browse-guides.php");
    exit();
}

$offerings = $customPackage->getAllActiveOfferings($guide_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guide Packages - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .guide-header { background: #f0f0f0; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .guide-name { font-size: 24px; font-weight: bold; }
        .guide-rating { color: #ffc107; font-size: 20px; margin: 10px 0; }
        .package-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin: 20px 0; }
        .package-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .package-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .package-price { font-size: 24px; color: #28a745; font-weight: bold; margin: 10px 0; }
        .package-details { margin: 10px 0; font-size: 14px; }
        .package-details p { margin: 5px 0; }
        .btn { padding: 10px 15px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <h1>Tour Guide Packages</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse-tours.php">Browse Tours</a>
        <a href="browse-guides.php">Browse Guides</a>
        <a href="my-requests.php">My Requests</a>
        <a href="my-bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <div class="guide-header">
        <div class="guide-name"><?php echo htmlspecialchars($guideInfo['full_name']); ?></div>
        <div class="guide-rating">★ <?php echo $guideInfo['role_rating_score'] ? number_format($guideInfo['role_rating_score'], 1) : 'N/A'; ?>/5.0</div>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($guideInfo['email']); ?></p>
        <a href="request-custom-package.php?guide_id=<?php echo $guide_id; ?>" class="btn btn-success">Request Custom Package</a>
        <a href="browse-guides.php" class="btn btn-secondary">Back to Guides</a>
    </div>
    
    <h2>Available Packages</h2>
    
    <?php if (count($offerings) > 0): ?>
        <div class="package-grid">
            <?php foreach ($offerings as $pkg): ?>
                <div class="package-card">
                    <div class="package-title"><?php echo htmlspecialchars($pkg['tourPackage_Name']); ?></div>
                    <?php if ($pkg['is_customizable']): ?>
                        <span class="badge">Customizable</span>
                    <?php endif; ?>
                    
                    <div class="package-price">
                        ₱<?php echo number_format($pkg['offering_price'], 2); ?>
                        <?php if ($pkg['price_per_person'] > 0): ?>
                            <span style="font-size: 14px; color: #666;">+ ₱<?php echo number_format($pkg['price_per_person'], 2); ?>/person</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="package-details">
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($pkg['tourPackage_Duration']); ?></p>
                        <p><strong>Capacity:</strong> <?php echo $pkg['min_pax']; ?> - <?php echo $pkg['max_pax']; ?> persons</p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($pkg['tourPackage_Description']); ?></p>
                        <?php if ($pkg['total_spots'] > 0): ?>
                            <p><strong>Tour Spots:</strong> <?php echo $pkg['total_spots']; ?> locations</p>
                        <?php endif; ?>
                        <?php if ($pkg['availability_notes']): ?>
                            <p><strong>Availability:</strong> <?php echo nl2br(htmlspecialchars($pkg['availability_notes'])); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <a href="request-custom-package.php?guide_id=<?php echo $guide_id; ?>&package_id=<?php echo $pkg['tourPackage_ID']; ?>" class="btn btn-primary">
                            <?php echo $pkg['is_customizable'] ? 'Customize & Book' : 'Book This Package'; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>This guide hasn't created any package offerings yet.</p>
        <a href="request-custom-package.php?guide_id=<?php echo $guide_id; ?>" class="btn btn-success">Request a Custom Package</a>
    <?php endif; ?>
</body>
</html>
