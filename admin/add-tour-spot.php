<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/TourSpot.php";

$spotsObj = new TourSpot();

$spots = [];
$success = "";
$error = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $spots["spots_Name"] = trim(htmlspecialchars($_POST['spots_Name']));
    $spots["spots_Description"] = trim(htmlspecialchars($_POST['spots_Description']));
    $spots["spots_category"] = trim(htmlspecialchars($_POST['spots_category']));
    $spots["spots_Address"] = trim(htmlspecialchars($_POST['spots_Address']));
    $spots["spots_GoogleLink"] = trim(htmlspecialchars($_POST['spots_GoogleLink']));

    // $spots_Name = trim($_POST['spots_Name']);
    // $spots_Description = trim($_POST['spots_Description']);
    // $spots_category = trim($_POST['spots_category']);
    // $spots_Address = trim($_POST['spots_Address']);
    // $spots_GoogleLink = trim($_POST['spots_GoogleLink']);
    
    
    if (empty($spots["spots_Name"])) {
        $error["spots_Name"] = "Tour spot name is required";
    } elseif (empty($spots["spots_Description"])) {
        $error["spots_Description"] = "Description is required";
    } elseif (empty($spots["spots_category"])) {
        $error["spots_category"] = "Category is required";
    } elseif (empty($spots["spots_Address"])) {
        $error["spots_Address"] = "Address is required";
    }

    if(empty(array_filter($error))){
        $tourSpot = new TourSpot();
        $results = $spotsObj->createTourSpot($spots["spots_Name"], $spots["spots_Description"], $spots["spots_category"], $spots["spots_Address"], $spots["spots_GoogleLink"]);
        
        if ($results !== false) {
            header("Location: tour-spots.php");
            exit();
        } else {
           echo "Failed to add tour spot.";
        }
    }
    
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Tour Spot - Admin</title>
    <style>
        .char-counter {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .char-counter.warning {
            color: #ff9800;
        }
        .char-counter.error {
            color: #f44336;
        }
    </style>
</head>
<body>
    <h1>Add New Tour Spot</h1>
    
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
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="spots_Name">Spot Name <span class="required">*</span></label>
                <input type="text" id="spots_Name" name="spots_Name" 
                       value="<?= $spots["spots_Name"] ?? "" ?>" 
                       maxlength="225"
                       required>
                <div class="char-counter" id="name-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_Description">Description <span class="required">*</span></label>
                <textarea id="spots_Description" name="spots_Description" maxlength="225" required><?= $spots["spots_Description"] ?? "" ?></textarea>
                <div class="char-counter" id="description-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_category">Category <span class="required">*</span></label>
                <select id="spots_category" name="spots_category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Historical" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Historical') ? 'selected' : ''; ?>>Historical</option>
                    <option value="Beach" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Beach') ? 'selected' : ''; ?>>Beach</option>
                    <option value="Nature" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Nature') ? 'selected' : ''; ?>>Nature</option>
                    <option value="Cultural" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Cultural') ? 'selected' : ''; ?>>Cultural</option>
                    <option value="Religious" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Religious') ? 'selected' : ''; ?>>Religious</option>
                    <option value="Adventure" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Adventure') ? 'selected' : ''; ?>>Adventure</option>
                    <option value="Food & Dining" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Food & Dining') ? 'selected' : ''; ?>>Food & Dining</option>
                    <option value="Shopping" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Shopping') ? 'selected' : ''; ?>>Shopping</option>
                    <option value="Entertainment" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Entertainment') ? 'selected' : ''; ?>>Entertainment</option>
                    <option value="Other" <?= (isset($spots["spots_category"]) && $spots["spots_category"] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="spots_Address">Address <span class="required">*</span></label>
                <input type="text" id="spots_Address" name="spots_Address" 
                       value="<?= isset($spots["spots_Address"]) ? htmlspecialchars($spots["spots_Address"]) : ''; ?>" 
                       placeholder="e.g., Paseo del Mar, Zamboanga City"
                       maxlength="225"
                       required>
                <div class="char-counter" id="address-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_GoogleLink">Google Maps Link (Optional)</label>
                <input type="url" id="spots_GoogleLink" name="spots_GoogleLink" 
                       value="<?= isset($spots["spots_GoogleLink"]) ? htmlspecialchars($spots["spots_GoogleLink"]) : ''; ?>" 
                       placeholder="https://maps.google.com/..."
                       maxlength="500">
                <small style="color: #666;">Paste the full Google Maps URL here</small>
                <div class="char-counter" id="link-counter">0 / 500 characters</div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Add Tour Spot</button>
                <a href="tour-spots.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
        // Character counter function
        function updateCharCounter(inputId, counterId, maxLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            
            function update() {
                const length = input.value.length;
                counter.textContent = length + ' / ' + maxLength + ' characters';
                
                // Add warning/error classes
                counter.classList.remove('warning', 'error');
                if (length > maxLength * 0.9) {
                    counter.classList.add('warning');
                }
                if (length >= maxLength) {
                    counter.classList.add('error');
                }
            }
            
            input.addEventListener('input', update);
            input.addEventListener('keyup', update);
            update(); // Initial update
        }
        
        // Initialize character counters
        document.addEventListener('DOMContentLoaded', function() {
            updateCharCounter('spots_Name', 'name-counter', 225);
            updateCharCounter('spots_Description', 'description-counter', 225);
            updateCharCounter('spots_Address', 'address-counter', 225);
            updateCharCounter('spots_GoogleLink', 'link-counter', 500);
        });
    </script>
</body>
</html>

<!-- $spots["spots_Name"]
$spots["spots_Description"]
$spots["spots_category"]
$spots["spots_Address"]
$spots["spots_GoogleLink"] -->