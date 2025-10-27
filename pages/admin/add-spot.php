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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spots_Name = trim($_POST['spots_Name'] ?? '');
    $spots_Description = trim($_POST['spots_Description'] ?? '');
    $spots_category = trim($_POST['spots_category'] ?? '');
    $spots_Address = trim($_POST['spots_Address'] ?? '');
    $spots_GoogleLink = trim($_POST['spots_GoogleLink'] ?? '');
    
    if (empty($spots_Name) || empty($spots_Description) || empty($spots_Address)) {
        $error = 'Name, Description, and Address are required.';
    } else {
        $result = $tourManager->createTourSpot(
            $spots_Name,
            $spots_Description,
            $spots_category,
            $spots_Address,
            $spots_GoogleLink
        );
        
        if ($result) {
            $success = 'Tourist spot created successfully!';
            header('Location: manage-spots.php');
            exit;
        } else {
            $error = 'Failed to create tourist spot. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tourist Spot - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Add New Tourist Spot</h1>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="manage-spots.php" style="color: white; margin-right: 15px;">Spots</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <a href="manage-spots.php" style="display: inline-block; margin-bottom: 20px; color: #1976d2;">
            &larr; Back to Spots
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
                <label for="spots_Name">Spot Name:</label><br>
                <input type="text" id="spots_Name" name="spots_Name" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="spots_Description">Description:</label><br>
                <textarea id="spots_Description" name="spots_Description" rows="4" required 
                          style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="spots_category">Category:</label><br>
                <select id="spots_category" name="spots_category" style="width: 100%; padding: 8px;">
                    <option value="">Select Category</option>
                    <option value="Beach">Beach</option>
                    <option value="Historical">Historical</option>
                    <option value="Cultural">Cultural</option>
                    <option value="Nature">Nature</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Religious">Religious</option>
                    <option value="Adventure">Adventure</option>
                    <option value="Food & Dining">Food & Dining</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="spots_Address">Address:</label><br>
                <input type="text" id="spots_Address" name="spots_Address" required 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="spots_GoogleLink">Google Maps Link (Optional):</label><br>
                <input type="url" id="spots_GoogleLink" name="spots_GoogleLink" 
                       placeholder="https://maps.app.goo.gl/..." 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #4caf50; color: white; border: none; cursor: pointer; font-size: 16px;">
                Create Tourist Spot
            </button>
        </form>
    </div>
</body>
</html>
