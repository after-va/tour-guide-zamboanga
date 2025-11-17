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

require_once "../../classes/tourist.php";
require_once "../../classes/payment-manager.php";
require_once "../../classes/booking.php";
require_once "../../classes/activity-log.php";


$tourist_ID = $_SESSION['user']['account_ID'];
$account_ID = $tourist_ID;
$booking_ID = $_GET['id'] ?? null;

$activityObj = new ActivityLogs();
$notification = $activityObj->touristNotification($tourist_ID);
if (!$booking_ID || !is_numeric($booking_ID)) {
    die("Invalid booking ID.");
} 

$touristObj = new Tourist();
$paymentObj = new PaymentManager();
$bookingObj = new Booking();

$hasPaymentTransaction = $paymentObj->hasPaymentTransaction($booking_ID);
if($hasPaymentTransaction < 0){
    header('Location: booking.php');
    exit;
}

$booking = $bookingObj->viewBookingByTouristANDBookingID($booking_ID);
$companions = $bookingObj->getCompanionsByBooking($booking_ID);
$companionBreakdown = $bookingObj->getCompanionBreakdown($booking_ID);

$mealFee = (float)($booking['pricing_mealfee'] ?? 0);
$transportFee = (float)($booking['transport_fee'] ?? 0);
$self_included = (int)($booking['booking_isselfincluded'] ?? 0);
$userCategory = $touristObj->getTouristCategory($tourist_ID);
$userPrice = $touristObj->getPricingOfTourist($userCategory, $booking_ID);
$totalNumberOfPeople = $self_included + count($companions);
$discount = (float)($booking['pricing_discount'] ?? 0);

