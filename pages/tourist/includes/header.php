<?php
// === Start session safely ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === Get the logged-in tourist ID safely ===
$account_ID = $_SESSION['account_ID'] ?? null;  // This is your actual session key

// Default values (safe fallback)
$touristNotification = [];
$unread_count = 0;
$badge_display = 'd-none'; // hide badge by default

// === Only load notifications if user is logged in ===
if ($account_ID && is_numeric($account_ID)) {
    // Fix your class path — you had wrong filename
    require_once __DIR__ . "/../../../classes/booking.php";
    require_once __DIR__ . "/../../../classes/activity-log.php";  // ← Correct filename!

    try {
        $bookingObj  = new Booking();
        $activityObj = new ActivityLogs();

        // Optional: update booking statuses
        $bookingObj->updateBookings();

        // Fetch notifications using the correct ID
        $touristNotification = $activityObj->touristNotification((int)$account_ID);

        // Count unread
        foreach ($touristNotification as $n) {
            if ((int)$n['is_viewed'] === 0) {
                $unread_count++;
            }
        }

        $badge_display = $unread_count > 0 ? '' : 'd-none';

    } catch (Throwable $e) {
        error_log("Header notification error: " . $e->getMessage());
        $touristNotification = [];
        $unread_count = 0;
    }
}
?>


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
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                           href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'booking.php' ? 'active' : '' ?>" 
                           href="booking.php">My Booking</a>
                    </li>

                    <!-- Notification Dropdown -->
                    <li class="nav-item dropdown position-relative">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill d-none d-lg-inline-block"></i>
                            <span class="d-lg-none">Notifications</span>

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?= $badge_display ?>"
                                  style="font-size: 0.65rem;">
                                <?= $unread_count ?>
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        </a>

                        <ul id="notification-dropdown" class="dropdown-menu dropdown-menu-end mt-2 shadow" style="width: 340px;">
                            <li><h6 class="dropdown-header">Notifications (<?= count($touristNotification) ?>)</h6></li>
                            <li><hr class="dropdown-divider"></li>

                            <?php if (empty($touristNotification)): ?>
                                <li>
                                    <div class="dropdown-item text-center text-muted py-5">
                                        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                        No notifications yet
                                    </div>
                                </li>
                            <?php else: ?>
                                <?php foreach ($touristNotification as $notif): 
                                    $isUnread = (int)$notif['is_viewed'] === 0;
                                ?>
                                    <li>
                                        <a class="dropdown-item py-3 <?= $isUnread ? 'bg-light fw-semibold' : '' ?> mark-as-read"
                                           href="javascript:void(0)"
                                           data-activity-id="<?= $notif['activity_ID'] ?>"
                                           data-account-id="<?= $account_ID ?>">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 mt-1">
                                                    <?php 
                                                    $action = strtolower($notif['action_name']);
                                                    if (str_contains($action, 'booking')): ?>
                                                        <i class="bi bi-calendar-check-fill text-primary"></i>
                                                    <?php elseif (str_contains($action, 'payment')): ?>
                                                        <i class="bi bi-credit-card-fill text-success"></i>
                                                    <?php elseif (str_contains($action, 'message')): ?>
                                                        <i class="bi bi-chat-dots-fill text-info"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-bell-fill text-secondary"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div><?= htmlspecialchars($notif['activity_description']) ?></div>
                                                    <div class="text-muted small"><?= htmlspecialchars($notif['action_name']) ?></div>
                                                    <div class="text-muted small">
                                                        <?= date('M j, Y · g:i A', strtotime($notif['activity_timestamp'])) ?>
                                                    </div>
                                                </div>
                                                <?php if ($isUnread): ?>
                                                    <div class="ms-2">
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
                </ul>

                <?php if ($account_ID): ?>
                    <a href="logout.php" class="btn btn-info ms-lg-3">Log out</a>
                <?php else: ?>
                    <a href="../../login.php" class="btn btn-outline-light ms-lg-3">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<script>
    


</script>