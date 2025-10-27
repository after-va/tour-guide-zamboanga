<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/booking-manager.php";
require_once "../../classes/tour-manager.php";
require_once "../../classes/guide.php";

$bookingManager = new BookingManager();
$tourManager = new TourManager();
$guideObj = new Guide();

$user = $_SESSION['user'];
$bookings = $bookingManager->getBookingsByCustomer($user['person_ID']);
$popularPackages = $tourManager->getPopularPackages(6);

// Check if user already has a guide role request
$db = new Database();
$connection = $db->connect();
$sql_check = "SELECT account_role_ID FROM Account_Role WHERE login_ID = :login_ID AND role_ID = 2";
$query_check = $connection->prepare($sql_check);
$query_check->bindParam(":login_ID", $_SESSION['login_ID'], PDO::PARAM_INT);
$query_check->execute();
$has_guide_role = $query_check->rowCount() > 0;

// Handle guide role request via AJAX
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_guide') {
    if ($guideObj->requestGuideRole($_SESSION['login_ID'], $connection)) {
        $message = "success";
        $has_guide_role = true;
    } else {
        $message = "error";
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => $message]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Dashboard - Tourismo Zamboanga</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .modal-content h2 {
            color: #2c3e50;
            margin-top: 0;
        }
        .modal-content p {
            color: #666;
            font-size: 16px;
            margin: 20px 0;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 25px;
        }
        .modal-buttons button {
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn-yes {
            background: #27ae60;
            color: white;
        }
        .btn-yes:hover {
            background: #229954;
        }
        .btn-no {
            background: #95a5a6;
            color: white;
        }
        .btn-no:hover {
            background: #7f8c8d;
        }
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
            display: none;
        }
    </style>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px; margin: -20px -20px 20px -20px;">
            <h1>Tourismo Zamboanga - Tourist Dashboard</h1>
            <p>Welcome, <?= htmlspecialchars($user['full_name']) ?>!</p>
            <nav>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
                <a href="my-bookings.php" style="color: white; margin-right: 15px;">My Bookings</a>
                <a href="../public/browse-packages.php" style="color: white; margin-right: 15px;">Browse Packages</a>
                <a href="../public/browse-spots.php" style="color: white; margin-right: 15px;">Tourist Spots</a>
                <a href="profile.php" style="color: white; margin-right: 15px;">Profile</a>
                <a href="../../logout.php" style="color: white;">Logout</a>
            </nav>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: #e3f2fd; padding: 20px; border-left: 4px solid #1976d2;">
                <h3 style="margin: 0 0 10px 0;">Total Bookings</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;"><?= count($bookings) ?></p>
            </div>
            
            <div style="background: #e8f5e9; padding: 20px; border-left: 4px solid #4caf50;">
                <h3 style="margin: 0 0 10px 0;">Confirmed Tours</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= count(array_filter($bookings, fn($b) => $b['booking_Status'] === 'confirmed')) ?>
                </p>
            </div>
            
            <div style="background: #fff3e0; padding: 20px; border-left: 4px solid #ff9800;">
                <h3 style="margin: 0 0 10px 0;">Pending Bookings</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= count(array_filter($bookings, fn($b) => $b['booking_Status'] === 'pending')) ?>
                </p>
            </div>
            
            <div style="background: #f3e5f5; padding: 20px; border-left: 4px solid #9c27b0;">
                <h3 style="margin: 0 0 10px 0;">Completed Tours</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0;">
                    <?= count(array_filter($bookings, fn($b) => $b['booking_Status'] === 'completed')) ?>
                </p>
            </div>
        </div>

        <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 30px;">
            <h2>Recent Bookings</h2>
            <?php if (empty($bookings)): ?>
                <p style="text-align: center; padding: 40px; background: #f5f5f5;">
                    You haven't made any bookings yet. 
                    <a href="../public/browse-packages.php" style="color: #1976d2;">Browse tour packages</a> to get started!
                </p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Package</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Date</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">PAX</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Status</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($bookings, 0, 5) as $booking): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($booking['tourPackage_Name']) ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= $booking['schedule_StartDateTime'] ? date('M d, Y', strtotime($booking['schedule_StartDateTime'])) : 'TBA' ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= $booking['booking_PAX'] ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <span style="padding: 4px 8px; background: 
                                        <?= $booking['booking_Status'] === 'confirmed' ? '#4caf50' : 
                                            ($booking['booking_Status'] === 'pending' ? '#ff9800' : 
                                            ($booking['booking_Status'] === 'completed' ? '#9c27b0' : '#757575')) ?>; 
                                        color: white; font-size: 12px;">
                                        <?= htmlspecialchars(ucfirst($booking['booking_Status'])) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <a href="booking-details.php?id=<?= $booking['booking_ID'] ?>" 
                                       style="color: #1976d2; text-decoration: none;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($bookings) > 5): ?>
                    <p style="text-align: center; margin-top: 15px;">
                        <a href="my-bookings.php" style="color: #1976d2;">View All Bookings</a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div style="background: white; border: 1px solid #ddd; padding: 20px;">
            <h2>Popular Tour Packages</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach ($popularPackages as $package): ?>
                    <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                        <h3><?= htmlspecialchars($package['tourPackage_Name']) ?></h3>
                        <p><?= htmlspecialchars($package['tourPackage_Description']) ?></p>
                        
                        <div style="margin: 10px 0;">
                            <strong>Duration:</strong> <?= htmlspecialchars($package['tourPackage_Duration']) ?><br>
                            <strong>Capacity:</strong> <?= htmlspecialchars($package['tourPackage_Capacity']) ?> persons
                        </div>

                        <?php if (isset($package['avg_rating']) && $package['avg_rating']): ?>
                            <p>
                                <strong>Rating:</strong> 
                                <?= number_format($package['avg_rating'], 1) ?>/5.0 
                                (<?= $package['total_reviews'] ?> reviews)
                            </p>
                        <?php endif; ?>

                        <a href="../public/package-details.php?id=<?= $package['tourPackage_ID'] ?>" 
                           style="display: inline-block; margin-top: 10px; padding: 8px 15px; background: #1976d2; color: white; text-decoration: none;">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Guide Request Modal -->
    <div id="guideModal" class="modal">
        <div class="modal-content">
            <h2>🎯 Become a Tour Guide?</h2>
            <p>Do you want to become a tour guide and start earning?</p>
            <p style="font-size: 14px; color: #999;">Your request will be reviewed by our admin team.</p>
            <div class="modal-buttons">
                <button class="btn-yes" onclick="requestGuideRole()">Yes, I'm Interested</button>
                <button class="btn-no" onclick="closeGuideModal()">Not Now</button>
            </div>
        </div>
    </div>

    <!-- Success Message Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h2>✓ Request Submitted!</h2>
            <p>Your guide role request has been submitted successfully.</p>
            <p style="font-size: 14px; color: #999;">Admin needs to accept your approval. You'll be notified once approved.</p>
            <div class="modal-buttons">
                <button class="btn-yes" onclick="closeSuccessModal()" style="width: 100%;">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Show guide modal on page load if user doesn't have guide role
        window.addEventListener('load', function() {
            <?php if (!$has_guide_role): ?>
                // Check if user has already dismissed this today
                const dismissed = localStorage.getItem('guideModalDismissed');
                if (!dismissed) {
                    document.getElementById('guideModal').style.display = 'block';
                }
            <?php endif; ?>
        });

        function closeGuideModal() {
            document.getElementById('guideModal').style.display = 'none';
            // Dismiss for today
            localStorage.setItem('guideModalDismissed', 'true');
        }

        function requestGuideRole() {
            const formData = new FormData();
            formData.append('action', 'request_guide');

            fetch('dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('guideModal').style.display = 'none';
                    document.getElementById('successModal').style.display = 'block';
                } else {
                    alert('Error: ' + (data.error || 'Failed to submit request'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
            localStorage.setItem('guideModalDismissed', 'true');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const guideModal = document.getElementById('guideModal');
            const successModal = document.getElementById('successModal');
            if (event.target === guideModal) {
                closeGuideModal();
            }
            if (event.target === successModal) {
                closeSuccessModal();
            }
        }
    </script>
</body>
</html>
