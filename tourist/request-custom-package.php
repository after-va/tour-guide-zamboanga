<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

require_once "../php/CustomPackage.php";
require_once "../php/Guide.php";
require_once "../php/TourSpot.php";
require_once "../php/TourPackage.php";
require_once "../php/Notification.php";

$customPackage = new CustomPackage();
$guide = new Guide();
$tourSpot = new TourSpot();
$tourPackage = new TourPackage();
$notification = new Notification();

$guide_id = $_GET['guide_id'] ?? 0;
$package_id = $_GET['package_id'] ?? null;

$guideInfo = $guide->getGuideById($guide_id);
if (!$guideInfo) {
    header("Location: browse-guides.php");
    exit();
}

$allSpots = $tourSpot->getAllTourSpots();
$basePackage = null;
if ($package_id) {
    $basePackage = $tourPackage->getTourPackageById($package_id);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'tourPackage_ID' => $package_id,
        'request_title' => $_POST['request_title'],
        'request_description' => $_POST['request_description'],
        'preferred_date' => $_POST['preferred_date'] ?: null,
        'preferred_duration' => $_POST['preferred_duration'],
        'number_of_pax' => $_POST['number_of_pax'],
        'budget_range' => $_POST['budget_range'],
        'special_requirements' => $_POST['special_requirements'],
        'spots' => []
    ];
    
    // Process selected spots
    if (isset($_POST['spots']) && is_array($_POST['spots'])) {
        foreach ($_POST['spots'] as $spot_id) {
            $priority = $_POST['spot_priority'][$spot_id] ?? 2;
            $notes = $_POST['spot_notes'][$spot_id] ?? '';
            $data['spots'][] = [
                'spots_ID' => $spot_id,
                'priority' => $priority,
                'notes' => $notes
            ];
        }
    }
    
    $request_ID = $customPackage->createCustomRequest($_SESSION['user_id'], $guide_id, $data);
    
    if ($request_ID) {
        // Send notification to guide
        $notification->createNotification(
            $guide_id,
            'package_request',
            'New Custom Package Request',
            $_SESSION['full_name'] . ' has requested a custom package: "' . $data['request_title'] . '"',
            'guide/package-requests.php?id=' . $request_ID
        );
        
        $success = "Your custom package request has been sent to " . $guideInfo['full_name'] . "!";
        $request_sent = true;
    } else {
        $error = "Failed to send request. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Request Custom Package - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .guide-info { background: #f0f0f0; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .form-container { max-width: 800px; margin: 0 auto; }
        .form-group { margin: 20px 0; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px;
        }
        .form-group textarea { resize: vertical; }
        .form-group small { color: #666; font-size: 12px; }
        .btn { padding: 12px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 16px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .spots-section { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9; }
        .spot-item { background: white; padding: 15px; margin: 10px 0; border-left: 3px solid #007bff; border-radius: 4px; }
        .spot-item label { font-weight: normal; cursor: pointer; }
        .spot-checkbox { margin-right: 10px; }
        .spot-details { margin-left: 30px; margin-top: 10px; display: none; }
        .spot-details.active { display: block; }
        .priority-select { padding: 5px; margin: 5px 0; }
        .base-package-info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <h1>Request Custom Package</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="browse-tours.php">Browse Tours</a>
        <a href="browse-guides.php">Browse Guides</a>
        <a href="my-requests.php">My Requests</a>
        <a href="my-bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <?php if (isset($success)): ?>
        <div class="success">
            <?php echo $success; ?>
            <br><br>
            <a href="my-requests.php" class="btn btn-primary">View My Requests</a>
            <a href="browse-guides.php" class="btn btn-secondary">Browse More Guides</a>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (!isset($request_sent)): ?>
        <div class="guide-info">
            <h2>Requesting from: <?php echo htmlspecialchars($guideInfo['full_name']); ?></h2>
            <p><strong>Rating:</strong> ★ <?php echo $guideInfo['role_rating_score'] ? number_format($guideInfo['role_rating_score'], 1) : 'N/A'; ?>/5.0</p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($guideInfo['email']); ?></p>
        </div>
        
        <?php if ($basePackage): ?>
            <div class="base-package-info">
                <h3>📦 Customizing: <?php echo htmlspecialchars($basePackage['tourPackage_Name']); ?></h3>
                <p><?php echo htmlspecialchars($basePackage['tourPackage_Description']); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($basePackage['tourPackage_Duration']); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2>Package Request Details</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Request Title: *</label>
                    <input type="text" name="request_title" required placeholder="e.g., Family Tour of Zamboanga Beaches">
                    <small>Give your custom package request a descriptive title</small>
                </div>
                
                <div class="form-group">
                    <label>Description: *</label>
                    <textarea name="request_description" rows="5" required placeholder="Describe what you're looking for in this tour package..."></textarea>
                    <small>Provide details about what you want to experience</small>
                </div>
                
                <div class="form-group">
                    <label>Preferred Date:</label>
                    <input type="date" name="preferred_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    <small>Leave blank if flexible</small>
                </div>
                
                <div class="form-group">
                    <label>Preferred Duration: *</label>
                    <select name="preferred_duration" required>
                        <option value="">-- Select Duration --</option>
                        <option value="Half Day (4 hours)">Half Day (4 hours)</option>
                        <option value="Full Day (8 hours)">Full Day (8 hours)</option>
                        <option value="2 Days / 1 Night">2 Days / 1 Night</option>
                        <option value="3 Days / 2 Nights">3 Days / 2 Nights</option>
                        <option value="4 Days / 3 Nights">4 Days / 3 Nights</option>
                        <option value="5+ Days">5+ Days</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Number of People (PAX): *</label>
                    <input type="number" name="number_of_pax" min="1" required placeholder="e.g., 4">
                </div>
                
                <div class="form-group">
                    <label>Budget Range: *</label>
                    <select name="budget_range" required>
                        <option value="">-- Select Budget Range --</option>
                        <option value="₱1,000 - ₱3,000">₱1,000 - ₱3,000</option>
                        <option value="₱3,000 - ₱5,000">₱3,000 - ₱5,000</option>
                        <option value="₱5,000 - ₱10,000">₱5,000 - ₱10,000</option>
                        <option value="₱10,000 - ₱20,000">₱10,000 - ₱20,000</option>
                        <option value="₱20,000+">₱20,000+</option>
                        <option value="Flexible">Flexible</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Special Requirements:</label>
                    <textarea name="special_requirements" rows="4" placeholder="e.g., Wheelchair accessible, Child-friendly activities, Vegetarian meals, etc."></textarea>
                </div>
                
                <div class="spots-section">
                    <h3>Select Tour Spots (Optional)</h3>
                    <p>Choose the places you'd like to visit and set their priority level.</p>
                    
                    <?php foreach ($allSpots as $spot): ?>
                        <div class="spot-item">
                            <label>
                                <input type="checkbox" name="spots[]" value="<?php echo $spot['spots_ID']; ?>" 
                                       class="spot-checkbox" onchange="toggleSpotDetails(<?php echo $spot['spots_ID']; ?>)">
                                <strong><?php echo htmlspecialchars($spot['spots_Name']); ?></strong>
                            </label>
                            <br>
                            <small><?php echo htmlspecialchars($spot['spots_Description']); ?></small>
                            <small style="color: #666;"> - <?php echo htmlspecialchars($spot['spots_category']); ?></small>
                            
                            <div class="spot-details" id="details-<?php echo $spot['spots_ID']; ?>">
                                <label>Priority:</label>
                                <select name="spot_priority[<?php echo $spot['spots_ID']; ?>]" class="priority-select">
                                    <option value="1">Must Visit</option>
                                    <option value="2" selected>Would Like to Visit</option>
                                    <option value="3">Optional</option>
                                </select>
                                <br>
                                <label>Notes:</label>
                                <input type="text" name="spot_notes[<?php echo $spot['spots_ID']; ?>]" 
                                       placeholder="Any specific requests for this spot?" style="width: 100%; padding: 5px;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-group" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Send Request to Guide</button>
                    <a href="guide-packages.php?guide_id=<?php echo $guide_id; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
    
    <script>
        function toggleSpotDetails(spotId) {
            const checkbox = document.querySelector(`input[value="${spotId}"]`);
            const details = document.getElementById(`details-${spotId}`);
            
            if (checkbox.checked) {
                details.classList.add('active');
            } else {
                details.classList.remove('active');
            }
        }
    </script>
</body>
</html>
