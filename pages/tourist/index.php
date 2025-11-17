<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended') {
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending') {
    header('Location: account-pending.php');
    exit;
}

$tourist_ID = $_SESSION['account_ID'];
$account_ID =  $_SESSION['account_ID'];
require_once "../../classes/tourist.php";
require_once "../../classes/tour-manager.php";
require_once "../../classes/booking.php";
require_once "../../classes/activity-log.php";

$activityObj = new ActivityLogs();
$notification = $activityObj->touristNotification($tourist_ID);

$bookingObj = new Booking();
$updateBookings = $bookingObj->updateBookings();

$TourManagerObj = new TourManager();
$packages = $TourManagerObj->viewAllPackages();
$packageCategory = $TourManagerObj->getTourSpotsCategory();

/* -------------------------------------------------
   AJAX: Return only filtered cards
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ajax'])) {
    // Properly handle categories[] from checkboxes
    $rawCategories = isset($_POST['categories']) ? $_POST['categories'] : [];

    // Ensure it's always an array
    if (!is_array($rawCategories)) {
        $rawCategories = $rawCategories === '' ? [] : [$rawCategories];
    }

    // Clean categories - trim whitespace but keep original case
    $categories = array_filter(array_map('trim', $rawCategories));
    $categories = array_values($categories); // reindex

    // Debug logging
    error_log("Categories received: " . print_r($categories, true));

    $filters = [
        'categories' => $categories,
        'price_min'  => isset($_POST['price_min']) && $_POST['price_min'] !== '' ? $_POST['price_min'] : null,
        'price_max'  => isset($_POST['price_max']) && $_POST['price_max'] !== '' ? $_POST['price_max'] : null,
        'minPax'     => isset($_POST['minPax']) && $_POST['minPax'] !== '' ? $_POST['minPax'] : null,
        'maxPax'     => isset($_POST['maxPax']) && $_POST['maxPax'] !== '' ? $_POST['maxPax'] : null,
    ];

    $packages = $TourManagerObj->filterPackages($filters);

    if (empty($packages)) {
        echo '<div class="w-100 text-center py-5 text-muted">
                <i class="bi bi-emoji-frown fs-1"></i>
                <p class="mt-3">No packages match the selected filters.</p>
              </div>';
        exit;
    }

    foreach ($packages as $package) {
        $schedule = $TourManagerObj->getScheduleByID($package['schedule_ID']);
        $people   = $TourManagerObj->getPeopleByID($schedule['numberofpeople_ID']);
        $pricing  = $TourManagerObj->getPricingByID($people['pricing_ID']);
        
        // Handle rating errors gracefully
        try {
            $rating = $TourManagerObj->getTourPackagesRating($package['tourpackage_ID']);
            $avg    = $rating['avg'] ?? 0;
            $count  = $rating['count'] ?? 0;
        } catch (Exception $e) {
            $avg   = 0;
            $count = 0;
        }
        
        include 'card-template.php';
    }
    exit;
}

function buildStarList(float $avg, int $count): string
{
    $full  = (int)floor($avg);
    $half  = ($avg - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    $html = str_repeat('<li class="list-inline-item me-0"><i class="fas fa-star text-warning fa-xs"></i></li>', $full);
    $html .= $half ? '<li class="list-inline-item me-0"><i class="fas fa-star-half-alt text-warning fa-xs"></i></li>' : '';
    $html .= str_repeat('<li class="list-inline-item me-0"><i class="far fa-star text-warning fa-xs"></i></li>', $empty);
    $html .= '<li class="list-inline-item"><small class="text-muted">'.number_format($avg,1).' ('.$count.')</small></li>';

    return $html;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourismo Zamboanga</title>

    <link rel="stylesheet" href="/../../assets/css/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/tourist/index.css">
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

<!-- Mobile Filter Toggle -->
<button class="filter-toggle btn btn-warning d-md-none position-fixed bottom-0 start-0 m-3 shadow-lg rounded-circle p-0 d-flex align-items-center justify-content-center"
        id="filterToggle" style="width: 3rem; height: 3rem; z-index: 1050;">
    <i class="bi bi-funnel-fill fs-4"></i>
</button>
<div class="filter-overlay d-md-none"></div>

<aside id="filterSidebar" class="aside-tourist p-3 bg-light border rounded shadow-sm">
    <form id="filterForm" onsubmit="return false;">
        <h4 class="text-dark mb-3 border-bottom pb-2"><i class="bi bi-funnel-fill"></i> Filters</h4>

        <!-- Categories -->
        <div class="mb-4">
            <h6 class="fw-bold mb-2">Categories</h6>
            <?php foreach ($packageCategory as $p):
                if (empty($p['spots_category'])) continue;
                $category = htmlspecialchars($p['spots_category']);
                $id = 'cat_' . preg_replace('/[^a-z0-9]+/', '_', strtolower($category));
            ?>
                <div class="form-check">
                    <input class="form-check-input category-checkbox" type="checkbox" id="<?= $id ?>" name="categories[]" value="<?= $category ?>">
                    <label class="form-check-label" for="<?= $id ?>"><?= $category ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Price: Dual Slider -->
        <div class="mb-4">
            <h6 class="fw-bold mb-2">Price (per adult)</h6>
            <div class="row g-2 mb-2">
                <div class="col">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="price_min" id="priceMinValue" class="form-control" min="500" max="10000" step="500" value="500">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="price_max" id="priceMaxValue" class="form-control" min="500" max="10000" step="500" value="10000">
                    </div>
                </div>
            </div>

            <div class="range-slider">
                <input type="range" class="range-thumb range-thumb--lower" id="priceMinRange" min="500" max="10000" step="500" value="500">
                <input type="range" class="range-thumb range-thumb--higher" id="priceMaxRange" min="500" max="10000" step="500" value="10000">
                <div class="range-slider__track"></div>
                <div class="range-slider__fill"></div>
            </div>

            <div class="d-flex justify-content-between mt-1 small text-muted">
                <span id="priceMinLabel">₱500</span>
                <span id="priceMaxLabel">₱10,000</span>
            </div>
        </div>

        <!-- PAX -->
        <div class="mb-4">
            <h6 class="fw-bold mb-2">PAX</h6>
            <div class="row g-2">
                <div class="col">
                    <label class="form-label small text-muted">Min</label>
                    <input type="number" class="form-control" id="minPax" name="minPax" min="1" placeholder="1">
                </div>
                <div class="col">
                    <label class="form-label small text-muted">Max</label>
                    <input type="number" class="form-control" id="maxPax" name="maxPax" min="1" placeholder="50">
                </div>
            </div>
        </div>
    </form>
</aside>

<main id="packagesContainer" class="main-contents row">
    <?php foreach ($packages as $package): ?>
        <?php
        $schedule = $TourManagerObj->getScheduleByID($package['schedule_ID']);
        $people   = $TourManagerObj->getPeopleByID($schedule['numberofpeople_ID']);
        $pricing  = $TourManagerObj->getPricingByID($people['pricing_ID']);
        
        // Handle rating errors gracefully
        try {
            $rating = $TourManagerObj->getTourPackagesRating($package['tourpackage_ID']);
            $avg    = $rating['avg'] ?? 0;
            $count  = $rating['count'] ?? 0;
        } catch (Exception $e) {
            $avg   = 0;
            $count = 0;
        }
        ?>
        <?php include 'card-template.php'; ?>
    <?php endforeach; ?>
</main>
<p><?= $updateBookings['message'] ?></p>
<script>
// === Dual Price Slider ===
const priceMinValue = document.getElementById('priceMinValue');
const priceMaxValue = document.getElementById('priceMaxValue');
const priceMinRange = document.getElementById('priceMinRange');
const priceMaxRange = document.getElementById('priceMaxRange');
const priceMinLabel = document.getElementById('priceMinLabel');
const priceMaxLabel = document.getElementById('priceMaxLabel');
const fill = document.querySelector('.range-slider__fill');

function updateSlider() {
    let min = parseInt(priceMinRange.value);
    let max = parseInt(priceMaxRange.value);

    if (min > max) {
        [min, max] = [max, min];
        priceMinRange.value = min;
        priceMaxRange.value = max;
    }

    priceMinValue.value = min;
    priceMaxValue.value = max;
    priceMinLabel.textContent = `₱${min.toLocaleString()}`;
    priceMaxLabel.textContent = `₱${max.toLocaleString()}`;

    const percentMin = ((min - 500) / 9500) * 100;
    const percentMax = ((max - 500) / 9500) * 100;
    fill.style.left = `${percentMin}%`;
    fill.style.right = `${100 - percentMax}%`;
}
updateSlider();

// === Real-time Filter ===
const form = document.getElementById('filterForm');
const container = document.getElementById('packagesContainer');
let debounceTimer;

function sendFilter() {
    const formData = new FormData(form);
    formData.append('ajax', '1');

    // Debug: Log what's being sent
    console.log('Filter data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    container.innerHTML = `<div class="w-100 text-center py-5"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

    fetch(location.href, { method: 'POST', body: formData })
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            console.log('Filter applied successfully');
        })
        .catch(err => {
            console.error('Filter error:', err);
            container.innerHTML = '<div class="text-danger">Error loading packages.</div>';
        });
}

// Trigger on checkbox change
const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
categoryCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        console.log('Category changed:', checkbox.value, checkbox.checked);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(sendFilter, 300);
    });
});

// Trigger on other form changes
form.addEventListener('change', (e) => {
    // Only trigger if it's not a category checkbox (already handled above)
    if (!e.target.classList.contains('category-checkbox')) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(sendFilter, 300);
    }
});

// Handle slider and number inputs
['priceMinRange', 'priceMaxRange', 'priceMinValue', 'priceMaxValue', 'minPax', 'maxPax'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', () => {
            updateSlider();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(sendFilter, 300);
        });
    }
});

// Mobile Sidebar
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("filterToggle");
    const sidebar = document.getElementById("filterSidebar");
    const overlay = document.querySelector(".filter-overlay");

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener("click", () => {
            sidebar.classList.add("active");
            overlay.classList.add("active");
        });
        overlay.addEventListener("click", () => {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>