$methodCategories = $paymentObj->viewAllPaymentMethodCategory();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $methodcategory_ID = $_POST['methodcategory_ID'] ?? null;
    $method_amount = $_POST['method_amount'] ?? 0;
    $method_currency = 'PHP';
    $method_cardnumber = $_POST['method_cardnumber'] ?? null;
    $method_expmonth = $_POST['method_expmonth'] ?? null;
    $method_expyear = $_POST['method_expyear'] ?? null;
    $method_cvc = $_POST['method_cvc'] ?? null;
    $method_name = trim($_POST['method_name'] ?? '');
    $method_email = trim($_POST['method_email'] ?? '');
    $method_line1 = trim($_POST['method_line1'] ?? '');
    $method_city = trim($_POST['method_city'] ?? '');
    $method_postalcode = trim($_POST['method_postalcode'] ?? '');
    $method_country = trim($_POST['method_country'] ?? '');
    $country_ID = $_POST['country_ID'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');
    $methodcategory_processing_fee = (float)($_POST['methodcategory_processing_fee'] ?? 0);

    $required_fields = [
        'methodcategory_ID', 'method_name', 'method_email', 'method_line1',
        'method_city', 'method_postalcode', 'method_country', 'country_ID', 'phone_number'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }

    if (empty($errors)) {
        try {
            $result = $paymentObj->addAllPaymentInfo(
                $booking_ID, $method_amount + $methodcategory_processing_fee,
                null, $methodcategory_ID, $method_amount, $method_currency,
                $method_cardnumber, $method_expmonth, $method_expyear, $method_cvc,
                $method_name, $method_email, $method_line1, $method_city,
                $method_postalcode, $method_country, $country_ID, $phone_number
            );

            if ($result) {
                $_SESSION['success'] = "Payment submitted successfully!";
                header("Location: booking.php");
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Payment failed: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Tourismo Zamboanga</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Your existing header CSS -->
    <link rel="stylesheet" href="/../../assets/css/header.css">

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

        .header + main { margin-top: 6rem; }

        .package-card, .payment-card, .breakdown-card {
            background: var(--primary-color);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .section-header {
            background: linear-gradient(135deg, var(--secondary-color), #1a2a2c);
            color: white;
            padding: 1.5rem;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .breakdown-table th {
            background: var(--secondary-accent);
            color: var(--secondary-color);
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
        }

        .breakdown-table td {
            padding: 0.65rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .breakdown-table .indent {
            padding-left: 2rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .total-row {
            font-size: 1.2rem;
            font-weight: bold;
            background: #f8f9fa !important;
        }

        .total-row td {
            padding: 1rem !important;
        }

        .btn-accent {
            background-color: var(--accent);
            color: var(--secondary-color);
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }

        .btn-accent:hover {
            background-color: #d4922c;
            color: white;
        }

        .text-accent { color: var(--accent); }

        .form-control, .form-select {
            border-radius: 0.6rem;
            padding: 0.75rem;
        }

        .payment-type-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.8rem;
            margin-top: 1rem;
            border: 1px solid #dee2e6;
        }

        .alert-danger {
            border-radius: 0.8rem;
            border: none;
        }

        @media (max-width: 768px) {
            .section-header { font-size: 1.2rem; padding: 1rem; }
            .breakdown-table { font-size: 0.9rem; }
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

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h2 class="text-center mb-5 text-accent fw-bold">
                <i class="bi bi-credit-card-2-front-fill fs-1"></i><br>
                Complete Your Payment
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

            <!-- Booking Summary -->
            <div class="package-card mb-4">
                <div class="section-header">
                    <i class="bi bi-info-circle"></i> Booking Summary
                </div>
                <div class="p-4">
                    <h4 class="fw-bold"><?= htmlspecialchars($booking['tourpackage_name']) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($booking['tourpackage_desc']) ?></p>
                    <div class="row g-4 mt-3">
                        <div class="col-md-6">
                            <strong><i class="bi bi-calendar-range text-accent"></i> Travel Dates</strong><br>
                            <?= date('M j, Y', strtotime($booking['booking_start_date'])) ?> → <?= date('M j, Y', strtotime($booking['booking_end_date'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="bi bi-people-fill text-accent"></i> Total Travelers</strong><br>
                            <?= $totalNumberOfPeople ?> person(s)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Breakdown -->
            <div class="breakdown-card mb-5">
                <div class="section-header">
                    <i class="bi bi-receipt"></i> Fee Breakdown
                </div>
                <div class="p-4">
                    <table class="breakdown-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="feeBreakdownBody"></tbody>
                        <tr class="total-row">
                            <td colspan="2"><strong>Grand Total (After Discount)</strong></td>
                            <td class="text-end" id="grandTotal">₱0.00</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="payment-card">
                <div class="section-header">
                    <i class="bi bi-shield-lock"></i> Secure Payment
                </div>
                <div class="p-4">
                    <form id="paymentForm" method="POST">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="methodcategory_ID" id="methodcategory_ID" class="form-select form-select-lg" required>
                                <option value="">-- Choose Payment Method --</option>
                                <?php foreach ($methodCategories as $category): ?>
                                    <option value="<?= $category['methodcategory_ID'] ?>"
                                            data-type="<?= htmlspecialchars(strtolower($category['methodcategory_type'])) ?>"
                                            data-fee="<?= $category['methodcategory_processing_fee'] ?>">
                                        <?= htmlspecialchars($category['methodcategory_name']) ?>
                                        <?php if ($category['methodcategory_processing_fee'] > 0): ?>
                                            (+₱<?= number_format($category['methodcategory_processing_fee'], 2) ?> fee)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Processing Fee</label>
                                <input type="text" id="methodcategory_processing_fee" class="form-control" readonly value="₱0.00">
                                <input type="hidden" name="methodcategory_processing_fee" id="hidden_processing_fee" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-5">Total Amount to Pay</label>
                                <input type="text" name="method_amount" id="method_amount" class="form-control form-control-lg fw-bold text-accent" readonly>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="method_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="method_email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Country Code</label>
                                <select name="country_ID" id="country_ID" class="form-select" required>
                                    <option value="">-- Code --</option>
                                    <?php foreach ($touristObj->fetchCountryCode() as $c): ?>
                                        <option value="<?= $c['country_ID'] ?>" <?= ($c['country_codenumber'] == '+63') ? 'selected' : '' ?>>
                                            <?= $c['country_name'] ?> (<?= $c['country_codenumber'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" maxlength="10" pattern="[0-9]{10}" required placeholder="9123456789">
                            </div>
                        </div>

                        <fieldset class="mt-4">
                            <legend class="fs-6 fw-bold">Billing Address</legend>
                            <div class="row g-3">
                                <div class="col-12"><input type="text" name="method_line1" class="form-control" placeholder="Street Address" required></div>
                                <div class="col-md-6"><input type="text" name="method_city" class="form-control" placeholder="City" required></div>
                                <div class="col-md-6"><input type="text" name="method_postalcode" class="form-control" placeholder="Postal Code" required></div>
                                <div class="col-12"><input type="text" name="method_country" class="form-control" placeholder="Country" value="Philippines" required></div>
                            </div>
                        </fieldset>

                        <!-- Card Details -->
                        <div id="cardSection" class="payment-type-section" style="display:none;">
                            <h5 class="mt-3"><i class="bi bi-credit-card"></i> Card Information</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="text" name="method_cardnumber" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="method_expmonth" class="form-control" placeholder="MM" maxlength="2">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="method_expyear" class="form-control" placeholder="YYYY" maxlength="4">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="method_cvc" class="form-control" placeholder="CVC" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer -->
                        <div id="bankSection" class="payment-type-section" style="display:none;">
                            <h5 class="mt-3"><i class="bi bi-bank"></i> Bank Transfer Details</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="text" name="bank_name" class="form-control" placeholder="Bank Name (e.g., BDO, BPI)">
                                </div>
                                <div class="col-12">
                                    <input type="text" name="bank_reference" class="form-control" placeholder="Reference Number">
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-5">
                            <a href="booking.php" class="btn btn-secondary me-3">← Back to Bookings</a>
                            <button type="submit" class="btn btn-accent btn-lg">
                                <i class="bi bi-lock-fill"></i> Confirm & Pay Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
// Fee calculation logic (same as before, just cleaner)
const bookingData = {
    companions: <?= json_encode($companionBreakdown) ?>,
    mealFee: <?= $mealFee ?>,
    transportFee: <?= $transportFee ?>,
    self_included: <?= $self_included ?>,
    userCategory: "<?= $userCategory ?>",
    userPrice: <?= $userPrice ?>,
    discount: <?= $discount ?>
};

function calculateFees() {
    let grandTotal = 0;
    const tbody = document.getElementById('feeBreakdownBody');
    tbody.innerHTML = '';

    const summary = {};
    ['Infant','Child','Young Adult','Adult','Senior','PWD'].forEach(c => summary[c] = {qty:0, total:0});

    if (bookingData.self_included) {
        summary[bookingData.userCategory].qty++;
        summary[bookingData.userCategory].total += parseFloat(bookingData.userPrice);
    }

    bookingData.companions.forEach(c => {
        const cat = c.category || 'Adult';
        summary[cat].qty += parseInt(c.qty);
        summary[cat].total += parseFloat(c.total);
    });

    for (const [cat, data] of Object.entries(summary)) {
        if (data.qty === 0) continue;

        const base = data.total;
        const meal = cat === 'Infant' ? 0 : bookingData.mealFee * data.qty;
        const transport = cat === 'Infant' ? 0 : (cat === 'Child' ? bookingData.transportFee * 0.5 : bookingData.transportFee) * data.qty;
        const subtotal = base + meal + transport;

        tbody.innerHTML += `
            <tr><td><strong>${cat}</strong></td><td class="text-end">${data.qty}</td><td class="text-end">₱${base.toFixed(2)}</td></tr>
            ${meal > 0 ? `<tr><td class="indent">└ Meal Fee</td><td class="text-end">${data.qty}</td><td class="text-end">₱${meal.toFixed(2)}</td></tr>` : ''}
            ${transport > 0 ? `<tr><td class="indent">└ Transport Fee</td><td class="text-end">${data.qty}</td><td class="text-end">₱${transport.toFixed(2)}</td></tr>` : ''}
        `;
        grandTotal += subtotal;
    }

    if (bookingData.discount > 0) {
        tbody.innerHTML += `<tr><td colspan="2"><em>Discount Applied</em></td><td class="text-end text-success">-₱${bookingData.discount.toFixed(2)}</td></tr>`;
        grandTotal -= bookingData.discount;
    }

    document.getElementById('grandTotal').textContent = `₱${grandTotal.toFixed(2)}`;
    return grandTotal;
}

let baseTotal = calculateFees();

document.getElementById('methodcategory_ID').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const fee = parseFloat(option.dataset.fee) || 0;
    const type = option.dataset.type || '';

    document.getElementById('methodcategory_processing_fee').value = fee > 0 ? `₱${fee.toFixed(2)}` : '₱0.00';
    document.getElementById('hidden_processing_fee').value = fee;
    document.getElementById('method_amount').value = `₱${(baseTotal + fee).toFixed(2)}`;

    document.querySelectorAll('.payment-type-section').forEach(s => s.style.display = 'none');
    if (type === 'card') document.getElementById('cardSection').style.display = 'block';
    if (type === 'bank') document.getElementById('bankSection').style.display = 'block';

    // Lock to Philippines for e-wallet
    const countrySelect = document.getElementById('country_ID');
    if (type === 'ewallet') {
        for (let opt of countrySelect.options) {
            if (opt.text.includes('+63')) {
                countrySelect.value = opt.value;
                countrySelect.disabled = true;
                break;
            }
        }
    } else {
        countrySelect.disabled = false;
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>