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
require_once "../../classes/tour-manager.php";

$tourist_ID = $_SESSION['user']['account_ID'];
$touristObj = new Tourist();
$packageObj = new TourManager();

// Get available packages
$packages = $packageObj->viewAllPackages(); // adjust method name if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="packages.php">
            <i class="fas fa-suitcase-rolling me-2"></i>Tourismo Zamboanga
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="packages.php"><i class="fas fa-map me-1"></i> Packages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="booking.php"><i class="fas fa-calendar-check me-1"></i> My Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="schedules.php"><i class="fas fa-clock me-1"></i> Schedules</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Packages Section -->
<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="section-title"><i class="fas fa-map-marked-alt me-2"></i>Available Tour Packages</h2>
        <p class="text-muted">Discover exciting destinations and plan your next adventure with ease.</p>
    </div>

    <div class="row g-4">
        <?php if (!empty($packages)) : ?>
            <?php foreach ($packages as $pkg): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card package-card h-100">
                        <img src="<?php echo htmlspecialchars($pkg['image_path']); ?>" alt="Package Image">
                        <div class="card-body">
                            <h5 class="package-title"><?php echo htmlspecialchars($pkg['package_name']); ?></h5>
                            <p class="package-location"><i class="fas fa-location-dot me-1"></i> 
                                <?php echo htmlspecialchars($pkg['location']); ?>
                            </p>
                            <p class="package-price">₱<?php echo number_format($pkg['price'], 2); ?></p>
                            <a href="booking.php?package_ID=<?php echo $pkg['package_ID']; ?>" class="btn btn-book w-100 mt-2">
                                <i class="fas fa-calendar-plus me-1"></i> Book Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">
                <i class="fas fa-info-circle me-2"></i>No packages available at the moment.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
