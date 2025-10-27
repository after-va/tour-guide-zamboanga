<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";

$tourManager = new TourManager();
$user = $_SESSION['user'];

$error = '';
$success = '';
$package = null;

// Get package ID from URL
$package_id = $_GET['id'] ?? null;

if (!$package_id) {
    header('Location: manage-packages.php');
    exit;
}

// Fetch package details
$db = new Database();
$conn = $db->connect();

try {
    $sql = "SELECT * FROM Tour_Package WHERE tourPackage_ID = :id";
    $query = $conn->prepare($sql);
    $query->bindParam(':id', $package_id, PDO::PARAM_INT);
    $query->execute();
    $package = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$package) {
        header('Location: manage-packages.php?error=Package not found');
        exit;
    }
} catch (PDOException $e) {
    $error = "Error fetching package: " . $e->getMessage();
}

// Get all tour spots
$spots = $tourManager->getAllTourSpots();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tourPackage_Name = trim($_POST['tourPackage_Name'] ?? '');
    $tourPackage_Description = trim($_POST['tourPackage_Description'] ?? '');
    $tourPackage_Capacity = trim($_POST['tourPackage_Capacity'] ?? '');
    $tourPackage_Duration = trim($_POST['tourPackage_Duration'] ?? '');
    $selected_spots = $_POST['spots'] ?? [];
    
    if (empty($tourPackage_Name) || empty($tourPackage_Description) || empty($tourPackage_Capacity) || empty($tourPackage_Duration)) {
        $error = 'All fields are required.';
    } else {
        try {
            // Update package
            $sql = "UPDATE Tour_Package 
                    SET tourPackage_Name = :name,
                        tourPackage_Description = :description,
                        tourPackage_Capacity = :capacity,
                        tourPackage_Duration = :duration
                    WHERE tourPackage_ID = :id";
            
            $query = $conn->prepare($sql);
            $query->bindParam(':name', $tourPackage_Name);
            $query->bindParam(':description', $tourPackage_Description);
            $query->bindParam(':capacity', $tourPackage_Capacity);
            $query->bindParam(':duration', $tourPackage_Duration);
            $query->bindParam(':id', $package_id, PDO::PARAM_INT);
            
            if ($query->execute()) {
                $success = 'Tour package updated successfully!';
                // Refresh package data
                $sql = "SELECT * FROM Tour_Package WHERE tourPackage_ID = :id";
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $package_id, PDO::PARAM_INT);
                $query->execute();
                $package = $query->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Failed to update tour package. Please try again.';
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Edit Tour Package</h1>
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

        <?php if ($package): ?>
            <form method="post" style="background: white; border: 1px solid #ddd; padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <label for="tourPackage_Name">Package Name:</label><br>
                    <input type="text" id="tourPackage_Name" name="tourPackage_Name" required 
                           value="<?= htmlspecialchars($package['tourPackage_Name']) ?>"
                           style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="tourPackage_Description">Description:</label><br>
                    <textarea id="tourPackage_Description" name="tourPackage_Description" rows="4" required 
                              style="width: 100%; padding: 8px; box-sizing: border-box;"><?= htmlspecialchars($package['tourPackage_Description']) ?></textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="tourPackage_Duration">Duration:</label><br>
                    <input type="text" id="tourPackage_Duration" name="tourPackage_Duration" 
                           placeholder="e.g., 4 hours, Full day, 2 days" required 
                           value="<?= htmlspecialchars($package['tourPackage_Duration']) ?>"
                           style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="tourPackage_Capacity">Capacity (persons):</label><br>
                    <input type="text" id="tourPackage_Capacity" name="tourPackage_Capacity" 
                           placeholder="e.g., 10, 15-20" required 
                           value="<?= htmlspecialchars($package['tourPackage_Capacity']) ?>"
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
                    Update Package
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
