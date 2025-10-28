<?php
require_once "../../classes/auth.php";
require_once "../../classes/guide-manager.php";
require_once "../../classes/tour-manager.php";

// Initialize managers
$auth = new Auth();
$guideManager = new GuideManager();
$tourManager = new TourManager();

// Ensure user is logged in and is a guide
$auth->requireGuide();
$guide_ID = $_SESSION['user']['person_ID'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tourPackage_ID = $_POST['tourPackage_ID'];
    $offering_price = $_POST['offering_price'];
    $price_per_person = !empty($_POST['price_per_person']) ? $_POST['price_per_person'] : null;
    $min_pax = $_POST['min_pax'];
    $max_pax = !empty($_POST['max_pax']) ? $_POST['max_pax'] : null;
    $is_customizable = isset($_POST['is_customizable']) ? 1 : 0;

    if ($guideManager->createGuideOffering(
        $guide_ID, 
        $tourPackage_ID, 
        $offering_price, 
        $price_per_person, 
        $min_pax, 
        $max_pax, 
        $is_customizable
    )) {
        $success_message = "Package offering created successfully!";
    } else {
        $error_message = "Failed to create package offering. Please try again.";
    }
}

// Get available packages and current offerings
$packages = $tourManager->getAllTourPackages();
$offerings = $guideManager->getGuideOfferings($guide_ID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Package Pricing - Guide Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
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
        input[type="text"],
        select {
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

        .offerings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .offering-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            background: #f9f9f9;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Package Pricing</h1>

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
            <h2>Create New Package Offering</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="tourPackage_ID">Select Tour Package</label>
                    <select name="tourPackage_ID" required>
                        <option value="">Select a package...</option>
                        <?php foreach ($packages as $package): ?>
                            <option value="<?= $package['tourPackage_ID'] ?>">
                                <?= htmlspecialchars($package['tourPackage_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="offering_price">Base Package Price (PHP)</label>
                    <input type="number" name="offering_price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="price_per_person">Price Per Person (PHP) - Optional</label>
                    <input type="number" name="price_per_person" step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label for="min_pax">Minimum PAX</label>
                    <input type="number" name="min_pax" value="1" min="1" required>
                </div>

                <div class="form-group">
                    <label for="max_pax">Maximum PAX (Optional)</label>
                    <input type="number" name="max_pax" min="1">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_customizable" checked>
                        Allow Package Customization
                    </label>
                </div>

                <button type="submit">Create Package Offering</button>
            </form>
        </div>

        <div class="section">
            <h2>Current Package Offerings</h2>
            <?php if (empty($offerings)): ?>
                <p style="text-align: center; padding: 20px; background: #f5f5f5;">
                    No package offerings yet. Create one using the form above.
                </p>
            <?php else: ?>
                <div class="offerings-grid">
                    <?php foreach ($offerings as $offering): ?>
                        <div class="offering-card">
                            <h3><?= htmlspecialchars($offering['tourPackage_Name']) ?></h3>
                            <p><strong>Base Price:</strong> PHP <?= number_format($offering['offering_price'], 2) ?></p>
                            <?php if ($offering['price_per_person']): ?>
                                <p><strong>Per Person:</strong> PHP <?= number_format($offering['price_per_person'], 2) ?></p>
                            <?php endif; ?>
                            <p><strong>PAX Range:</strong> <?= $offering['min_pax'] ?> - <?= $offering['max_pax'] ?? 'Unlimited' ?></p>
                            <p><strong>Customizable:</strong> <?= $offering['is_customizable'] ? 'Yes' : 'No' ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
