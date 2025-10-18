<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/TourSpot.php";

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $spots_Name = trim($_POST['spots_Name']);
    $spots_Description = trim($_POST['spots_Description']);
    $spots_category = trim($_POST['spots_category']);
    $spots_Address = trim($_POST['spots_Address']);
    $spots_GoogleLink = trim($_POST['spots_GoogleLink']);
    
    // Validation
    if (empty($spots_Name)) {
        $error = "Tour spot name is required";
    } elseif (empty($spots_Description)) {
        $error = "Description is required";
    } elseif (empty($spots_category)) {
        $error = "Category is required";
    } elseif (empty($spots_Address)) {
        $error = "Address is required";
    } else {
        $tourSpot = new TourSpot();
        $result = $tourSpot->createTourSpot($spots_Name, $spots_Description, $spots_category, $spots_Address, $spots_GoogleLink);
        
        if ($result) {
            $success = "Tour spot added successfully!";
            // Clear form
            $_POST = array();
        } else {
            $error = "Failed to add tour spot. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Tour Spot - Admin</title>
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
                       value="<?php echo isset($_POST['spots_Name']) ? htmlspecialchars($_POST['spots_Name']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="spots_Description">Description <span class="required">*</span></label>
                <textarea id="spots_Description" name="spots_Description" required><?php echo isset($_POST['spots_Description']) ? htmlspecialchars($_POST['spots_Description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="spots_category">Category <span class="required">*</span></label>
                <select id="spots_category" name="spots_category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Historical" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Historical') ? 'selected' : ''; ?>>Historical</option>
                    <option value="Beach" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Beach') ? 'selected' : ''; ?>>Beach</option>
                    <option value="Nature" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Nature') ? 'selected' : ''; ?>>Nature</option>
                    <option value="Cultural" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Cultural') ? 'selected' : ''; ?>>Cultural</option>
                    <option value="Religious" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Religious') ? 'selected' : ''; ?>>Religious</option>
                    <option value="Adventure" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Adventure') ? 'selected' : ''; ?>>Adventure</option>
                    <option value="Food & Dining" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Food & Dining') ? 'selected' : ''; ?>>Food & Dining</option>
                    <option value="Shopping" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Shopping') ? 'selected' : ''; ?>>Shopping</option>
                    <option value="Entertainment" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Entertainment') ? 'selected' : ''; ?>>Entertainment</option>
                    <option value="Other" <?php echo (isset($_POST['spots_category']) && $_POST['spots_category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="spots_Address">Address <span class="required">*</span></label>
                <input type="text" id="spots_Address" name="spots_Address" 
                       value="<?php echo isset($_POST['spots_Address']) ? htmlspecialchars($_POST['spots_Address']) : ''; ?>" 
                       placeholder="e.g., Paseo del Mar, Zamboanga City"
                       required>
            </div>
            
            <div class="form-group">
                <label for="spots_GoogleLink">Google Maps Link (Optional)</label>
                <input type="url" id="spots_GoogleLink" name="spots_GoogleLink" 
                       value="<?php echo isset($_POST['spots_GoogleLink']) ? htmlspecialchars($_POST['spots_GoogleLink']) : ''; ?>" 
                       placeholder="https://maps.google.com/...">
                <small style="color: #666;">Paste the full Google Maps URL here</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Add Tour Spot</button>
                <a href="tour-spots.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
