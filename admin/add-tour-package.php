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
    $spots_ID = $_POST['spots_ID'];
    
    // Validation
    if (empty($tourPackage_Name)) {
        $error = "Package name is required";
    } elseif (empty($tourPackage_Description)) {
        $error = "Description is required";
    } elseif (empty($tourPackage_Capacity)) {
        $error = "Capacity is required";
    } elseif (empty($tourPackage_Duration)) {
        $error = "Duration is required";
    } elseif (empty($spots_ID)) {
        $error = "Please select a tour spot";
    } else {
        $tourPackage = new TourPackage();
        $result = $tourPackage->createTourPackage($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $spots_ID);
        
        if ($result) {
            $success = "Tour package added successfully!";
            $_POST = array();
        } else {
            $error = "Failed to add tour package. Please try again.";
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
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="tourPackage_Name">Package Name <span class="required">*</span></label>
                <input type="text" id="tourPackage_Name" name="tourPackage_Name" 
                       value="<?php echo isset($_POST['tourPackage_Name']) ? htmlspecialchars($_POST['tourPackage_Name']) : ''; ?>" 
                       placeholder="e.g., Fort Pilar Historical Tour"
                       required>
            </div>
            
            <div class="form-group">
                <label for="tourPackage_Description">Description <span class="required">*</span></label>
                <textarea id="tourPackage_Description" name="tourPackage_Description" required><?php echo isset($_POST['tourPackage_Description']) ? htmlspecialchars($_POST['tourPackage_Description']) : ''; ?></textarea>
                <small>Describe what's included in this tour package</small>
            </div>
            
            <div class="form-group">
                <label for="spots_ID">Tour Spot <span class="required">*</span></label>
                <select id="spots_ID" name="spots_ID" required <?php echo empty($spots) ? 'disabled' : ''; ?>>
                    <option value="">-- Select Tour Spot --</option>
                    <?php foreach ($spots as $spot): ?>
                        <option value="<?php echo $spot['spots_ID']; ?>" 
                                <?php echo (isset($_POST['spots_ID']) && $_POST['spots_ID'] == $spot['spots_ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($spot['spots_Name']); ?> (<?php echo $spot['spots_category']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="tourPackage_Capacity">Maximum Capacity <span class="required">*</span></label>
                <input type="number" id="tourPackage_Capacity" name="tourPackage_Capacity" 
                       value="<?php echo isset($_POST['tourPackage_Capacity']) ? htmlspecialchars($_POST['tourPackage_Capacity']) : ''; ?>" 
                       placeholder="e.g., 10"
                       min="1"
                       required>
                <small>Maximum number of people per tour</small>
            </div>
            
            <div class="form-group">
                <label for="tourPackage_Duration">Duration <span class="required">*</span></label>
                <input type="text" id="tourPackage_Duration" name="tourPackage_Duration" 
                       value="<?php echo isset($_POST['tourPackage_Duration']) ? htmlspecialchars($_POST['tourPackage_Duration']) : ''; ?>" 
                       placeholder="e.g., 3 hours, Half Day, Full Day"
                       required>
                <small>How long does this tour take?</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" <?php echo empty($spots) ? 'disabled' : ''; ?>>Add Tour Package</button>
                <a href="tour-packages.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
