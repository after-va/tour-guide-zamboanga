<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended'){
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending'){
    header('Location: account-pending.php');
}
require_once "../../classes/tourist.php";
$tourist_ID = $_SESSION['user']['account_ID'];
$toristObj = new Tourist();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #213638;
            --accent-color: #E5A13E;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --error-color: #d9534f;
            --success-color: #28a745;
            --border-radius: 10px;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-color) !important;
            transform: translateY(-2px);
        }

        .navbar-toggler {
            border-color: var(--accent-color);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23E5A13E' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border-bottom: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1a2a2c;
            border-color: #1a2a2c;
            transform: translateY(-2px);
        }

        .btn-accent {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--primary-color);
            font-weight: 600;
            border-radius: var(--border-radius);
        }

        .btn-accent:hover {
            background-color: #d48f35;
            border-color: #d48f35;
            color: var(--primary-color);
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), #2c4a4d);
            color: white;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.8rem;
        }

        .stats-card {
            background-color: var(--white);
        }

        .stats-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stats-label {
            color: #6c757d;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .icon-box {
                width: 55px;
                height: 55px;
                font-size: 1.4rem;
            }
            .stats-number {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-compass me-2"></i>Tourist Hub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-calendar-check me-1"></i> My Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="schedules.php">
                            <i class="fas fa-clock me-1"></i> Schedules
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Welcome Card -->
            <div class="col-lg-12 mb-4">
                <div class="card welcome-card text-white">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center">
                            <div class="icon-box">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h2 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['first_name'] ?? 'Tourist'); ?>!</h2>
                                <p class="mb-0 opacity-90">Explore new destinations and manage your bookings with ease.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="col-md-4 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-calendar-check text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <div class="stats-number"><?php echo $toristObj->getTotalBookings($tourist_ID); ?></div>
                        <div class="stats-label">Total Bookings</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-clock text-warning mb-3" style="font-size: 2.5rem;"></i>
                        <div class="stats-number"><?php echo $toristObj->getUpcomingBookings($tourist_ID); ?></div>
                        <div class="stats-label">Upcoming Trips</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 2.5rem;"></i>
                        <div class="stats-number"><?php echo $toristObj->getCompletedBookings($tourist_ID); ?></div>
                        <div class="stats-label">Completed Tours</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <a href="booking.php" class="btn btn-accent w-100 py-3">
                                    <i class="fas fa-plus-circle me-2"></i>New Booking
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="schedules.php" class="btn btn-primary w-100 py-3">
                                    <i class="fas fa-search me-2"></i>View Schedules
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="profile.php" class="btn btn-outline-secondary w-100 py-3">
                                    <i class="fas fa-user-cog me-2"></i>Edit Profile
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="support.php" class="btn btn-outline-info w-100 py-3">
                                    <i class="fas fa-headset me-2"></i>Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>