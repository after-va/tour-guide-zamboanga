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
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tourPackage_Name = trim($_POST['tourPackage_Name'] ?? '');
    $tourPackage_Description = trim($_POST['tourPackage_Description'] ?? '');
    $tourPackage_Capacity = trim($_POST['tourPackage_Capacity'] ?? '');
    $tourPackage_Duration = trim($_POST['tourPackage_Duration'] ?? '');
    $selected_spots = $_POST['spots'] ?? [];
    
    if (empty($tourPackage_Name) || empty($tourPackage_Description) || empty($tourPackage_Capacity) || empty($tourPackage_Duration)) {
        $error = 'All fields are required.';
    } else {
        $result = $tourManager->createPackageWithSpots(
            $tourPackage_Name,
            $tourPackage_Description,
            $tourPackage_Capacity,
            $tourPackage_Duration,
            $selected_spots
        );
        
        if ($result) {
            $success = 'Tour package created successfully!';
            header('Location: manage-packages.php');
            exit;
        } else {
            $error = 'Failed to create tour package. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Add New Tour Package</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="manage-packages.php" style="color: white; margin-right: 15px;">Packages</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <a href="manage-packages.php" style="display: inline-block; margin-bottom: 20px; color: #1976d2;">
            &larr; Back to Packages
        </a>

        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 15px; margin-bottom: 20px; border: 1px solid #ef5350;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin-bottom: 20px; border: 1px solid #4caf50;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post" style="background: white; border: 1px solid #ddd; padding: 20px;">
            <div style="margin-bottom: 15px;">
                <label for="tourPackage_Name">Package Name:</label><br>
                <input type="text" id="tourPackage_Name" name="tourPackage_Name" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="tourPackage_Description">Description:</label><br>
                <textarea id="tourPackage_Description" name="tourPackage_Description" rows="4" required 
                          style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="tourPackage_Duration">Duration:</label><br>
                <input type="text" id="tourPackage_Duration" name="tourPackage_Duration" 
                       placeholder="e.g., 4 hours, Full day, 2 days" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="tourPackage_Capacity">Capacity (persons):</label><br>
                <input type="text" id="tourPackage_Capacity" name="tourPackage_Capacity" 
                       placeholder="e.g., 10, 15-20" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Select Tourist Spots (Optional):</label><br>
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                    <?php foreach ($spots as $spot): ?>
                        <div style="margin-bottom: 8px;">
                            <label style="display: block; padding: 8px; background: white; cursor: pointer;">
                                <input type="checkbox" name="spots[]" value="<?= $spot['spots_ID'] ?>">
                                <strong><?= htmlspecialchars($spot['spots_Name']) ?></strong>
                                <br>
                                <small style="color: #757575;"><?= htmlspecialchars($spot['spots_Description']) ?></small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #4caf50; color: white; border: none; cursor: pointer; font-size: 16px;">
                Create Package
            </button>
        </form>
    </div>
</body>
</html>
