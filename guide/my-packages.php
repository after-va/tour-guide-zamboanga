<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

require_once "../php/CustomPackage.php";
require_once "../php/TourPackage.php";

$customPackage = new CustomPackage();
$tourPackage = new TourPackage();

$myOfferings = $customPackage->getOfferingsByGuide($_SESSION['user_id']);
$allPackages = $tourPackage->getAllTourPackages();

// Handle form submission for creating new offering
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_offering') {
    $data = [
        'offering_price' => $_POST['offering_price'],
        'price_per_person' => $_POST['price_per_person'],
        'min_pax' => $_POST['min_pax'],
        'max_pax' => $_POST['max_pax'],
        'is_customizable' => isset($_POST['is_customizable']) ? 1 : 0,
        'availability_notes' => $_POST['availability_notes']
    ];
    
    if ($customPackage->createGuideOffering($_SESSION['user_id'], $_POST['tourPackage_ID'], $data)) {
        $success = "Package offering created successfully!";
        $myOfferings = $customPackage->getOfferingsByGuide($_SESSION['user_id']);
    } else {
        $error = "Failed to create package offering.";
    }
}

// Handle update offering
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_offering') {
    $data = [
        'offering_price' => $_POST['offering_price'],
        'price_per_person' => $_POST['price_per_person'],
        'min_pax' => $_POST['min_pax'],
        'max_pax' => $_POST['max_pax'],
        'is_customizable' => isset($_POST['is_customizable']) ? 1 : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'availability_notes' => $_POST['availability_notes']
    ];
    
    if ($customPackage->updateGuideOffering($_POST['offering_ID'], $_SESSION['user_id'], $data)) {
        $success = "Package offering updated successfully!";
        $myOfferings = $customPackage->getOfferingsByGuide($_SESSION['user_id']);
    } else {
        $error = "Failed to update package offering.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Packages - Tour Guide System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn { padding: 8px 15px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; 
                 width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; 
                         border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; }
        .badge-active { background: #28a745; color: white; }
        .badge-inactive { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <h1>My Package Offerings</h1>
    <p>Welcome, <?php echo $_SESSION['full_name']; ?>!</p>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="my-schedules.php">My Schedules</a>
        <a href="my-bookings.php">My Bookings</a>
        <a href="my-packages.php">My Packages</a>
        <a href="package-requests.php">Package Requests</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <button class="btn btn-primary" onclick="document.getElementById('createModal').style.display='block'">
        + Create New Package Offering
    </button>
    
    <h2>My Package Offerings</h2>
    <?php if (count($myOfferings) > 0): ?>
        <table>
            <tr>
                <th>Package Name</th>
                <th>Duration</th>
                <th>Base Price</th>
                <th>Per Person</th>
                <th>PAX Range</th>
                <th>Customizable</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($myOfferings as $offering): ?>
            <tr>
                <td><?php echo htmlspecialchars($offering['tourPackage_Name']); ?></td>
                <td><?php echo htmlspecialchars($offering['tourPackage_Duration']); ?></td>
                <td>₱<?php echo number_format($offering['offering_price'], 2); ?></td>
                <td>₱<?php echo number_format($offering['price_per_person'], 2); ?></td>
                <td><?php echo $offering['min_pax']; ?> - <?php echo $offering['max_pax']; ?></td>
                <td><?php echo $offering['is_customizable'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <span class="badge <?php echo $offering['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                        <?php echo $offering['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td>
                    <button class="btn btn-warning" onclick="editOffering(<?php echo htmlspecialchars(json_encode($offering)); ?>)">
                        Edit
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You haven't created any package offerings yet. Click "Create New Package Offering" to get started!</p>
    <?php endif; ?>
    
    <!-- Create Offering Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('createModal').style.display='none'">&times;</span>
            <h2>Create Package Offering</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create_offering">
                
                <div class="form-group">
                    <label>Select Tour Package:</label>
                    <select name="tourPackage_ID" required>
                        <option value="">-- Select Package --</option>
                        <?php foreach ($allPackages as $pkg): ?>
                            <option value="<?php echo $pkg['tourPackage_ID']; ?>">
                                <?php echo htmlspecialchars($pkg['tourPackage_Name']); ?> 
                                (<?php echo $pkg['tourPackage_Duration']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Base Price (₱):</label>
                    <input type="number" name="offering_price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Price Per Person (₱):</label>
                    <input type="number" name="price_per_person" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Minimum PAX:</label>
                    <input type="number" name="min_pax" value="1" required>
                </div>
                
                <div class="form-group">
                    <label>Maximum PAX:</label>
                    <input type="number" name="max_pax" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_customizable" checked>
                        Allow tourists to request customizations
                    </label>
                </div>
                
                <div class="form-group">
                    <label>Availability Notes:</label>
                    <textarea name="availability_notes" rows="3" placeholder="e.g., Available weekends only, Requires 3 days advance booking"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Create Offering</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Offering Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
            <h2>Edit Package Offering</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update_offering">
                <input type="hidden" name="offering_ID" id="edit_offering_ID">
                
                <div class="form-group">
                    <label>Package:</label>
                    <input type="text" id="edit_package_name" readonly style="background: #f0f0f0;">
                </div>
                
                <div class="form-group">
                    <label>Base Price (₱):</label>
                    <input type="number" name="offering_price" id="edit_offering_price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Price Per Person (₱):</label>
                    <input type="number" name="price_per_person" id="edit_price_per_person" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Minimum PAX:</label>
                    <input type="number" name="min_pax" id="edit_min_pax" required>
                </div>
                
                <div class="form-group">
                    <label>Maximum PAX:</label>
                    <input type="number" name="max_pax" id="edit_max_pax" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_customizable" id="edit_is_customizable">
                        Allow tourists to request customizations
                    </label>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" id="edit_is_active">
                        Active (visible to tourists)
                    </label>
                </div>
                
                <div class="form-group">
                    <label>Availability Notes:</label>
                    <textarea name="availability_notes" id="edit_availability_notes" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Update Offering</button>
            </form>
        </div>
    </div>
    
    <script>
        function editOffering(offering) {
            document.getElementById('edit_offering_ID').value = offering.offering_ID;
            document.getElementById('edit_package_name').value = offering.tourPackage_Name;
            document.getElementById('edit_offering_price').value = offering.offering_price;
            document.getElementById('edit_price_per_person').value = offering.price_per_person;
            document.getElementById('edit_min_pax').value = offering.min_pax;
            document.getElementById('edit_max_pax').value = offering.max_pax;
            document.getElementById('edit_is_customizable').checked = offering.is_customizable == 1;
            document.getElementById('edit_is_active').checked = offering.is_active == 1;
            document.getElementById('edit_availability_notes').value = offering.availability_notes || '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
