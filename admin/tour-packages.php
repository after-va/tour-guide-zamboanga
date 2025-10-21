<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/TourPackage.php";

$tourPackage = new TourPackage();
$packages = $tourPackage->getAllTourPackages();

$success = isset($_GET['success']) || isset($_GET['updated']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Tour Packages - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        nav {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .package-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafafa;
        }
        .package-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
        }
        .package-header h3 {
            margin: 0;
            color: #007bff;
        }
        .package-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        .info-item {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        .info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            font-weight: bold;
            color: #333;
        }
        .itinerary-section {
            margin-top: 15px;
        }
        .day-section {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid #28a745;
        }
        .day-header {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .itinerary-item {
            display: flex;
            gap: 15px;
            padding: 10px;
            margin-bottom: 8px;
            background: #f9f9f9;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        .itinerary-item.break-time {
            background: #fff9e6;
            border-left-color: #ffc107;
        }
        .time-badge {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            align-self: flex-start;
        }
        .break-time .time-badge {
            background: #ffc107;
            color: #333;
        }
        .item-content {
            flex: 1;
        }
        .item-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .item-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .item-notes {
            font-size: 12px;
            color: #999;
            font-style: italic;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .toggle-itinerary {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Tour Packages</h1>
        
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="bookings.php">Bookings</a> |
            <a href="users.php">Users</a> |
            <a href="tour-packages.php">Tour Packages</a> |
            <a href="tour-spots.php">Tour Spots</a> |
            <a href="schedules.php">Schedules</a> |
            <a href="payments.php">Payments</a> |
            <a href="logout.php">Logout</a>
        </nav>
        
        <?php if ($success): ?>
            <div class="alert">
                ✓ Tour package saved successfully!
            </div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="add-tour-package.php" class="btn btn-primary">+ Add New Tour Package</a>
        </div>
        
        <?php if (empty($packages)): ?>
            <p style="text-align: center; color: #999; padding: 40px;">No tour packages found. Create your first package!</p>
        <?php else: ?>
            <?php foreach ($packages as $p): 
                $itineraryByDay = [];
                foreach ($p['itinerary'] as $item) {
                    $itineraryByDay[$item['day_number']][] = $item;
                }
            ?>
            <div class="package-card">
                <div class="package-header">
                    <h3><?= htmlspecialchars($p['tourPackage_Name']); ?></h3>
                    <div class="actions">
                        <a href="edit-tour-package.php?id=<?= $p['tourPackage_ID']; ?>" class="btn btn-secondary">Edit</a>
                        <a href="delete-tour-package.php?id=<?= $p['tourPackage_ID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this package?')">Delete</a>
                    </div>
                </div>
                
                <p style="color: #666; margin-bottom: 15px;"><?= htmlspecialchars($p['tourPackage_Description']); ?></p>
                
                <div class="package-info">
                    <div class="info-item">
                        <div class="info-label">Duration</div>
                        <div class="info-value"><?= htmlspecialchars($p['tourPackage_Duration']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Days</div>
                        <div class="info-value"><?= htmlspecialchars($p['tourPackage_TotalDays']); ?> Day<?= $p['tourPackage_TotalDays'] > 1 ? 's' : ''; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Max Capacity</div>
                        <div class="info-value"><?= htmlspecialchars($p['tourPackage_Capacity']); ?> People</div>
                    </div>
                </div>
                
                <div class="itinerary-section" id="itinerary-<?= $p['tourPackage_ID']; ?>" style="display: none;">
                    <h4 style="margin-bottom: 15px; color: #333;">📋 Detailed Itinerary</h4>
                    <?php if (empty($p['itinerary'])): ?>
                        <p style="color: #999; font-style: italic;">No itinerary items yet</p>
                    <?php else: ?>
                        <?php foreach ($itineraryByDay as $day => $items): ?>
                            <div class="day-section">
                                <div class="day-header">📅 Day <?= $day; ?></div>
                                <?php foreach ($items as $item): ?>
                                    <div class="itinerary-item <?= empty($item['spots_ID']) ? 'break-time' : ''; ?>">
                                        <div class="time-badge">
                                            <?= date('g:i A', strtotime($item['start_time'])); ?> - <?= date('g:i A', strtotime($item['end_time'])); ?>
                                        </div>
                                        <div class="item-content">
                                            <div class="item-title">
                                                <?php if (empty($item['spots_ID'])): ?>
                                                    <?php 
                                                        $desc = $item['activity_description'];
                                                        if (stripos($desc, 'lunch') !== false) echo '🍽️ ';
                                                        elseif (stripos($desc, 'sleep') !== false) echo '🌙 ';
                                                        else echo '☕ ';
                                                    ?>
                                                    <?= htmlspecialchars($item['activity_description'] ?: 'Break Time'); ?>
                                                <?php else: ?>
                                                    📍 <?= htmlspecialchars($item['spots_Name']); ?>
                                                    <span style="color: #999; font-size: 12px; font-weight: normal;">(<?= htmlspecialchars($item['spots_category']); ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($item['activity_description']) && !empty($item['spots_ID'])): ?>
                                                <div class="item-description"><?= htmlspecialchars($item['activity_description']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($item['notes'])): ?>
                                                <div class="item-notes">💡 <?= htmlspecialchars($item['notes']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <a class="toggle-itinerary" onclick="toggleItinerary(<?= $p['tourPackage_ID']; ?>)" id="toggle-<?= $p['tourPackage_ID']; ?>">
                    ▼ Show Detailed Itinerary (<?= count($p['itinerary']); ?> items)
                </a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleItinerary(packageId) {
            const itinerary = document.getElementById('itinerary-' + packageId);
            const toggle = document.getElementById('toggle-' + packageId);
            
            if (itinerary.style.display === 'none') {
                itinerary.style.display = 'block';
                toggle.innerHTML = toggle.innerHTML.replace('▼ Show', '▲ Hide');
            } else {
                itinerary.style.display = 'none';
                toggle.innerHTML = toggle.innerHTML.replace('▲ Hide', '▼ Show');
            }
        }
    </script>
</body>
</html>
