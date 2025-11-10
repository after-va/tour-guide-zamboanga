<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/booking.php";
require_once "../../classes/tourist.php";

$tourist_ID = $_SESSION['user']['account_ID'];
$touristObj = new Tourist();
$bookingObj = new Booking();


$bookings = $bookingObj->viewBookingByTourist($tourist_ID);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourismo Zamboanga</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/tourist/booking.css">
    <link rel="stylesheet" href="/../../assets/css/header.css">

    
</head>
<body>
<header class = "header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Tourismo Zamboanga</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">My Booking</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="#">Tour Spots</a></li> -->
            </ul>
            <a href="logout.php" class="btn btn-info ms-lg-3">Log out </a>
            </div>
        </div>
        </nav>

</header>
<main class = "">   
<div class="container py-5">
    <h2 class="mb-4">My Bookings</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <a href="tour-packages-browse.php" class="btn btn-outline-primary me-2">
            <i class="bi bi-search"></i> Browse Tour Packages
        </a>
        <a href="booking-history.php" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history"></i> View Booking History
        </a>
    </div>

    <?php if (!empty($bookings)): ?>
        <div class="row g-4">
            <?php $no = 1; foreach ($bookings as $booking): 
                // Filter only relevant statuses
                if (!in_array($booking['booking_status'], ['Pending for Payment', 'Pending for Approval', 'Approved', 'In Progress'])) continue;
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card booking-card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <small class="text-white-50">#<?= $no++; ?></small>
                            <span class="badge 
                                <?= $booking['booking_status'] == 'Pending for Payment' ? 'bg-warning' : 
                                   ($booking['booking_status'] == 'Pending for Approval' ? 'bg-info' : 
                                   ($booking['booking_status'] == 'Approved' ? 'bg-success' : 'bg-secondary')) ?> 
                                status-badge">
                                <?= htmlspecialchars($booking['booking_status']) ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title mb-3"><?= htmlspecialchars($booking['tourpackage_name']) ?></h5>
                            <p class="card-text text-muted small">
                                <?= htmlspecialchars($booking['tourpackage_desc']) ?>
                            </p>

                            <ul class="list-unstyled text-muted small">
                                <li><strong>Duration:</strong> <?= htmlspecialchars($booking['schedule_days']) ?> days</li>
                                <li><strong>Guide:</strong> <?= htmlspecialchars($booking['guide_name']) ?></li>
                                <li><strong>Start:</strong> <?= htmlspecialchars($booking['booking_start_date']) ?></li>
                                <li><strong>End:</strong> <?= htmlspecialchars($booking['booking_end_date']) ?></li>
                                <li><strong>Spots:</strong> <?= htmlspecialchars($booking['tour_spots'] ?? '—') ?></li>
                            </ul>
                        </div>

                        <div class="card-footer bg-light card-footer-actions">
                            <?php if ($booking['booking_status'] == 'Pending for Payment'): ?>
                                <a href="payment-form.php?id=<?= $booking['booking_ID'] ?>" 
                                   class="btn btn-sm btn-success me-1">
                                    <i class="bi bi-credit-card"></i> Pay Now
                                </a>
                                <a href="booking-cancel.php?id=<?= $booking['booking_ID'] ?>" 
                                   class="btn btn-sm btn-outline-danger me-1 cancel-booking"
                                   data-name="<?= htmlspecialchars($booking['tourpackage_name']) ?>">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <a href="booking-view.php?id=<?= $booking['booking_ID'] ?>" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            <?php else: ?>
                                <a href="booking-cancel.php?id=<?= $booking['booking_ID'] ?>" 
                                   class="btn btn-sm btn-outline-danger me-1 cancel-booking"
                                   data-name="<?= htmlspecialchars($booking['tourpackage_name']) ?>">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <a href="booking-view.php?id=<?= $booking['booking_ID'] ?>" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="mt-3 text-muted">You currently have no active bookings.</p>
            <a href="tour-packages-browse.php" class="btn btn-primary">Explore Tours</a>
        </div>
    <?php endif; ?>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    $('.cancel-booking').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const tourName = $(this).data('name');

        if (confirm(`Are you sure you want to cancel your booking for "${tourName}"?`)) {
            window.location.href = url;
        }
    });
});
</script>
</body>
</html>
