<?php
require_once "../../classes/auth.php";
require_once "../../classes/guide-manager.php";

// Initialize managers
$auth = new Auth();
$guideManager = new GuideManager();

// Ensure user is logged in and is a guide
$auth->requireGuide();
$guide_ID = $_SESSION['person_ID'];

// Get the offering ID from URL
$offering_ID = isset($_GET['id']) ? $_GET['id'] : null;

if (!$offering_ID) {
    header('Location: manage-pricing.php');
    exit;
}

// Get the offering details
$offering = $guideManager->getGuideOffering($offering_ID);

// Check if offering exists and belongs to the guide
if (!$offering || $offering['guide_ID'] != $guide_ID) {
    header('Location: manage-pricing.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offering_price = $_POST['offering_price'];
    $price_per_person = !empty($_POST['price_per_person']) ? $_POST['price_per_person'] : null;
    $min_pax = $_POST['min_pax'];
    $max_pax = !empty($_POST['max_pax']) ? $_POST['max_pax'] : null;
    $is_customizable = isset($_POST['is_customizable']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($guideManager->updateGuideOffering(
        $offering_ID,
        $guide_ID,
        $offering_price,
        $price_per_person,
        $min_pax,
        $max_pax,
        $is_customizable,
        $is_active
    )) {
        $success_message = "Package offering updated successfully!";
        // Refresh offering details
        $offering = $guideManager->getGuideOffering($offering_ID);
    } else {
        $error_message = "Failed to update package offering. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package Offering - Guide Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h1, h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="number"],
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #1565c0;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .package-info {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Package Offering</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="section">
            <div class="package-info">
                <h3><?= htmlspecialchars($offering['tourPackage_Name']) ?></h3>
                <p><?= htmlspecialchars($offering['tourPackage_Description']) ?></p>
                <p><strong>Duration:</strong> <?= htmlspecialchars($offering['tourPackage_Duration']) ?></p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="offering_price">Base Package Price (PHP)</label>
                    <input type="number" name="offering_price" step="0.01" min="0" 
                           value="<?= $offering['offering_price'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="price_per_person">Price Per Person (PHP) - Optional</label>
                    <input type="number" name="price_per_person" step="0.01" min="0" 
                           value="<?= $offering['price_per_person'] ?>">
                </div>

                <div class="form-group">
                    <label for="min_pax">Minimum PAX</label>
                    <input type="number" name="min_pax" min="1" 
                           value="<?= $offering['min_pax'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="max_pax">Maximum PAX (Optional)</label>
                    <input type="number" name="max_pax" min="1" 
                           value="<?= $offering['max_pax'] ?>">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_customizable" 
                               <?= $offering['is_customizable'] ? 'checked' : '' ?>>
                        Allow Package Customization
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" 
                               <?= $offering['is_active'] ? 'checked' : '' ?>>
                        Package Offering is Active
                    </label>
                </div>

                <button type="submit">Update Package Offering</button>
                <a href="manage-pricing.php" style="display: inline-block; margin-left: 10px; color: #666; text-decoration: none;">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</body>
</html>