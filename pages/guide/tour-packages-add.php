<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tour Guide') {
    header('Location: ../../index.php');
    exit;
}
require_once "../../classes/guide.php";

$guideObj = new Guide();

$guide_ID = $guideObj->getGuide_ID($_SESSION['user']['account_ID']);
$packages = $guideObj->viewPackageByGuideID($guide_ID);


// Load old input & errors from session
$guidePackage = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';

// Clear session data
unset($_SESSION['old_input'], $_SESSION['errors'], $_SESSION['success']);

$spots = $guideObj->getAllSpots();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // === SANITIZE INPUT ===
    $guidePackage["spots"]               = $_POST['spots'] ?? [];
    $guidePackage["tourpackage_name"]    = trim(htmlspecialchars($_POST['tourpackage_name'] ?? ''));
    $guidePackage["tourpackage_desc"]    = trim(htmlspecialchars($_POST['tourpackage_desc'] ?? ''));
    $guidePackage["schedule_days"]       = trim(htmlspecialchars($_POST['schedule_days'] ?? ''));
    $guidePackage["numberofpeople_maximum"] = trim(htmlspecialchars($_POST['numberofpeople_maximum'] ?? ''));
    $guidePackage["numberofpeople_based"] = trim(htmlspecialchars($_POST['numberofpeople_based'] ?? ''));
    $guidePackage["basedAmount"]         = trim(htmlspecialchars($_POST['basedAmount'] ?? ''));
    $guidePackage["discount"]            = trim(htmlspecialchars($_POST['discount'] ?? ''));
    $guidePackage["currency"]            = 'PHP';

    // === VALIDATION ===
    

    if (empty($guidePackage["tourpackage_name"])) {
        $errors["tourpackage_name"] = "Tour package name is required";
    }

    if (empty($guidePackage["tourpackage_desc"])) {
        $errors["tourpackage_desc"] = "Description is required";
    }

    if (empty($guidePackage["schedule_days"]) || !is_numeric($guidePackage["schedule_days"]) || $guidePackage["schedule_days"] < 1) {
        $errors["schedule_days"] = "Number of days must be at least 1";
    }

    if (empty($guidePackage["numberofpeople_maximum"]) || !is_numeric($guidePackage["numberofpeople_maximum"]) || $guidePackage["numberofpeople_maximum"] < 1) {
        $errors["numberofpeople_maximum"] = "Maximum people must be at least 1";
    }

    if (empty($guidePackage["numberofpeople_based"])) {
        $errors["numberofpeople_based"] = "Minimum people is required";
    } elseif (!is_numeric($guidePackage["numberofpeople_based"]) || $guidePackage["numberofpeople_based"] < 1) {
        $errors["numberofpeople_based"] = "Minimum people must be at least 1";
    }

    if (empty($guidePackage["basedAmount"]) || !is_numeric($guidePackage["basedAmount"]) || $guidePackage["basedAmount"] < 0) {
        $errors["basedAmount"] = "Base amount must be a positive number";
    }

    if (!is_numeric($guidePackage["discount"]) || $guidePackage["discount"] < 0) {
        $errors["discount"] = "Discount must be 0 or more";
    }

    // === SAVE ONLY IF NO ERRORS ===
    if (empty($errors)) {
        $tourpackage_ID = $guideObj->addTourPackage( 
            $guide_ID,
            $guidePackage["tourpackage_name"],
            $guidePackage["tourpackage_desc"],
            $guidePackage["schedule_days"],
            $guidePackage["numberofpeople_maximum"],
            $guidePackage["numberofpeople_based"],
            $guidePackage["currency"],
            $guidePackage["basedAmount"],
            $guidePackage["discount"]
        );

        $result = $guideObj->linkSpotToPackage($tourpackage_ID, $guidePackage["spots"] );

        if ($result) {
            $_SESSION['success'] = "Tour package added successfully!";
            header("Location: tour-packages.php"); // Changed from view_packages.php to tour-packages.php
            exit;
        } else {
            $errors['general'] = "Failed to save package. Please try again.";
        }
    }

    // === KEEP DATA IF ERRORS ===
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $guidePackage;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tour Package</title>

