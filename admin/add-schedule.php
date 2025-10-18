<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/Schedule.php";
require_once "../php/TourPackage.php";
require_once "../php/User.php";
require_once "../php/Database.php";

$success = "";
$error = "";

// Get all tour packages for dropdown
$tourPackage = new TourPackage();
$packages = $tourPackage->getAllTourPackages();

// Get verified tour guides only
$db = new Database();
$conn = $db->connect();
$guideSql = "SELECT p.person_ID, CONCAT(n.name_first, ' ', n.name_last) as full_name,
                    gc.status as certification_status
             FROM Person p
             INNER JOIN Name_Info n ON p.name_ID = n.name_ID
             INNER JOIN Role_Info r ON p.role_ID = r.role_ID
             LEFT JOIN Guide_Certification gc ON p.person_ID = gc.guide_ID
             WHERE r.role_name = 'Tour Guide'
             AND (gc.status = 'verified' OR gc.status IS NULL)
             GROUP BY p.person_ID
             ORDER BY n.name_first ASC";
$guideQuery = $conn->prepare($guideSql);
$guideQuery->execute();
$guides = $guideQuery->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tourPackage_ID = $_POST['tourPackage_ID'];
    $guide_ID = $_POST['guide_ID'];
    $schedule_StartDateTime = $_POST['schedule_StartDateTime'];
    $schedule_EndDateTime = $_POST['schedule_EndDateTime'];
    $schedule_Capacity = $_POST['schedule_Capacity'];
    $schedule_MeetingSpot = trim($_POST['schedule_MeetingSpot']);
    
    // Validation
    if (empty($tourPackage_ID)) {
        $error = "Please select a tour package";
    } elseif (empty($guide_ID)) {
        $error = "Please select a tour guide";
    } elseif (empty($schedule_StartDateTime)) {
        $error = "Start date and time is required";
    } elseif (empty($schedule_EndDateTime)) {
        $error = "End date and time is required";
    } elseif (empty($schedule_Capacity)) {
        $error = "Capacity is required";
    } elseif (empty($schedule_MeetingSpot)) {
        $error = "Meeting spot is required";
    } elseif (strtotime($schedule_StartDateTime) <= time()) {
        $error = "Start date must be in the future";
    } elseif (strtotime($schedule_EndDateTime) <= strtotime($schedule_StartDateTime)) {
        $error = "End date must be after start date";
    } else {
        $schedule = new Schedule();
        $result = $schedule->createSchedule($tourPackage_ID, $guide_ID, $schedule_StartDateTime, $schedule_EndDateTime, $schedule_Capacity, $schedule_MeetingSpot);
        
        if ($result) {
            $success = "Schedule added successfully!";
            $_POST = array();
        } else {
            $error = "Failed to add schedule. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Schedule - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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
        .alert {
            padding: 12px;
            margin-bottom: 15px;
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
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
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
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>Add New Schedule</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="bookings.php">Bookings</a> |
        <a href="users.php">Users</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="tour-spots.php">Tour Spots</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="guide-applications.php">Guide Applications</a> |
        <a href="logout.php">Logout</a>
    </nav>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (empty($packages)): ?>
            <div class="alert alert-error">
                <strong>No tour packages available!</strong> Please <a href="add-tour-package.php">add a tour package</a> first.
            </div>
        <?php endif; ?>
        
        <?php if (empty($guides)): ?>
            <div class="alert alert-warning">
                <strong>No verified tour guides available!</strong> Please verify tour guide applications in <a href="guide-applications.php">Guide Applications</a>.
            </div>
        <?php endif; ?>
        
        <div class="info-box">
            <strong>Note:</strong> Only verified tour guides can be assigned to schedules. Make sure to approve guide applications first.
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="tourPackage_ID">Tour Package <span class="required">*</span></label>
                <select id="tourPackage_ID" name="tourPackage_ID" required <?php echo empty($packages) ? 'disabled' : ''; ?>>
                    <option value="">-- Select Tour Package --</option>
                    <?php foreach ($packages as $pkg): ?>
                        <option value="<?php echo $pkg['tourPackage_ID']; ?>" 
                                <?php echo (isset($_POST['tourPackage_ID']) && $_POST['tourPackage_ID'] == $pkg['tourPackage_ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pkg['tourPackage_Name']); ?> - <?php echo htmlspecialchars($pkg['spots_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="guide_ID">Tour Guide <span class="required">*</span></label>
                <select id="guide_ID" name="guide_ID" required <?php echo empty($guides) ? 'disabled' : ''; ?>>
                    <option value="">-- Select Tour Guide --</option>
                    <?php foreach ($guides as $guide): ?>
                        <option value="<?php echo $guide['person_ID']; ?>" 
                                <?php echo (isset($_POST['guide_ID']) && $_POST['guide_ID'] == $guide['person_ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($guide['full_name']); ?>
                            <?php if ($guide['certification_status'] == 'verified'): ?>
                                ✓ Verified
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>Only verified tour guides are shown</small>
            </div>
            
            <div class="form-group">
                <label for="schedule_StartDateTime">Start Date & Time <span class="required">*</span></label>
                <input type="datetime-local" id="schedule_StartDateTime" name="schedule_StartDateTime" 
                       value="<?php echo isset($_POST['schedule_StartDateTime']) ? $_POST['schedule_StartDateTime'] : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="schedule_EndDateTime">End Date & Time <span class="required">*</span></label>
                <input type="datetime-local" id="schedule_EndDateTime" name="schedule_EndDateTime" 
                       value="<?php echo isset($_POST['schedule_EndDateTime']) ? $_POST['schedule_EndDateTime'] : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="schedule_Capacity">Capacity <span class="required">*</span></label>
                <input type="number" id="schedule_Capacity" name="schedule_Capacity" 
                       value="<?php echo isset($_POST['schedule_Capacity']) ? htmlspecialchars($_POST['schedule_Capacity']) : ''; ?>" 
                       placeholder="e.g., 10"
                       min="1"
                       required>
                <small>Maximum number of people for this schedule</small>
            </div>
            
            <div class="form-group">
                <label for="schedule_MeetingSpot">Meeting Spot <span class="required">*</span></label>
                <input type="text" id="schedule_MeetingSpot" name="schedule_MeetingSpot" 
                       value="<?php echo isset($_POST['schedule_MeetingSpot']) ? htmlspecialchars($_POST['schedule_MeetingSpot']) : ''; ?>" 
                       placeholder="e.g., Fort Pilar Main Entrance"
                       required>
                <small>Where tourists should meet the guide</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" <?php echo (empty($packages) || empty($guides)) ? 'disabled' : ''; ?>>Add Schedule</button>
                <a href="schedules.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
