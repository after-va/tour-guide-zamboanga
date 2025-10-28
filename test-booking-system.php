<?php
session_start();
require_once "classes/database.php";
require_once "classes/tour-manager.php";
require_once "classes/booking-manager.php";

echo "<h1>Booking System Test</h1>";

$db = new Database();
$conn = $db->connect();
$tourManager = new TourManager();
$bookingManager = new BookingManager();

// 1. Check required tables
echo "<h2>1. Checking Required Tables...</h2>";
$requiredTables = [
    'Tour_Package',
    'Schedule',
    'Booking',
    'Payment_Info',
    'Payment_Method',
    'Payment_Transaction'
];

foreach ($requiredTables as $table) {
    $query = $conn->query("SHOW TABLES LIKE '$table'");
    if ($query->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Table $table exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table $table is missing!</p>";
    }
}

// 2. Check Payment Methods
echo "<h2>2. Checking Payment Methods...</h2>";
$query = $conn->query("SELECT * FROM Payment_Method");
$methods = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($methods) > 0) {
    echo "<p style='color: green;'>✓ Found " . count($methods) . " payment methods:</p>";
    echo "<ul>";
    foreach ($methods as $method) {
        echo "<li>" . htmlspecialchars($method['method_name']) . " (" . $method['method_type'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ No payment methods found! Need to run payment setup SQL.</p>";
}

// 3. Check Tour Packages
echo "<h2>3. Checking Tour Packages...</h2>";
$packages = $tourManager->getAllTourPackages();

if (count($packages) > 0) {
    echo "<p style='color: green;'>✓ Found " . count($packages) . " tour packages:</p>";
    echo "<ul>";
    foreach ($packages as $package) {
        echo "<li>" . htmlspecialchars($package['tourPackage_Name']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ No tour packages found! Need to create test package.</p>";
    
    // Create a test package
    echo "<p>Creating test package...</p>";
    $package_id = $tourManager->createTourPackage(
        "Test City Tour",
        "A test tour package for system verification",
        10,
        "4 hours"
    );
    
    if ($package_id) {
        echo "<p style='color: green;'>✓ Created test package with ID: $package_id</p>";
    }
}

// 4. Check Schedules
echo "<h2>4. Checking Schedules...</h2>";
$schedules = $tourManager->getAllSchedules();

if (count($schedules) > 0) {
    echo "<p style='color: green;'>✓ Found " . count($schedules) . " schedules:</p>";
    echo "<ul>";
    foreach ($schedules as $schedule) {
        echo "<li>Package: " . htmlspecialchars($schedule['tourPackage_Name']) . 
             " - Date: " . $schedule['schedule_StartDateTime'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ No schedules found! Need to create test schedule.</p>";
    
    // Create a test schedule if we have a package
    if (isset($package_id) || !empty($packages)) {
        $package_to_use = $package_id ?? $packages[0]['tourPackage_ID'];
        echo "<p>Creating test schedule...</p>";
        
        // Schedule for tomorrow
        $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));
        $schedule_id = $tourManager->createSchedule(
            $package_to_use,
            $tomorrow,
            10,
            "City Hall"
        );
        
        if ($schedule_id) {
            echo "<p style='color: green;'>✓ Created test schedule with ID: $schedule_id</p>";
        }
    }
}

// 5. Test Booking Creation
echo "<h2>5. Testing Booking Creation...</h2>";

// Get a schedule to test with
$test_schedule = $tourManager->getFirstAvailableSchedule();
if ($test_schedule) {
    echo "<p style='color: green;'>✓ Found schedule to test with: ID " . $test_schedule['schedule_ID'] . "</p>";
    
    // Test booking creation
    $result = $bookingManager->createBookingWithPayment(
        1, // Assuming admin user ID is 1
        $test_schedule['schedule_ID'],
        $test_schedule['tourPackage_ID'],
        2, // 2 persons
        1000.00, // PHP 1,000
        1 // Assuming payment method ID 1 exists
    );
    
    if ($result) {
        echo "<p style='color: green;'>✓ Successfully created test booking!</p>";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create test booking</p>";
    }
} else {
    echo "<p style='color: red;'>✗ No available schedule to test booking with</p>";
}

echo "<h2>Test Complete!</h2>";
echo "<p>Check the results above to see what components need to be fixed or set up.</p>";