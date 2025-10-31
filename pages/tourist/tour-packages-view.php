<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";
require_once "../../classes/guide.php";
require_once "../../classes/tourist.php";
$tourist_ID = $_SESSION['user']['account_ID'];
$toristObj = new Tourist();
// Validate and get package ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid tour package ID.";
    header("Location: tour-packages.php");
    exit();
}

$tourpackage_ID = intval($_GET['id']);
$tourManager = new TourManager();
$guideObj = new Guide();

// Get package details
$package = $tourManager->getTourPackageById($tourpackage_ID);

if (!$package) {
    $_SESSION['error'] = "Tour package not found.";
    header("Location: tour-packages.php");
    exit();
}

// Get guide name
$guides = $guideObj->viewAllGuide();
$guideName = "";
foreach ($guides as $guide) {
    if ($guide['guide_ID'] == $package['guide_ID']) {
        $guideName = $guide['guide_name'];
        break;
    }
}

// Get associated tour spots
$spots = $tourManager->getSpotsByPackage($tourpackage_ID);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Tour Package - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fafafa;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        h1 {
            margin-bottom: 15px;
        }
        .package-details {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-radius: 5px;
        }
        .package-details p {
            margin: 10px 0;
        }
        .package-details strong {
            display: inline-block;
            width: 180px;
        }
        .spots-list {
            margin-left: 180px;
            padding-left: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Tour Package</h1>

        <div class="package-details">
            <h3>Package Information</h3>
            <p><strong>Package Name:</strong> <?php echo htmlspecialchars($package['tourpackage_name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($package['tourpackage_desc']); ?></p>
            <p><strong>Guide:</strong> <?php echo htmlspecialchars($guideName ?: 'N/A'); ?></p>
            <p><strong>Schedule Days:</strong> <?php echo htmlspecialchars($package['schedule_days']); ?> days</p>
            <p><strong>Maximum People:</strong> <?php echo htmlspecialchars($package['numberofpeople_maximum']); ?></p>
            <p><strong>Minimum People:</strong> <?php echo htmlspecialchars($package['numberofpeople_based']); ?></p>
            <p><strong>Base Amount:</strong> 
                <?php echo htmlspecialchars($package['pricing_currency'] . ' ' . number_format($package['pricing_based'], 2)); ?>
            </p>
            <p><strong>Discount:</strong> 
                <?php echo htmlspecialchars($package['pricing_currency'] . ' ' . number_format($package['pricing_discount'], 2)); ?>
            </p>

            <?php if (!empty($spots)): ?>
            <p><strong>Tour Spots:</strong></p>
            <ul class="spots-list">
                <?php foreach ($spots as $spot): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($spot['spots_name']); ?></strong>
                        <div style="margin-left: 20px; color: #555;">
                            <?php echo htmlspecialchars($spot['spots_description']); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p><em>No associated tour spots.</em></p>
            <?php endif; ?>
        </div>

        <a href="tour-packages-browse.php" class="btn btn-secondary">← Back to Tour Packages</a>
    </div>
</body>
</html>
