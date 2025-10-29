<?php
session_start();


require_once "../../classes/tour-manager.php";

$spotsObj = new TourManager();

$spots = [];
$success = "";
$error = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $spots["spots_name"] = trim(htmlspecialchars($_POST['spots_name']));
    $spots["spots_description"] = trim(htmlspecialchars($_POST['spots_description']));
    $spots["spots_category"] = trim(htmlspecialchars($_POST['spots_category']));
    $spots["spots_address"] = trim(htmlspecialchars($_POST['spots_address']));
    $spots["spots_googlelink"] = trim(htmlspecialchars($_POST['spots_googlelink']));

    // $spots_name = trim($_POST['spots_name']);
    // $spots_description = trim($_POST['spots_description']);
    // $spots_category = trim($_POST['spots_category']);
    // $spots_address = trim($_POST['spots_address']);
    // $spots_googlelink = trim($_POST['spots_googlelink']);
    
    
    if (empty($spots["spots_name"])) {
        $error["spots_name"] = "Tour spot name is required";
    } elseif (empty($spots["spots_description"])) {
        $error["spots_description"] = "Description is required";
    } elseif (empty($spots["spots_category"])) {
        $error["spots_category"] = "Category is required";
    } elseif (empty($spots["spots_address"])) {
        $error["spots_address"] = "Address is required";
    }

    if(empty(array_filter($error))){
        $results = $spotsObj->addTourSpots($spots["spots_name"], $spots["spots_description"], $spots["spots_category"], $spots["spots_address"], $spots["spots_googlelink"]);
        
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
                <label for="spots_name">Spot Name <span class="required">*</span></label>
                <input type="text" id="spots_name" name="spots_name" 
                       value="<?= $spots["spots_name"] ?? "" ?>" 
                       maxlength="225"
                       required>
                <div class="char-counter" id="name-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_description">Description <span class="required">*</span></label>
                <textarea id="spots_description" name="spots_description" maxlength="225" required><?= $spots["spots_description"] ?? "" ?></textarea>
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
                <label for="spots_address">Address <span class="required">*</span></label>
                <input type="text" id="spots_address" name="spots_address" 
                       value="<?= isset($spots["spots_address"]) ? htmlspecialchars($spots["spots_address"]) : ''; ?>" 
                       placeholder="e.g., Paseo del Mar, Zamboanga City"
                       maxlength="225"
                       required>
                <div class="char-counter" id="address-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_googlelink">Google Maps Link (Optional)</label>
                <input type="url" id="spots_googlelink" name="spots_googlelink" 
                       value="<?= isset($spots["spots_googlelink"]) ? htmlspecialchars($spots["spots_googlelink"]) : ''; ?>" 
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
            updateCharCounter('spots_name', 'name-counter', 225);
            updateCharCounter('spots_description', 'description-counter', 225);
            updateCharCounter('spots_address', 'address-counter', 225);
            updateCharCounter('spots_googlelink', 'link-counter', 500);
        });
    </script>
</body>
</html>

<!-- $spots["spots_name"]
$spots["spots_description"]
$spots["spots_category"]
$spots["spots_address"]
$spots["spots_googlelink"] -->