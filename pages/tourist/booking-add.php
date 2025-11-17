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
require_once "../../classes/activity-log.php";

$activityObj = new ActivityLogs();
$tourist_ID = $_SESSION['user']['account_ID'];
$account_ID =  $_SESSION['account_ID'];

$notification = $activityObj->touristNotification($tourist_ID);

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

$package = $tourManager->getTourPackageDetailsByID($tourpackage_ID);
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
    // ... [your existing POST validation logic remains unchanged] ...
    // (I'll keep it exactly as you had it for accuracy)
    $errors = [];

    $is_selfIncluded = (isset($_POST['is_selfIncluded']) && $_POST['is_selfIncluded'] === 'yes') ? 1 : 0;
    $companion_names = $_POST['companion_name'] ?? [];
    $companion_categories = $_POST['companion_category'] ?? [];

    if (!is_array($companion_names)) $companion_names = [];
    if (!is_array($companion_categories)) $companion_categories = [];

    $companions_count = count($companion_names);
    $min_people = (int)$package['numberofpeople_based'];
    $max_people = (int)$package['numberofpeople_maximum'];
    $total_people = $is_selfIncluded + $companions_count;

    if ($total_people < $min_people) {
        $errors[] = "You must have at least {$min_people} person(s) in total.";
    }
    if ($total_people > $max_people) {
        $errors[] = "You can only have up to {$max_people} people.";
    }
    if ($max_people === 1) {
        if ($is_selfIncluded && $companions_count > 0) {
            $errors[] = "Only one person is allowed — remove companions.";
        }
        if (!$is_selfIncluded && $companions_count === 0) {
            $errors[] = "One companion is required if you do not include yourself.";
        }
    }

    $booking_start_date = $_POST['booking_start_date'] ?? '';
    $booking_end_date = $_POST['booking_end_date'] ?? '';

    $bookings = $bookingObj->existingBookingsInGuide($package['guide_ID']);
    foreach ($bookings as $b) {
        if (
            strtotime($booking_start_date) <= strtotime($b['booking_end_date']) &&
            strtotime($b['booking_start_date']) <= strtotime($booking_end_date)
        ) {
            $errors[] = "The guide is already booked during this period.";
            break;
        }
    }

    if (empty($booking_start_date) || empty($booking_end_date)) {
        $errors[] = "Please select both a start and end date.";
    } elseif ($booking_start_date > $booking_end_date) {
        $errors[] = "End date must be after the start date.";
    } elseif (strtotime($booking_start_date) < strtotime('today')) {
        $errors[] = "Start date cannot be in the past.";
    }

    if (empty($errors)) {
        $booking_ID = $bookingObj->addBookingForTourist(
            $tourist_ID,
            $tourpackage_ID,
            $booking_start_date,
            $booking_end_date,
            $is_selfIncluded
        );

        if ($booking_ID && $companions_count > 0) {
            foreach ($companion_names as $index => $name) {
                $category_ID = $companion_categories[$index] ?? null;
                if ($name && $category_ID) {
                    $bookingObj->addCompanionToBooking($booking_ID, $name, $category_ID);
                }
            }
        }

        $_SESSION['success'] = "Booking successful. Proceeding to payment.";
        $activityObj->touristBook($booking_ID, $tourist_ID);
        header("Location: payment-form.php?id=" . $booking_ID);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tour Package - Tourismo Zamboanga</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Header CSS (separate as requested) -->
    <link rel="stylesheet" href="/../../assets/css/header.css">
    
    <!-- Custom Styles for this page -->
    <style>
        :root {
            --primary-color: #ffffff;
            --secondary-color: #213638;
            --accent: #E5A13E;
            --secondary-accent: #CFE7E5;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --muted-color: gainsboro;
            --danger: #dc3545;
            --success: #198754;
        }

        body {
            background-color: var(--muted-color);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .header + main {
            margin-top: 6rem;
        }

        .package-card {
            background: var(--primary-color);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .package-header {
            background: linear-gradient(135deg, var(--secondary-color), #1a2a2c);
            color: white;
            padding: 1.5rem;
        }

        .spots-list {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: .5rem;
            margin: 1rem 0;
        }

        .form-section {
            background: var(--primary-color);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .companion-row {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: .75rem;
            margin-bottom: .75rem;
            border: 1px solid #dee2e6;
        }

        .btn-accent {
            background-color: var(--accent);
            color: var(--secondary-color);
            font-weight: 600;
        }

        .btn-accent:hover {
            background-color: #d4922c;
            color: white;
        }

        .text-accent {
            color: var(--accent);
        }

        .form-control, .form-select {
            border-radius: .5rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: none;
            border-radius: .75rem;
        }

        #overlapWarning {
            margin-top: .5rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 1.5rem;
            }
            .companion-row .col-md-5,
            .companion-row .col-md-5 {
                margin-bottom: .5rem;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Tourismo Zamboanga</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="booking.php">My Booking</a></li>

                    <!-- Notification Item - CORRECTED -->
                    <li class="nav-item dropdown position-relative">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" 
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill d-none d-lg-inline-block"></i>
                        <span class="d-lg-none">Notifications</span>

                        <?php 
                        $notifications = $activityObj->touristNotification($account_ID);
                        $unread_count = 0;
                        foreach ($notifications as $n) {
                            if ((int)$n['is_viewed'] === 0) $unread_count++;
                        }
                        $badge_display = $unread_count > 0 ? '' : 'd-none';
                        ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?= $badge_display ?>"
                            style="font-size: 0.65rem;">
                            <?= $unread_count ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>

                    <!-- Dropdown Menu -->
                    <ul id="notification-dropdown" class="dropdown-menu dropdown-menu-end mt-2 shadow" style="width: 340px;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>

                        <?php if (empty($notifications)): ?>
                            <li><div class="dropdown-item text-center text-muted py-4">No notifications yet</div></li>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): 
                                $activity_ID = $notif['activity_ID'];
                                $isUnread = (int)$notif['is_viewed'] === 0;
                                $bg = $isUnread ? 'bg-light' : '';
                                $textWeight = $isUnread ? 'fw-bold' : '';
                            ?>
                                <li>
                                    <!-- AJAX call when clicked → marks as read instantly -->
                                    <a class="dropdown-item py-3 <?= $bg ?> mark-as-read" 
                                    href="javascript:void(0)"
                                    data-activity-id="<?= $activity_ID ?>"
                                    data-account-id="<?= $account_ID ?>">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <?php if (strpos($notif['action_name'], 'Booking') !== false): ?>
                                                    <i class="bi bi-calendar-check-fill text-primary"></i>
                                                <?php elseif (strpos($notif['action_name'], 'Payment') !== false): ?>
                                                    <i class="bi bi-credit-card-fill text-success"></i>
                                                <?php elseif (strpos($notif['action_name'], 'Message') !== false): ?>
                                                    <i class="bi bi-chat-dots-fill text-info"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-bell-fill text-secondary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="<?= $textWeight ?>"><?= htmlspecialchars($notif['activity_description']) ?></div>
                                                <div class="text-muted small"><?= $notif['action_name'] ?></div>
                                                <div class="text-muted small">
                                                    <?= date('M j, Y · g:i A', strtotime($notif['activity_timestamp'])) ?>
                                                </div>
                                            </div>
                                            <?php if ($isUnread): ?>
                                                <div class="flex-shrink-0">
                                                    <span class="badge bg-danger rounded-pill">New</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <li>
                            <a class="dropdown-item text-center text-primary fw-bold" href="notifications.php">
                                View all notifications
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Logout Button -->
                <a href="logout.php" class="btn btn-info ms-lg-3">Log out</a>
            </div>
        </div>
    </nav>
</header>

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h2 class="mb-4 text-center text-accent fw-bold">
                <i class="bi bi-calendar-check me-2"></i>Book Your Adventure
            </h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Package Details -->
            <div class="package-card mb-5">
                <div class="package-header">
                    <h3 class="mb-0"><?= htmlspecialchars($package['tourpackage_name']); ?></h3>
                    <small><i class="bi bi-person-fill"></i> Guide: <?= htmlspecialchars($guideName ?: 'Not Assigned') ?></small>
                </div>
                <div class="p-4">
                    <p class="lead"><?= htmlspecialchars($package['tourpackage_desc']); ?></p>

                    <div class="row text-center my-4">
                        <div class="col-md-4">
                            <strong><?= $package['schedule_days'] ?> Days</strong><br>
                            <small class="text-muted">Duration</small>
                        </div>
                        <div class="col-md-4">
                            <strong>₱<?= number_format($package['pricing_foradult'], 2) ?></strong><br>
                            <small class="text-muted">Per Adult</small>
                        </div>
                        <div class="col-md-4">
                            <strong><?= $package['numberofpeople_based'] ?>–<?= $package['numberofpeople_maximum'] ?> Pax</strong><br>
                            <small class="text-muted">Group Size</small>
                        </div>
                    </div>

                    <?php if (!empty($spots)): ?>
                        <div class="spots-list">
                            <h5><i class="bi bi-geo-alt-fill text-accent"></i> Tour Spots Included:</h5>
                            <ul class="list-unstyled">
                                <?php foreach ($spots as $spot): ?>
                                    <li class="mb-2">
                                        <strong><?= htmlspecialchars($spot['spots_name']); ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($spot['spots_description']); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="form-section">
                <form action="" method="post" id="bookingForm">

                    <!-- Booking Dates -->
                    <h4 class="mb-4 text-accent"><i class="bi bi-calendar3"></i> Select Travel Dates</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Start Date</label>
                            <input type="date" name="booking_start_date" id="booking_start_date" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">End Date <small class="text-muted">(Auto-calculated)</small></label>
                            <input type="date" name="booking_end_date" id="booking_end_date" class="form-control form-control-lg" readonly required>
                        </div>
                    </div>
                    <div id="overlapWarning" class="text-danger fw-bold" style="display:none;">
                        ⚠️ The guide is unavailable during these dates.
                    </div>

                    <hr class="my-5">

                    <!-- Self Inclusion -->
                    <h4 class="mb-4 text-accent"><i class="bi bi-person-check"></i> Are you joining the tour?</h4>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_selfIncluded" value="yes" id="selfYes" required>
                                <label class="form-check-label fw-bold" for="selfYes">Yes, include me</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_selfIncluded" value="no" id="selfNo" required>
                                <label class="form-check-label fw-bold" for="selfNo">No, only companions</label>
                            </div>
                        </div>
                    </div>

                    <!-- Companions -->
                    <h4 class="mb-3 text-accent"><i class="bi bi-people-fill"></i> Add Companions <small class="text-muted">(Optional)</small></h4>
                    <div id="inputContainer" class="mb-4"></div>
                    <button type="button" class="btn btn-outline-secondary mb-4" onclick="addInput()">
                        <i class="bi bi-person-plus"></i> Add Companion
                    </button>

                    <div class="text-end">
                        <a href="tour-packages-browse.php" class="btn btn-secondary me-3">← Back</a>
                        <button type="submit" class="btn btn-accent btn-lg px-5">
                            Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>

<script>
const maxPeople = <?= intval($package['numberofpeople_maximum']); ?>;
const minPeople = <?= intval($package['numberofpeople_based']); ?>;
const scheduleDays = <?= intval($package['schedule_days']); ?>;
const guideID = <?= intval($package['guide_ID']); ?>;
const inputContainer = document.getElementById('inputContainer');

if (maxPeople === 1) {
    document.querySelector('button[onclick="addInput()"]').style.display = 'none';
}

document.querySelectorAll('input[name="is_selfIncluded"]').forEach(radio => {
    radio.addEventListener('change', () => {
        if (maxPeople === 1 && radio.value === 'yes') {
            inputContainer.innerHTML = '';
        }
    });
});

function addInput() {
    const currentCount = inputContainer.children.length + (document.querySelector('input[value="yes"]:checked') ? 1 : 0);
    if (currentCount >= maxPeople) {
        alert(`Maximum ${maxPeople} people allowed.`);
        return;
    }

    const div = document.createElement('div');
    div.className = 'companion-row row g-3 align-items-end';
    div.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="companion_name[]" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="col-md-5">
            <select name="companion_category[]" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($bookingObj->getAllCompanionCategories() as $c): ?>
                    <option value="<?= $c['companion_category_ID'] ?>"><?= htmlspecialchars($c['companion_category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger w-100" onclick="this.parentElement.parentElement.remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    inputContainer.appendChild(div);
}

// Auto-set end date
document.getElementById('booking_start_date').addEventListener('change', function () {
    const start = new Date(this.value);
    if (!isNaN(start)) {
        start.setDate(start.getDate() + scheduleDays - 1);
        document.getElementById('booking_end_date').value = start.toISOString().split('T')[0];
        checkOverlap();
    }
});

// Check guide availability
async function checkOverlap() {
    const start = document.getElementById('booking_start_date').value;
    const end = document.getElementById('booking_end_date').value;
    const warning = document.getElementById('overlapWarning');

    if (!start || !end) return;

    try {
        const res = await fetch('booking-overlap.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ guide_ID: guideID, start_date: start, end_date: end })
        });
        const data = await res.json();
        warning.style.display = data.overlap ? 'block' : 'none';
    } catch (err) {
        console.error(err);
    }
}

document.getElementById('booking_start_date').addEventListener('change', checkOverlap);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>