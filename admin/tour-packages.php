<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

require_once "../php/TourPackage.php";

$tourPackage = new TourPackage();
$packages = $tourPackage->getAllTourPackages();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Tour Packages - Admin</title>
</head>
<body>
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
    
    <hr>
    
    <h2>All Tour Packages</h2>
    <p><a href="add-tour-package.php">Add New Package</a></p>
    
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Package Name</th>
            <th>Description</th>
            <th>Tour Spots</th>
            <th>Capacity</th>
            <th>Duration</th>
            <th>Actions</th>
        </tr>
        <?php $no = 1; foreach ($packages as $p): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($p['tourPackage_Name']); ?></td>
            <td><?= htmlspecialchars($p['tourPackage_Description']); ?></td>
            <td>
                <?php if (!empty($p['spots'])): ?>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($p['spots'] as $spot): ?>
                            <li><?= htmlspecialchars($spot['spots_Name']); ?> <em style="color: #666;">(<?= $spot['spots_category']; ?>)</em></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <em style="color: #999;">No spots assigned</em>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['tourPackage_Capacity']); ?></td>
            <td><?= htmlspecialchars($p['tourPackage_Duration']); ?></td>
            <td>
                <a href="edit-tour-package.php?id=<?= $p['tourPackage_ID']; ?>">Edit</a> |
                <a href="delete-tour-package.php?id=<?= $p['tourPackage_ID']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
