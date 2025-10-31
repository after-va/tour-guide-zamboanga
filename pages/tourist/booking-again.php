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

// Initialize all required objects early
$tourManager = new TourManager();
$guideObj = new Guide();
$bookingObj = new Booking();
$touristObj = new Tourist();

$tourist_ID = $_SESSION['user']['account_ID'];

// Get parameters safely
$oldBookingID = isset($_GET['id']) ? intval($_GET['id']) : 0; // old booking ID
$tourpackage_ID = isset($_GET['ref']) ? intval($_GET['ref']) : 0; // tour package ID

$errors = [];
$rebookData = null;
// well
// Load previous booking details (only if valid and owned by this user)
if ($oldBookingID > 0) {
    $rebookData = $bookingObj->getBookingDetailsByBooking($oldBookingID);
    if (!$rebookData || !isset($rebookData['tourist_ID']) || $rebookData['tourist_ID'] != $tourist_ID || $rebookData['booking_status'] !== 'Cancelled') {
        

    }
}

// Validate tour package
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

// Get tour spots
$spots = $tourManager->getSpotsByPackage($tourpackage_ID);

// Handle form submission
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

    // ✅ Validate companions
    if (empty($companion_names)) {
        $errors[] = "Please add at least one companion.";
    }

    if ($companions_count < $min_people) {
        $errors[] = "You must add at least {$min_people} companions.";
    }

    if ($companions_count > $max_people) {
        $errors[] = "You can only add up to {$max_people} companions.";
    }

    // ✅ Proceed if no errors
    if (empty($errors)) {
        $booking_ID = $bookingObj->addBookingForTourist($tourist_ID, $tourpackage_ID, $booking_start_date, $booking_end_date);

        if ($booking_ID) {
            foreach ($companion_names as $index => $name) {
                $category_ID = $companion_categories[$index];
                $bookingObj->addCompanionToBooking($booking_ID, $name, $category_ID);
            }

            $_SESSION['success'] = "Booking re-created successfully. Proceeding to payment.";
            header("Location: payment.php?id=" . $booking_ID);
            exit();
        } else {
            $errors[] = "Failed to create booking. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rebook Tour Package</title>
</head>
<body>
    <div class="container">
        <h1>Rebook Tour Package</h1>

        <?php if (!empty($errors)): ?>
            <div style="color:red;">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($rebookData){?>
            <div style="background:#f8f9fa; padding:10px; border-radius:8px;">
                <p>⚠️ You are rebooking your cancelled trip from 
                   <strong><?= htmlspecialchars($rebookData['booking_start_date']) ?></strong> 
                   to <strong><?= htmlspecialchars($rebookData['booking_end_date']) ?></strong>.
                </p>
            </div>
        <?php } ?>

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
                        <li><strong><?= htmlspecialchars($spot['spots_name']); ?></strong> - <?= htmlspecialchars($spot['spots_description']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>No associated tour spots.</em></p>
            <?php endif; ?>
        </div>

        <form action="" method="post">
            <h2>Booking Dates</h2>
            <?php $bookingdate =[];
                if($rebookData){
                    $bookingdate = $bookingObj->getBookingDateByOldBooking($oldBookingID);
                }
                if (!empty($bookingdate)){
                ?>
            <label for="booking_start_date">Start Date:</label>
            <input type="date" name="booking_start_date" id="booking_start_date" 
                value="<?= htmlspecialchars($bookingdate['booking_start'] ?? '') ?>" required>

            <label for="booking_end_date">End Date:</label>
            <input type="date" name="booking_end_date" id="booking_end_date" 
                value="<?= htmlspecialchars($bookingdate['booking_end'] ?? '') ?>" readonly required>
            <?php }?>    
            <h2>Companions</h2>
            <div id="inputContainer">
                <?php 
                $companions = [];
                if ($rebookData) {
                    $companions = $bookingObj->getCompanionsByBooking($oldBookingID);
                }

                if (!empty($companions)) {
                    foreach ($companions as $c) { ?>
                        <div>
                            <input type="text" name="companion_name[]" value="<?= htmlspecialchars($c['companion_name']) ?>" required>
                            <select name="companion_category[]" required>
                                <option value="">-- SELECT CATEGORY ---</option>
                                <?php 
                                foreach ($bookingObj->getAllCompanionCategories() as $cat) {
                                    $selected = $c['companion_category_ID'] == $cat['companion_category_ID'] ? 'selected' : '';
                                    echo "<option value='{$cat['companion_category_ID']}' $selected>{$cat['companion_category_name']}</option>";
                                }
                                ?>
                            </select>
                            <button type="button" onclick="this.parentNode.remove()">Remove</button>
                        </div>
                    <?php } 
                } else { ?>
                    <div>
                        <input type="text" name="companion_name[]" placeholder="Name" required>
                        <select name="companion_category[]" required>
                            <option value="">-- SELECT CATEGORY ---</option>
                            <?php foreach ($bookingObj->getAllCompanionCategories() as $c) { ?>
                                <option value="<?= $c['companion_category_ID'] ?>"> <?= $c['companion_category_name'] ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
            </div>

            <button type="button" onclick="addInput()">Add Companion</button><br><br>
            <input type="submit" value="Proceed to Payment">
        </form>

        <a href="tour-packages-browse.php">← Back to Tour Packages</a>
    </div>

    <script>
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

        const scheduleDays = <?= intval($package['schedule_days']); ?>;

        document.getElementById('booking_start_date').addEventListener('change', function () {
            const startDate = new Date(this.value);
            if (isNaN(startDate.getTime())) return;
            startDate.setDate(startDate.getDate() + scheduleDays - 1);
            const formatted = startDate.toISOString().split('T')[0];
            document.getElementById('booking_end_date').value = formatted;
        });
    </script>
</body>
</html>
