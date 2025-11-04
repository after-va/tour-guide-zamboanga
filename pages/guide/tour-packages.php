<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tour Guide') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended'){
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending'){
    header('Location: account-pending.php');
}

require_once "../../classes/guide.php";

$guideObj = new Guide();

$guide_ID = $guideObj->getGuide_ID($_SESSION['user']['account_ID']);
$packages = $guideObj->viewPackageByGuideID($guide_ID);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Tour Packages</title>
</head>
<body>
    <h1>Manage Tour Packages</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="booking.php">Bookings</a> |
        <a href="tour-packages.php">Tour Packages</a> |
        <a href="schedules.php">Schedules</a> |
        <a href="payments.php">Payments</a> |
        <a href="account-change.php">Change to Tourist</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <h2>All Tour Packages</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <p><a href="tour-packages-add.php">Add New Package</a></p>
    
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Package Name</th>
            <th>Description</th>
            <th>Schedule Days</th>
            <th>Max People</th>
            <th>Min People</th>
            <th>Base Amount</th>
            <th>Discount</th>
            <th>Tour Spots</th>
            <th>Actions</th>
        </tr>
        <?php $no = 1; foreach ($packages as $package){ 
            $schedule = $guideObj->getScheduleByID($package['schedule_ID']);
            $people = $guideObj->getPeopleByID($schedule['numberofpeople_ID']);
            $pricing = $guideObj->getPricingByID($people['pricing_ID']);
            $spots = $guideObj->getSpotsByPackage($package['tourpackage_ID']);
            $spotNames = array_map(function($spot) {
                return $spot['spots_name'];
            }, $spots);
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $package['tourpackage_name']; ?></td>
            <td><?= $package['tourpackage_desc']; ?></td>
            <td><?= $schedule['schedule_days']; ?></td>
            <td><?= $people['numberofpeople_maximum']; ?></td>
            <td><?= $people['numberofpeople_based']; ?></td>
            <td><?= $pricing['pricing_currency'] . ' ' . number_format($pricing['pricing_foradult'], 2); ?></td>
            <td><?= $pricing['pricing_currency'] . ' ' . number_format($pricing['pricing_discount'], 2); ?></td>
            <td><?= implode(', ', $spotNames); ?></td>
            <td>
                <a href="tour-packages-edit.php?id=<?= $package['tourpackage_ID']; ?>">Edit</a> |
                <a href="tour-packages-delete.php?id=<?= $package['tourpackage_ID']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>