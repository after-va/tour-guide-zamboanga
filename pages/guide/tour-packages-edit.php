<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tour Guide') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended'){
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending'){
    header('Location: account-pending.php');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

require_once "../../classes/guide.php";
require_once "../../classes/tour-manager.php";

$tourMgrObj = new TourManager();
$guideObj   = new Guide();

$guide_ID = $guideObj->getGuide_ID($_SESSION['user']['account_ID']);
$spots    = $guideObj->getAllSpots();

$tourpackage_ID = intval($_GET['id']);

// --- Load Related Data ---
$tourpackage       = $tourMgrObj->getTourPackageByID($tourpackage_ID);
$schedule          = $tourMgrObj->getScheduleByID($tourpackage['schedule_ID']);
$numberofpeople    = $tourMgrObj->getPeopleByID($schedule['numberofpeople_ID']);
$pricing           = $tourMgrObj->getPricingByID($numberofpeople['pricing_ID']);
$tourpackage_spots = $tourMgrObj->getSpotsByPackageID($tourpackage_ID);
print_r($tourMgrObj->getSpotsByPackageID($tourpackage_ID));


/* -------------------------------------------------
   Flash Data
   ------------------------------------------------- */
$old      = $_SESSION['old_input'] ?? [];
$errors   = $_SESSION['errors']   ?? [];
$success  = $_SESSION['success']  ?? '';

unset($_SESSION['old_input'], $_SESSION['errors'], $_SESSION['success']);

/* -------------------------------------------------
   Populate fields from DB
   ------------------------------------------------- */
$pkg = [
    'tourpackage_name'       => $old['tourpackage_name']       ?? $tourpackage['tourpackage_name'] ?? '',
    'tourpackage_desc'       => $old['tourpackage_desc']       ?? $tourpackage['tourpackage_desc'] ?? '',
    'schedule_days'          => $old['schedule_days']          ?? $schedule['schedule_days'] ?? 1,
    'numberofpeople_maximum' => $old['numberofpeople_maximum'] ?? $numberofpeople['numberofpeople_maximum'] ?? '',
    'numberofpeople_based'   => $old['numberofpeople_based']   ?? $numberofpeople['numberofpeople_based'] ?? '',
    'pricing_foradult'       => $old['pricing_foradult']       ?? $pricing['pricing_foradult'] ?? '',
    'pricing_forchild'       => $old['pricing_forchild']       ?? $pricing['pricing_forchild'] ?? '',
    'pricing_foryoungadult'  => $old['pricing_foryoungadult']  ?? $pricing['pricing_foryoungadult'] ?? '',
    'pricing_forsenior'      => $old['pricing_forsenior']      ?? $pricing['pricing_forsenior'] ?? '',
    'pricing_forpwd'         => $old['pricing_forpwd']         ?? $pricing['pricing_forpwd'] ?? '',
    'include_meal'           => $old['include_meal']           ?? $pricing['include_meal'] ?? 0,
    'meal_fee'               => $old['meal_fee']               ?? $pricing['meal_fee'] ?? '0.00',
    'transport_fee'          => $old['transport_fee']          ?? $pricing['transport_fee'] ?? '0.00',
    'discount'               => $old['discount']               ?? $pricing['discount'] ?? '0.00',
];

/* -------------------------------------------------
   Form Submission (same as before)
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrfToken, $_POST['csrf_token'] ?? '')) {
        $errors['general'] = 'Invalid CSRF token. Please try again.';
    } else {

        $posted = [
            'tourpackage_name'       => trim($_POST['tourpackage_name'] ?? ''),
            'tourpackage_desc'       => trim($_POST['tourpackage_desc'] ?? ''),
            'schedule_days'          => $_POST['schedule_days'] ?? '',
            'numberofpeople_maximum' => $_POST['numberofpeople_maximum'] ?? '',
            'numberofpeople_based'   => $_POST['numberofpeople_based'] ?? '',
            'pricing_foradult'       => $_POST['pricing_foradult'] ?? '',
            'pricing_forchild'       => $_POST['pricing_forchild'] ?? '',
            'pricing_foryoungadult'  => $_POST['pricing_foryoungadult'] ?? '',
            'pricing_forsenior'      => $_POST['pricing_forsenior'] ?? '',
            'pricing_forpwd'         => $_POST['pricing_forpwd'] ?? '',
            'include_meal'           => isset($_POST['include_meal']) ? 1 : 0,
            'meal_fee'               => $_POST['meal_fee'] ?? '0.00',
            'transport_fee'          => $_POST['transport_fee'] ?? '0.00',
            'discount'               => $_POST['discount'] ?? '0.00',
            'itinerary'              => $_POST['itinerary'] ?? [],
        ];

        // Validation Helper
        $v = new class {
            public $errors = [];
            public function required($val, $name) {
                if ($val === '') $this->errors[$name] = ucfirst(str_replace('_', ' ', $name)) . ' is required.';
            }
            public function numeric($val, $name, $min = null) {
                if (!is_numeric($val) || $val < 0) {
                    $this->errors[$name] = ucfirst(str_replace('_', ' ', $name)) . ' must be a positive number.';
                } elseif ($min !== null && $val < $min) {
                    $this->errors[$name] = ucfirst(str_replace('_', ' ', $name)) . " must be at least $min.";
                }
            }
        };

        // Basic Fields
        $v->required($posted['tourpackage_name'], 'tourpackage_name');
        $v->required($posted['tourpackage_desc'], 'tourpackage_desc');
        $v->required($posted['schedule_days'], 'schedule_days');
        $v->numeric($posted['schedule_days'], 'schedule_days', 1);

        $v->required($posted['numberofpeople_maximum'], 'numberofpeople_maximum');
        $v->numeric($posted['numberofpeople_maximum'], 'numberofpeople_maximum', 1);
        $v->required($posted['numberofpeople_based'], 'numberofpeople_based');
        $v->numeric($posted['numberofpeople_based'], 'numberofpeople_based', 1);

        $v->required($posted['pricing_foradult'], 'pricing_foradult');
        $v->numeric($posted['pricing_foradult'], 'pricing_foradult', 0);

        if ($posted['include_meal']) {
            $v->numeric($posted['meal_fee'], 'meal_fee', 0);
        }
        $v->numeric($posted['transport_fee'], 'transport_fee', 0);
        $v->numeric($posted['discount'], 'discount', 0);

        // Itinerary Validation
        $itinerary = $posted['itinerary'];
        if (!is_array($itinerary) || empty($itinerary)) {
            $v->errors['itinerary'] = 'At least one itinerary item is required.';
        } else {
            foreach ($itinerary as $idx => $item) {
                $spot     = $item['spot'] ?? '';
                $activity = trim($item['activity_name'] ?? '');
                $day      = $item['day'] ?? '';
                $start    = $item['start_time'] ?? '';
                $end      = $item['end_time'] ?? '';

                if ($spot === '' && $activity === '') {
                    $v->errors["itinerary_$idx"] = "Row " . ($idx + 1) . ": Select a spot or enter an activity.";
                }
                if ($day === '' || !ctype_digit((string)$day) || $day < 1 || $day > $posted['schedule_days']) {
                    $v->errors["itinerary_day_$idx"] = "Row " . ($idx + 1) . ": Invalid day.";
                }
                if ($start && $end) {
                    // Convert times to seconds for easier comparison
                    $startSec = strtotime($start);
                    $endSec   = strtotime($end);

                    // 1️⃣ Must not cross midnight
                    if ($endSec <= $startSec) {
                        $v->errors["itinerary_time_$idx"] = "Row " . ($idx + 1) . ": Activity cannot go past midnight.";
                    }

                    // 2️⃣ Check for overlapping activities on the same day
                    foreach ($itinerary as $j => $other) {
                        if ($j === $idx) continue; // Skip same activity

                        if (($other['day'] ?? '') == $day && !empty($other['start_time']) && !empty($other['end_time'])) {
                            $otherStart = strtotime($other['start_time']);
                            $otherEnd   = strtotime($other['end_time']);

                            // Overlap condition
                            if ($startSec < $otherEnd && $endSec > $otherStart) {
                                $v->errors["itinerary_overlap_$idx"] =
                                    "Row " . ($idx + 1) . ": Time overlaps with Row " . ($j + 1) . " on Day $day.";
                                break;
                            }
                        }
                    }
                }

            }
        }

        $errors = $v->errors;
    
        if (empty($errors)) {
            $tour_spots = $activities = $startTimes = $endTimes = $days = $packagespots_id = [];

                foreach ($itinerary as $item) {
                    // These come from hidden inputs in your form (one per existing spot)
                    $packagespots_id[] = $item['packagespot_ID'] ?? null;

                    $tour_spots[]   = $item['spot'] === '' ? null : $item['spot'];
                    $activities[]   = trim($item['activity_name'] ?? '');
                    $startTimes[]   = $item['start_time'] ?? null;
                    $endTimes[]     = $item['end_time'] ?? null;
                    $days[]         = $item['day'] ?? null;
                }

                //($packagespot_ID, $tour_spots, $packagespots_activityname, $packagespots_starttime, $packagespots_endtime, $packagespot_day, $tourpackage_ID, $guide_ID, $name, $desc, $schedule_ID, $days, $numberofpeople_ID, $numberofpeople_maximum, $numberofpeople_based, $pricing_ID, $currency, $forAdult, $forChild, $forYoungAdult, $forSenior, $forPWD, $includeMeal, $mealFee, $transportFee, $discount)
                $result = $tourMgrObj->updateTourPackagesAndItsSpots(
                    $packagespots_id, $tour_spots, $activities, $startTimes, $endTimes, $days,
                    $tourpackage_ID,  $guide_ID, $posted['tourpackage_name'], $posted['tourpackage_desc'],
                    $schedule['schedule_ID'], $posted['schedule_days'],
                    $numberofpeople['numberofpeople_ID'], $posted['numberofpeople_maximum'], $posted['numberofpeople_based'],
                    $pricing['pricing_ID'], 'PHP', $posted['pricing_foradult'], $posted['pricing_forchild'] ?? 0, $posted['pricing_foryoungadult'] ?? 0, $posted['pricing_forsenior'] ?? 0, $posted['pricing_forpwd'] ?? 0, $posted['include_meal'], $posted['meal_fee'], $posted['transport_fee'], $posted['discount'] );

                if ($result) {
                    $_SESSION['success'] = 'Tour package updated successfully!';
                    header('Location: tour-packages.php');
                    exit;
                } else {
                    $errors['general'] = 'Failed to update package. Check server logs.';
                    error_log('TourManager update error: ' . print_r($tourMgrObj->getLastError(), true));
                }
        }
    }

    $_SESSION['old_input'] = $posted;
    $pkg = $posted;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour Package</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; font-size: 0.9em; }
        .success { color: green; font-weight: bold; }
        .itinerary-item { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: #f9f9f9; position: relative; }
        .remove-btn { position: absolute; top: 5px; right: 5px; background: #e74c3c; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; font-weight: bold; }
        label { display: inline-block; width: 180px; font-weight: bold; }
        input, select, textarea { margin-bottom: 10px; padding: 5px; width: 300px; }
        textarea { height: 80px; }
        button { margin-top: 10px; padding: 8px 16px; }
        h3 { margin-top: 20px; }
    </style>
</head>
<body>
<h1>Edit Tour Package</h1>

<?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
<?php if (!empty($errors['general'])): ?><p class="error"><?= htmlspecialchars($errors['general']) ?></p><?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <label>Tour Package Name:</label>
    <input type="text" name="tourpackage_name" value="<?= htmlspecialchars($pkg['tourpackage_name']) ?>"><br>

    <label>Description:</label><br>
    <textarea name="tourpackage_desc"><?= htmlspecialchars($pkg['tourpackage_desc']) ?></textarea><br>

    <label>Schedule Days:</label>
    <input type="number" id="schedule_days" name="schedule_days" min="1" value="<?= htmlspecialchars($pkg['schedule_days']) ?>"><br>

    <label>Maximum People:</label>
    <input type="number" name="numberofpeople_maximum" min="1" value="<?= htmlspecialchars($pkg['numberofpeople_maximum']) ?>"><br>

    <label>Minimum People:</label>
    <input type="number" name="numberofpeople_based" min="1" value="<?= htmlspecialchars($pkg['numberofpeople_based']) ?>"><br>

    <h3>Pricing (PHP)</h3>
    <label>Adult:</label>
    <input type="number" step="0.01" name="pricing_foradult" value="<?= htmlspecialchars($pkg['pricing_foradult']) ?>"><br>

    <label>Child:</label>
    <input type="number" step="0.01" name="pricing_forchild" value="<?= htmlspecialchars($pkg['pricing_forchild']) ?>"><br>

    <label>Young Adult:</label>
    <input type="number" step="0.01" name="pricing_foryoungadult" value="<?= htmlspecialchars($pkg['pricing_foryoungadult']) ?>"><br>

    <label>Senior:</label>
    <input type="number" step="0.01" name="pricing_forsenior" value="<?= htmlspecialchars($pkg['pricing_forsenior']) ?>"><br>

    <label>PWD:</label>
    <input type="number" step="0.01" name="pricing_forpwd" value="<?= htmlspecialchars($pkg['pricing_forpwd']) ?>"><br>

    <label>
        <input type="checkbox" name="include_meal" value="1" <?= $pkg['include_meal'] ? 'checked' : '' ?>> Include Meal
    </label><br>

    <div id="mealFeeContainer" style="<?= $pkg['include_meal'] ? '' : 'display:none;' ?>">
        <label>Meal Fee:</label>
        <input type="number" step="0.01" name="meal_fee" value="<?= htmlspecialchars($pkg['meal_fee']) ?>"><br>
    </div>

    <label>Transport Fee:</label>
    <input type="number" step="0.01" name="transport_fee" value="<?= htmlspecialchars($pkg['transport_fee']) ?>"><br>

    <label>Discount:</label>
    <input type="number" step="0.01" name="discount" value="<?= htmlspecialchars($pkg['discount']) ?>"><br>

    <h3>Itinerary</h3>

    <div id="itinerary-container">
        <?php if (!empty($tourpackage_spots)): ?>
            <?php foreach ($tourpackage_spots as $idx => $spot): ?>
                <div class="itinerary-item">
                    <button type="button" class="remove-btn" onclick="removeItinerary(this)">X</button>

                    <label>Day:</label>
                    <select name="itinerary[<?= $idx ?>][day]">
                        <?php for ($d = 1; $d <= $pkg['schedule_days']; $d++): ?>
                            <option value="<?= $d ?>" <?= ($spot['packagespot_day'] ?? 1) == $d ? 'selected' : '' ?>>
                                <?= $d ?>
                            </option>
                        <?php endfor; ?>
                    </select><br>


                    <label>Spot:</label>
                    <select name="itinerary[<?= $idx ?>][spot]" onchange="toggleActivity(this)">
                        <option value="">-- None / Custom Activity --</option>
                        <?php foreach ($spots as $s): ?>
                            <option value="<?= htmlspecialchars($s['spots_ID']) ?>" <?= $spot['spots_ID'] == $s['spots_ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['spots_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <label>Activity Name:</label>
                        <input type="text" name="itinerary[<?= $idx ?>][activity_name]"
                            value="<?= htmlspecialchars($spot['packagespot_activityname'] ?? '') ?>"><br>

                        <label>Start Time:</label>
                        <input type="time" name="itinerary[<?= $idx ?>][start_time]"
                            value="<?= htmlspecialchars($spot['packagespot_starttime'] ?? '') ?>"><br>

                        <label>End Time:</label>
                        <input type="time" name="itinerary[<?= $idx ?>][end_time]"
                            value="<?= htmlspecialchars($spot['packagespot_endtime'] ?? '') ?>"><br>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No itinerary data found for this package.</p>
        <?php endif; ?>
        
    </div>

    <button type="button" onclick="addItinerary()">+ Add Another Stop/Activity</button><br><br>
    <button type="submit">Save Changes</button>
</form>

<script>
    // const daysInput = document.getElementById('schedule_days');
    // function syncAllDayDropdowns() {
    //     const max = parseInt(daysInput.value, 10) || 1;
    //     document.querySelectorAll('.day-dropdown').forEach(sel => fillDayOptions(sel, max));
    // }

    function toggleActivity(select) {
        const actInput = select.closest('.itinerary-item').querySelector('input[name$="[activity_name]"]');
        actInput.style.display = select.value === '' ? '' : 'none';
    }

    function removeItinerary(btn) {
        btn.closest('.itinerary-item').remove();
    }

    function updateItineraryDayDropdowns() {
        const scheduleDays = parseInt(document.getElementById('schedule_days').value) || 1;
        document.querySelectorAll('.itinerary-item select[name$="[day]"]').forEach(sel => {
            const currentValue = sel.value;
            sel.innerHTML = ''; // Clear existing options
            for (let d = 1; d <= scheduleDays; d++) {
                const option = document.createElement('option');
                option.value = d;
                option.textContent = d;
                if (currentValue == d) option.selected = true;
                sel.appendChild(option);
            }
        });
    }

    // Listen for changes in Schedule Days input
    document.getElementById('schedule_days').addEventListener('input', updateItineraryDayDropdowns);

    function addItinerary() {
        const container = document.getElementById('itinerary-container');
        const idx = container.children.length;
        const scheduleDays = <?= (int)$pkg['schedule_days'] ?>; // get schedule_days from PHP
        let dayOptions = '';
        for (let d = 1; d <= scheduleDays; d++) {
            dayOptions += `<option value="${d}">${d}</option>`;
        }

        const div = document.createElement('div');
        div.classList.add('itinerary-item');
        div.innerHTML = `
            <button type="button" class="remove-btn" onclick="removeItinerary(this)">X</button>

            <label>Day:</label>
            <select name="itinerary[${idx}][day]">${dayOptions}</select><br>

            <label>Spot:</label>
            <select name="itinerary[${idx}][spot]" onchange="toggleActivity(this)">
                <option value="">-- None / Custom Activity --</option>
                <?php foreach ($spots as $s): ?>
                    <option value="<?= htmlspecialchars($s['spots_ID']) ?>"><?= htmlspecialchars($s['spots_name']) ?></option>
                <?php endforeach; ?>
            </select><br>

            <label>Activity Name:</label>
            <input type="text" name="itinerary[${idx}][activity_name]" placeholder="e.g. Sightseeing"><br>

            <label>Start Time:</label>
            <input type="time" name="itinerary[${idx}][start_time]"><br>

            <label>End Time:</label>
            <input type="time" name="itinerary[${idx}][end_time]"><br>

            <input type="hidden" name="itinerary[${idx}][packagespot_ID]" value="">
        `;
        container.appendChild(div);
    }

    // IDK 
    function parseTime(time) {
        if (!time) return null;
        const [h, m] = time.split(':').map(Number);
        return h * 60 + m; // minutes from midnight
    }

    function validateItineraryRealtime() {
        const items = document.querySelectorAll('.itinerary-item');
        const activities = [];
        const spotTracker = new Map(); // {spotID -> [indexes]}
        
        // Clear previous highlights & errors
        items.forEach(item => {
            item.style.borderColor = "#ccc";
            item.style.backgroundColor = "#f9f9f9";
            const err = item.querySelector('.time-error');
            if (err) err.remove();
        });

        // Collect all item info
        items.forEach((item, i) => {
            const day = parseInt(item.querySelector('select[name$="[day]"]')?.value);
            const start = item.querySelector('input[name$="[start_time]"]')?.value;
            const end = item.querySelector('input[name$="[end_time]"]')?.value;
            const spot = item.querySelector('select[name$="[spot]"]')?.value || null;
            const startMins = parseTime(start);
            const endMins = parseTime(end);

            // Track spots for duplicate detection
            if (spot && spot !== "") {
                if (!spotTracker.has(spot)) spotTracker.set(spot, []);
                spotTracker.get(spot).push(i);
            }

            activities.push({ item, i, day, startMins, endMins, start, end });
        });

        // Helper for inline error message
        const showError = (item, msg) => {
            item.style.borderColor = "#e74c3c";
            item.style.backgroundColor = "#fff5f5";
            if (!item.querySelector('.time-error')) {
                const div = document.createElement('div');
                div.className = 'time-error';
                div.style.color = "red";
                div.style.fontSize = "0.9em";
                div.style.marginTop = "5px";
                div.textContent = msg;
                item.appendChild(div);
            }
        };

        // 1️⃣ Check invalid times
        activities.forEach(a => {
            if (a.start && a.end && a.endMins <= a.startMins) {
                showError(a.item, "Activity cannot go past midnight or end before it starts.");
            }
        });

        // 2️⃣ Check overlaps on same day
        for (let i = 0; i < activities.length; i++) {
            for (let j = i + 1; j < activities.length; j++) {
                const a = activities[i], b = activities[j];
                if (a.day && b.day && a.day === b.day &&
                    a.startMins !== null && a.endMins !== null &&
                    b.startMins !== null && b.endMins !== null) {

                    if (a.startMins < b.endMins && a.endMins > b.startMins) {
                        showError(a.item, `Overlaps with Row ${b.i + 1} on Day ${a.day}.`);
                        showError(b.item, `Overlaps with Row ${a.i + 1} on Day ${a.day}.`);
                    }
                }
            }
        }

        // 3️⃣ Check duplicate spots
        for (const [spotID, indexes] of spotTracker.entries()) {
            if (indexes.length > 1) {
                indexes.forEach(i => {
                    const item = items[i];
                    showError(item, "This spot is already chosen in another itinerary item.");
                });
            }
        }
    }

    // 🧩 Auto-run validation when user edits any field
    document.addEventListener('input', (e) => {
        if (
            e.target.matches('input[name$="[start_time]"], input[name$="[end_time]"], select[name$="[day]"], select[name$="[spot]"]')
        ) {
            validateItineraryRealtime();
        }
    });

    // 🧩 Auto revalidate when itinerary items are added or removed
    const container = document.getElementById('itinerary-container');
    const observer = new MutationObserver(() => {
        setTimeout(validateItineraryRealtime, 50);
    });
    observer.observe(container, { childList: true });

    // Run validation on page load
    document.addEventListener('DOMContentLoaded', validateItineraryRealtime);

    // Final check on submit
    document.querySelector('form').addEventListener('submit', function (e) {
        validateItineraryRealtime();
        if (document.querySelector('.time-error')) {
            alert("Please fix all conflicts before submitting.");
            e.preventDefault();
        }
    });

// Validation when it itinerary doesnt have a activty/spots AND start AND end Time 
    document.querySelector('form').addEventListener('submit', function (e) {
        // 🧹 1️⃣ Remove completely empty itinerary rows
        document.querySelectorAll('.itinerary-item').forEach(item => {
            const spot = item.querySelector('select[name$="[spot]"]')?.value.trim();
            const activity = item.querySelector('input[name$="[activity_name]"]')?.value.trim();
            const start = item.querySelector('input[name$="[start_time]"]')?.value.trim();
            const end = item.querySelector('input[name$="[end_time]"]')?.value.trim();

            // If no spot, no activity, no start & no end → remove this block
            if (!spot && !activity && !start && !end) {
                item.remove();
            }
        });

        // 🧩 2️⃣ Run your normal validation after cleanup
        validateItineraryRealtime();

        // 🧩 3️⃣ Prevent submit if errors exist
        if (document.querySelector('.time-error')) {
            alert("Please fix all conflicts before submitting.");
            e.preventDefault();
        }
    });

</script>
</body>
</html>
