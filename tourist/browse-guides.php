<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/Guide.php";
require_once "../php/CustomPackage.php";

$guide = new Guide();
$customPackage = new CustomPackage();

$allGuides = $guide->getAllGuides();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Tour Guides - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .guide-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .guide-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9; }
        .guide-name { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .guide-rating { color: #ffc107; font-size: 18px; }
        .guide-info { margin: 10px 0; font-size: 14px; }
        .btn { padding: 10px 15px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <h1>Browse Tour Guides</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse-tours.php">Browse Tours</a>
        <a href="browse-guides.php">Browse Guides</a>
        <a href="my-requests.php">My Requests</a>
        <a href="my-bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <h2>Available Tour Guides</h2>
    <p>Select a guide to view their packages or request a custom tour package.</p>
    
    <div class="guide-grid">
        <?php foreach ($allGuides as $g): ?>
            <div class="guide-card">
                <div class="guide-name"><?php echo htmlspecialchars($g['full_name']); ?></div>
                <div class="guide-rating">
                    ★ <?php echo $g['role_rating_score'] ? number_format($g['role_rating_score'], 1) : 'N/A'; ?>/5.0
                </div>
                <div class="guide-info">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($g['email']); ?></p>
                    <?php if ($g['phone_number']): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($g['phone_number']); ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="guide-packages.php?guide_id=<?php echo $g['person_ID']; ?>" class="btn btn-primary">View Packages</a>
                    <a href="request-custom-package.php?guide_id=<?php echo $g['person_ID']; ?>" class="btn btn-success">Request Custom Package</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
