<?php
session_start();


require_once "../../classes/tour-manager.php";

$success = "";
$error = "";
$spot = null;

// Get spot ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: tour-spots.php");
    exit();
}

$spots_ID = intval($_GET['id']);
$tourSpot = new TourManager();

// Fetch existing spot data
$spot = $tourSpot->getTourSpotById($spots_ID);

if (!$spot) {
    $_SESSION['error'] = "Tour spot not found.";
    header("Location: tour-spots.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $spots_name = trim($_POST['spots_name']);
    $spots_description = trim($_POST['spots_description']);
    $spots_category = trim($_POST['spots_category']);
    $spots_address = trim($_POST['spots_address']);
    $spots_googlelink = trim($_POST['spots_googlelink']);
    
    if (empty($spots_name)) {
        $error = "Tour spot name is required";
    } elseif (empty($spots_description)) {
        $error = "Description is required";
    } elseif (empty($spots_category)) {
        $error = "Category is required";
    } elseif (empty($spots_address)) {
        $error = "Address is required";
    } else {
        // Validate field lengths
        if (strlen($spots_name) > 225) {
            $error = "Tour spot name is too long (max 225 characters). Current: " . strlen($spots_name);
        } elseif (strlen($spots_description) > 225) {
            $error = "Description is too long (max 225 characters). Current: " . strlen($spots_description);
        } elseif (strlen($spots_category) > 225) {
            $error = "Category is too long (max 225 characters). Current: " . strlen($spots_category);
        } elseif (strlen($spots_address) > 225) {
            $error = "Address is too long (max 225 characters). Current: " . strlen($spots_address);
        } elseif (strlen($spots_googlelink) > 500) {
            $error = "Google Link is too long (max 500 characters). Current: " . strlen($spots_googlelink);
        } else {
            $result = $tourSpot->updateTourSpot($spots_ID, $spots_name, $spots_description, $spots_category, $spots_address, $spots_googlelink);
            
            if ($result) {
                $_SESSION['success'] = "Tour spot updated successfully!";
                header("Location: tour-spots.php");
                exit();
            } else {
                $error = "Failed to update tour spot. Please try again.";
            }
        }
    }
    
    // Update spot data with posted values for display
    $spot['spots_name'] = $spots_name;
    $spot['spots_description'] = $spots_description;
    $spot['spots_category'] = $spots_category;
    $spot['spots_address'] = $spots_address;
    $spot['spots_googlelink'] = $spots_googlelink;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Tour Spot - Admin</title>
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
    <h1>Edit Tour Spot</h1>
    
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
                       value="<?php echo htmlspecialchars($spot['spots_name']); ?>" 
                       maxlength="225"
                       required>
                <div class="char-counter" id="name-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_description">Description <span class="required">*</span></label>
                <textarea id="spots_description" name="spots_description" maxlength="225" required><?php echo htmlspecialchars($spot['spots_description']); ?></textarea>
                <div class="char-counter" id="description-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_category">Category <span class="required">*</span></label>
                <select id="spots_category" name="spots_category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Historical" <?php echo ($spot['spots_category'] == 'Historical') ? 'selected' : ''; ?>>Historical</option>
                    <option value="Beach" <?php echo ($spot['spots_category'] == 'Beach') ? 'selected' : ''; ?>>Beach</option>
                    <option value="Nature" <?php echo ($spot['spots_category'] == 'Nature') ? 'selected' : ''; ?>>Nature</option>
                    <option value="Cultural" <?php echo ($spot['spots_category'] == 'Cultural') ? 'selected' : ''; ?>>Cultural</option>
                    <option value="Religious" <?php echo ($spot['spots_category'] == 'Religious') ? 'selected' : ''; ?>>Religious</option>
                    <option value="Adventure" <?php echo ($spot['spots_category'] == 'Adventure') ? 'selected' : ''; ?>>Adventure</option>
                    <option value="Food & Dining" <?php echo ($spot['spots_category'] == 'Food & Dining') ? 'selected' : ''; ?>>Food & Dining</option>
                    <option value="Shopping" <?php echo ($spot['spots_category'] == 'Shopping') ? 'selected' : ''; ?>>Shopping</option>
                    <option value="Entertainment" <?php echo ($spot['spots_category'] == 'Entertainment') ? 'selected' : ''; ?>>Entertainment</option>
                    <option value="Other" <?php echo ($spot['spots_category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="spots_address">Address <span class="required">*</span></label>
                <input type="text" id="spots_address" name="spots_address" 
                       value="<?php echo htmlspecialchars($spot['spots_address']); ?>" 
                       placeholder="e.g., Paseo del Mar, Zamboanga City"
                       maxlength="225"
                       required>
                <div class="char-counter" id="address-counter">0 / 225 characters</div>
            </div>
            
            <div class="form-group">
                <label for="spots_googlelink">Google Maps Link (Optional)</label>
                <input type="url" id="spots_googlelink" name="spots_googlelink" 
                       value="<?php echo htmlspecialchars($spot['spots_googlelink']); ?>" 
                       placeholder="https://maps.google.com/..."
                       maxlength="500">
                <small style="color: #666;">Paste the full Google Maps URL here</small>
                <div class="char-counter" id="link-counter">0 / 500 characters</div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Tour Spot</button>
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