</head>
<body>
    <h1>Add Tour Package</h1>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <p class="error"><?= $errors['general'] ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <!-- Tour Package Name -->
        <div>
            <label for="tourpackage_name">Tour Package Name:</label>
            <input type="text" name="tourpackage_name" id="tourpackage_name" 
                   value="<?= $guidePackage['tourpackage_name'] ?? '' ?>">
            <?php if (isset($errors['tourpackage_name'])): ?>
                <span class="error"><?= $errors['tourpackage_name'] ?></span>
            <?php endif; ?>
        </div>
        <br>

        <!-- Description -->
        <div>
            <label for="tourpackage_desc">Description:</label>
            <textarea name="tourpackage_desc" id="tourpackage_desc"><?= $guidePackage['tourpackage_desc'] ?? '' ?></textarea>
            <?php if (isset($errors['tourpackage_desc'])): ?>
                <span class="error"><?= $errors['tourpackage_desc'] ?></span>
            <?php endif; ?>
        </div>
        <br>

        <!-- Schedule Days -->
        <div>
            <label for="schedule_days">Schedule Days:</label>
            <input type="number" name="schedule_days" id="schedule_days" min="1"
                   value="<?= $guidePackage['schedule_days'] ?? '' ?>">
            <?php if (isset($errors['schedule_days'])): ?>
                <span class="error"><?= $errors['schedule_days'] ?></span>
            <?php endif; ?>
        </div>
        <br>

        <!-- Maximum People -->
        <div>
            <label for="numberofpeople_maximum">Maximum People:</label>
            <input type="number" name="numberofpeople_maximum" id="numberofpeople_maximum" min="1"
                   value="<?= $guidePackage['numberofpeople_maximum'] ?? '' ?>">
            <?php if (isset($errors['numberofpeople_maximum'])): ?>
                <span class="error"><?= $errors['numberofpeople_maximum'] ?></span>
            <?php endif; ?>
        </div>
        <br>

        <!-- Minimum People -->
        <div>
            <label for="numberofpeople_based">Minimum People:</label>
            <input type="number" name="numberofpeople_based" id="numberofpeople_based" min="1"
                   value="<?= $guidePackage['numberofpeople_based'] ?? '' ?>">
            <?php if (isset($errors['numberofpeople_based'])): ?>
                <span class="error"><?= $errors['numberofpeople_based'] ?></span>
            <?php endif; ?>
        </div>
        <br>

        <!-- Base Amount -->
        <div>
            <label for="basedAmount">Base Amount (PHP):</label>
            <input type="number" name="basedAmount" id="basedAmount" min="0" step="0.01"
                   value="<?= $guidePackage['basedAmount'] ?? '' ?>">
            <?php if (isset($errors['basedAmount'])): ?>
                <span class="error"><?= $errors['basedAmount'] ?></span>
            <?php endif; ?>
        </div>
        <br>

        <!-- Discount -->
        <div>
            <label for="discount">Discount (PHP):</label>
            <input type="number" name="discount" id="discount" min="0" step="0.01"
                   value="<?= $guidePackage['discount'] ?? '0' ?>">
            <?php if (isset($errors['discount'])): ?>
                <span class="error"><?= $errors['discount'] ?></span>
            <?php endif; ?>
        </div>
        <br>


        <!-- Tourist Spots -->
        <div>
            <label>Select Tourist Spots (Optional):</label><br>
            <div class="checkbox-list">
                <?php foreach ($spots as $spot): ?>
                    <label style="display: block; margin: 8px 0;">
                        <input type="checkbox" name="spots[]" value="<?= $spot['spots_ID'] ?>"
                            <?= in_array($spot['spots_ID'], $guidePackage['spots'] ?? []) ? 'checked' : '' ?>>
                        <strong><?= htmlspecialchars($spot['spots_name']) ?></strong>
                        <small style="color: #666; display: block;">
                            <?= htmlspecialchars($spot['spots_description']) ?>
                        </small>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <br>

        <button type="submit" style="padding: 10px 20px; font-size: 16px;">Add Package</button>
    </form>
    <!-- Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eum maiores laborum dolorem doloribus tempore nulla debitis provident tempora beatae deleniti officiis consequatur, minima modi magnam dicta expedita numquam corporis delectus? !-->
</body>
</html>