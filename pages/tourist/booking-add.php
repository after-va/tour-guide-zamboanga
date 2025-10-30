<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/tour-manager.php";
require_once "../../classes/guide.php";
require_once "../../classes/tourist.php";
require_once "../../classes/booking.php";

$tourist_ID = $_SESSION['user']['account_ID'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid tour package ID.";
    header("Location: tour-packages.php");
    exit();
}

$tourpackage_ID = intval($_GET['id']);
$tourManager = new TourManager();
$guideObj = new Guide();
$bookingObj = new Booking();
$touristObj = new Tourist();

$errors = [];

$package = $tourManager->getTourPackageById($tourpackage_ID);

if (!$package) {
    $_SESSION['error'] = "Tour package not found.";
    header("Location: tour-packages.php");
    exit();
}

$guides = $guideObj->viewAllGuide();
$guideName = "";
foreach ($guides as $guide) {
    if ($guide['guide_ID'] == $package['guide_ID']) {
        $guideName = $guide['guide_name'];
        break;
    }
}

$spots = $tourManager->getSpotsByPackage($tourpackage_ID);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $companion_names = $_POST['companion_name'] ?? [];
    $companion_categories = $_POST['companion_category'] ?? [];


    $companions_count = is_array($companion_names) ? count($companion_names) : 0;

    $min_people = intval($package['numberofpeople_based']);
    $max_people = intval($package['numberofpeople_maximum']);

    $booking_start_date = $_POST['booking_start_date'] ?? '';
    $booking_end_date = $_POST['booking_end_date'] ?? '';

    // ✅ Validate dates
    if (empty($booking_start_date) || empty($booking_end_date)) {
        $errors[] = "Please select both a start and end date.";
    } elseif ($booking_start_date > $booking_end_date) {
        $errors[] = "End date must be after the start date.";
    } elseif (strtotime($booking_start_date) < strtotime('today')) {
        $errors[] = "Start date cannot be in the past.";
    }

    if(empty($companion_names)){
        $errors = "This is required at least";
    }

    if ($companions_count < $min_people) {
        $errors[] = "You must add at least {$min_people} companions.";
    }

    if ($companions_count > $max_people) {
        $errors[] = "You can only add up to {$max_people} companions.";
    }

    if (empty($errors)) {
        $booking_ID = $bookingObj->addBookingForTourist($tourist_ID, $tourpackage_ID, $booking_start_date, $booking_end_date);


        if ($booking_ID) {
            foreach ($companion_names as $index => $name) {
                $category_ID = $companion_categories[$index];
                $bookingObj->addCompanionToBooking($booking_ID, $name, $category_ID);
            }
        }

        
        // $bookingObj->createBooking($tourist_ID, $tourpackage_ID, $companion_names, $companion_categories);
        $_SESSION['success'] = "Companions validated successfully. Proceeding to payment.";
        header("Location: payment.php?id=" . $booking_ID);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Booking</title>
    
</head>
<body>
    <div class="container">
        <h1>Book Tour Package</h1>

        <?php if (!empty($errors)): ?>
            <div style="color:red;">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="package-details">
            <h3>Package Information</h3>
            <p><strong>Package Name:</strong> <?= htmlspecialchars($package['tourpackage_name']); ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($package['tourpackage_desc']); ?></p>
            <p><strong>Guide:</strong> <?= htmlspecialchars($guideName ?: 'N/A'); ?></p>
            <p><strong>Schedule Days:</strong> <?= htmlspecialchars($package['schedule_days']); ?> days</p>
            <p><strong>Maximum People:</strong> <?= htmlspecialchars($package['numberofpeople_maximum']); ?></p>
            <p><strong>Minimum People:</strong> <?= htmlspecialchars($package['numberofpeople_based']); ?></p>
            <p><strong>Base Amount:</strong> <?= htmlspecialchars($package['pricing_currency'] . ' ' . number_format($package['pricing_based'], 2)); ?></p>
            <p><strong>Discount:</strong> <?= htmlspecialchars($package['pricing_currency'] . ' ' . number_format($package['pricing_discount'], 2)); ?></p>

            <?php if (!empty($spots)): ?>
                <p><strong>Tour Spots:</strong></p>
                <ul>
                    <?php foreach ($spots as $spot): ?>
                        <li>
                            <strong><?= htmlspecialchars($spot['spots_name']); ?></strong> - 
                            <?= htmlspecialchars($spot['spots_description']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>No associated tour spots.</em></p>
            <?php endif; ?>
        </div>

        <form action="" method="post">
            <h2>Booking Dates</h2>
            <label for="booking_start_date">Start Date:</label>
            <input type="date" name="booking_start_date" id="booking_start_date" required>

            <label for="booking_end_date">End Date:</label>
            <input type="date" name="booking_end_date" id="booking_end_date" required>

            <h2>Companions</h2>
            <div id="inputContainer">
                <div>
                    <input type="text" name="companion_name[]" placeholder="Name" required>
                    <select name="companion_category[]" required>
                        <option value="">-- SELECT CATEGORY ---</option>
                        <?php foreach ($bookingObj->getAllCompanionCategories() as $c) { ?>
                            <option value="<?= $c['companion_category_ID'] ?>"> <?= $c['companion_category_name'] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button type="button" onclick="addInput()">Add Companion</button><br><br>

            <input type="submit" value="Proceed to Payment">
        </form>

        <a href="tour-packages-browse.php">← Back to Tour Packages</a>
    </div>
    <script>
        // ✅ Allow dynamic companion input fields
        function addInput() {
            const container = document.getElementById('inputContainer');
            const div = document.createElement('div');
            div.innerHTML = `
                <input type="text" name="companion_name[]" placeholder="Name" required>
                <select name="companion_category[]" required>
                    <option value="">-- SELECT CATEGORY ---</option>
                    <?php foreach ($bookingObj->getAllCompanionCategories() as $c) { ?>
                        <option value="<?= $c['companion_category_ID'] ?>"> <?= $c['companion_category_name'] ?> </option>
                    <?php } ?>
                </select>
                <button type="button" onclick="this.parentNode.remove()">Remove</button>
            `;
            container.appendChild(div);
        }
    </script>
</body>
</html>
