<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/TourPackage.php";
require_once "../php/TourSpot.php";

$success = "";
$error = "";

// Get all tour spots for dropdown
$tourSpot = new TourSpot();
$spots = $tourSpot->getAllTourSpots();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tourPackage_Name = trim($_POST['tourPackage_Name']);
    $tourPackage_Description = trim($_POST['tourPackage_Description']);
    $tourPackage_Capacity = trim($_POST['tourPackage_Capacity']);
    $tourPackage_Duration = trim($_POST['tourPackage_Duration']);
    $tourPackage_TotalDays = intval($_POST['tourPackage_TotalDays']);
    
    // Validation
    if (empty($tourPackage_Name)) {
        $error = "Package name is required";
    } elseif (empty($tourPackage_Description)) {
        $error = "Description is required";
    } elseif (empty($tourPackage_Capacity)) {
        $error = "Capacity is required";
    } elseif (empty($tourPackage_Duration)) {
        $error = "Duration is required";
    } elseif ($tourPackage_TotalDays < 1) {
        $error = "Total days must be at least 1";
    } else {
        // Process itinerary items
        $itinerary_items = [];
        if (isset($_POST['itinerary']) && is_array($_POST['itinerary'])) {
            foreach ($_POST['itinerary'] as $item) {
                // Require either a spot OR a break type, plus start/end times
                if (!empty($item['start_time']) && !empty($item['end_time'])) {
                    $itinerary_items[] = [
                        'spots_ID' => !empty($item['spots_ID']) ? intval($item['spots_ID']) : null,
                        'day_number' => intval($item['day_number']),
                        'sequence_order' => intval($item['sequence_order']),
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                        'activity_description' => trim($item['activity_description'] ?? ''),
                        'notes' => trim($item['notes'] ?? '')
                    ];
                }
            }
        }
        
        if (empty($itinerary_items)) {
            $error = "Please add at least one itinerary item";
        } else {
            $tourPackage = new TourPackage();
            $result = $tourPackage->createTourPackage($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $tourPackage_TotalDays, $itinerary_items);
            
            if ($result !== false) {
                header("Location: tour-packages.php?success=1");
                exit();
            } else {
                $error = "Failed to add tour package. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Tour Package - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="number"],
        input[type="time"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
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
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        .required {
            color: red;
        }
        small {
            color: #666;
            font-size: 0.9em;
        }
        .itinerary-item {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }
        .itinerary-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        .itinerary-item-header h4 {
            margin: 0;
            color: #007bff;
        }
        .itinerary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .itinerary-grid-3 {
            display: grid;
            grid-template-columns: 80px 80px 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Add New Tour Package</h1>
    
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
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (empty($spots)): ?>
            <div class="alert alert-error">
                <strong>No tour spots available!</strong> Please <a href="add-tour-spot.php">add a tour spot</a> first before creating packages.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="packageForm">
            <div class="form-group">
                <label for="tourPackage_Name">Package Name <span class="required">*</span></label>
                <input type="text" id="tourPackage_Name" name="tourPackage_Name" 
                       value="<?php echo isset($_POST['tourPackage_Name']) ? htmlspecialchars($_POST['tourPackage_Name']) : ''; ?>" 
                       placeholder="e.g., Zamboanga City Heritage Tour"
                       required>
            </div>
            
            <div class="form-group">
                <label for="tourPackage_Description">Description <span class="required">*</span></label>
                <textarea id="tourPackage_Description" name="tourPackage_Description" required><?php echo isset($_POST['tourPackage_Description']) ? htmlspecialchars($_POST['tourPackage_Description']) : ''; ?></textarea>
                <small>Describe what's included in this tour package</small>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="tourPackage_Capacity">Maximum Capacity <span class="required">*</span></label>
                    <input type="number" id="tourPackage_Capacity" name="tourPackage_Capacity" 
                           value="<?php echo isset($_POST['tourPackage_Capacity']) ? htmlspecialchars($_POST['tourPackage_Capacity']) : ''; ?>" 
                           placeholder="e.g., 15"
                           min="1"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="tourPackage_Duration">Duration <span class="required">*</span></label>
                    <input type="text" id="tourPackage_Duration" name="tourPackage_Duration" 
                           value="<?php echo isset($_POST['tourPackage_Duration']) ? htmlspecialchars($_POST['tourPackage_Duration']) : ''; ?>" 
                           placeholder="e.g., 3 Days / 2 Nights"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="tourPackage_TotalDays">Total Days <span class="required">*</span></label>
                    <input type="number" id="tourPackage_TotalDays" name="tourPackage_TotalDays" 
                           value="<?php echo isset($_POST['tourPackage_TotalDays']) ? htmlspecialchars($_POST['tourPackage_TotalDays']) : '1'; ?>" 
                           min="1"
                           max="30"
                           required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Tour Itinerary <span class="required">*</span></label>
                <small style="display: block; margin-bottom: 10px;">Build your day-by-day itinerary with specific times</small>
                
                <div id="itineraryContainer">
                    <!-- Itinerary items will be added here dynamically -->
                </div>
                
                <div style="margin-top: 10px; display: flex; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="addItineraryItem()">
                        + Add Tour Spot
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="addBreakTime('Lunch Break')" style="background-color: #28a745;">
                        🍽️ Add Lunch Break
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="addBreakTime('Rest Break')" style="background-color: #17a2b8;">
                        ☕ Add Break Time
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="addBreakTime('Sleep Time')" style="background-color: #6f42c1;">
                        🌙 Add Sleep Time
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" <?php echo empty($spots) ? 'disabled' : ''; ?>>Create Tour Package</button>
                <a href="tour-packages.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
        let itineraryCounter = 0;
        const spots = <?php echo json_encode($spots); ?>;
        
        function addItineraryItem() {
            itineraryCounter++;
            const container = document.getElementById('itineraryContainer');
            const totalDays = parseInt(document.getElementById('tourPackage_TotalDays').value) || 1;
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'itinerary-item';
            itemDiv.id = 'itinerary-' + itineraryCounter;
            
            itemDiv.innerHTML = `
                <div class="itinerary-item-header">
                    <h4>Itinerary Item #${itineraryCounter}</h4>
                    <button type="button" class="btn btn-danger" onclick="removeItineraryItem(${itineraryCounter})">Remove</button>
                </div>
                
                <div class="itinerary-grid-3">
                    <div>
                        <label>Day <span class="required">*</span></label>
                        <input type="number" name="itinerary[${itineraryCounter}][day_number]" min="1" max="${totalDays}" value="1" required>
                    </div>
                    <div>
                        <label>Order <span class="required">*</span></label>
                        <input type="number" name="itinerary[${itineraryCounter}][sequence_order]" min="1" value="${itineraryCounter}" required>
                    </div>
                    <div>
                        <label>Tour Spot <span class="required">*</span></label>
                        <select name="itinerary[${itineraryCounter}][spots_ID]" required>
                            <option value="">-- Select Spot --</option>
                            ${spots.map(spot => `<option value="${spot.spots_ID}">${spot.spots_Name} (${spot.spots_category})</option>`).join('')}
                        </select>
                    </div>
                </div>
                
                <div class="itinerary-grid">
                    <div>
                        <label>Start Time <span class="required">*</span></label>
                        <input type="time" name="itinerary[${itineraryCounter}][start_time]" required>
                    </div>
                    <div>
                        <label>End Time <span class="required">*</span></label>
                        <input type="time" name="itinerary[${itineraryCounter}][end_time]" required>
                    </div>
                </div>
                
                <div>
                    <label>Activity Description</label>
                    <textarea name="itinerary[${itineraryCounter}][activity_description]" rows="2" placeholder="e.g., Morning walk along the scenic waterfront, enjoy the sea breeze and take photos"></textarea>
                </div>
                
                <div style="margin-top: 10px;">
                    <label>Notes</label>
                    <input type="text" name="itinerary[${itineraryCounter}][notes]" placeholder="e.g., Bring comfortable walking shoes, Entrance fee: PHP 50">
                </div>
            `;
            
            container.appendChild(itemDiv);
        }
        
        function addBreakTime(breakType) {
            itineraryCounter++;
            const container = document.getElementById('itineraryContainer');
            const totalDays = parseInt(document.getElementById('tourPackage_TotalDays').value) || 1;
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'itinerary-item';
            itemDiv.id = 'itinerary-' + itineraryCounter;
            itemDiv.style.background = '#fff9e6';
            itemDiv.style.borderLeft = '4px solid #ffc107';
            
            let icon = '☕';
            let defaultDescription = '';
            if (breakType === 'Lunch Break') {
                icon = '🍽️';
                defaultDescription = 'Lunch break at a local restaurant';
            } else if (breakType === 'Rest Break') {
                icon = '☕';
                defaultDescription = 'Short rest and refreshment break';
            } else if (breakType === 'Sleep Time') {
                icon = '🌙';
                defaultDescription = 'Overnight stay / Sleep time';
            }
            
            itemDiv.innerHTML = `
                <div class="itinerary-item-header">
                    <h4>${icon} ${breakType} #${itineraryCounter}</h4>
                    <button type="button" class="btn btn-danger" onclick="removeItineraryItem(${itineraryCounter})">Remove</button>
                </div>
                
                <input type="hidden" name="itinerary[${itineraryCounter}][spots_ID]" value="">
                
                <div class="itinerary-grid-3">
                    <div>
                        <label>Day <span class="required">*</span></label>
                        <input type="number" name="itinerary[${itineraryCounter}][day_number]" min="1" max="${totalDays}" value="1" required>
                    </div>
                    <div>
                        <label>Order <span class="required">*</span></label>
                        <input type="number" name="itinerary[${itineraryCounter}][sequence_order]" min="1" value="${itineraryCounter}" required>
                    </div>
                    <div>
                        <label>Break Type</label>
                        <input type="text" value="${breakType}" readonly style="background: #f0f0f0;">
                    </div>
                </div>
                
                <div class="itinerary-grid">
                    <div>
                        <label>Start Time <span class="required">*</span></label>
                        <input type="time" name="itinerary[${itineraryCounter}][start_time]" required>
                    </div>
                    <div>
                        <label>End Time <span class="required">*</span></label>
                        <input type="time" name="itinerary[${itineraryCounter}][end_time]" required>
                    </div>
                </div>
                
                <div>
                    <label>Description</label>
                    <textarea name="itinerary[${itineraryCounter}][activity_description]" rows="2" placeholder="${defaultDescription}">${defaultDescription}</textarea>
                </div>
                
                <div style="margin-top: 10px;">
                    <label>Notes</label>
                    <input type="text" name="itinerary[${itineraryCounter}][notes]" placeholder="e.g., Meal included in package, Recommended restaurant">
                </div>
            `;
            
            container.appendChild(itemDiv);
        }
        
        function removeItineraryItem(id) {
            const item = document.getElementById('itinerary-' + id);
            if (item) {
                item.remove();
            }
        }
        
        // Add first itinerary item on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (spots && spots.length > 0) {
                addItineraryItem();
            }
        });
        
        // Update max day number when total days changes
        document.getElementById('tourPackage_TotalDays').addEventListener('change', function() {
            const totalDays = this.value;
            document.querySelectorAll('[name*="[day_number]"]').forEach(input => {
                input.max = totalDays;
                if (parseInt(input.value) > parseInt(totalDays)) {
                    input.value = totalDays;
                }
            });
        });
    </script>
</body>
</html>
