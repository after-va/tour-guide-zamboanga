<?php
/**
 * Test Operations for Tour Guide System
 * This file tests basic operations of all classes
 */

require_once "php/Database.php";
require_once "php/User.php";
require_once "php/Tourist.php";
require_once "php/Guide.php";
require_once "php/TourSpot.php";
require_once "php/TourPackage.php";
require_once "php/Schedule.php";
require_once "php/Booking.php";
require_once "php/Payment.php";
require_once "php/Rating.php";
require_once "php/Notification.php";

echo "<h1>Tour Guide System - Operation Tests</h1>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $db = new Database();
    $conn = $db->connect();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test 2: User Login (Admin)
echo "<h2>Test 2: User Login Test</h2>";
try {
    $user = new User();
    $result = $user->login('admin@tourismozamboanga.com', 'admin123');
    if ($result) {
        echo "<p style='color: green;'>✓ Admin login successful! User ID: " . $result['person_ID'] . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Admin login failed (user may not exist yet)</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Login test error: " . $e->getMessage() . "</p>";
}

// Test 3: Fetch Country Codes
echo "<h2>Test 3: Fetch Country Codes</h2>";
try {
    $tourist = new Tourist();
    $countryCodes = $tourist->fetchCountryCode();
    echo "<p style='color: green;'>✓ Fetched " . count($countryCodes) . " country codes</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Country code fetch error: " . $e->getMessage() . "</p>";
}

// Test 4: Get All Tour Spots
echo "<h2>Test 4: Get All Tour Spots</h2>";
try {
    $tourSpot = new TourSpot();
    $spots = $tourSpot->getAllTourSpots();
    echo "<p style='color: green;'>✓ Fetched " . count($spots) . " tour spots</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Tour spots fetch error: " . $e->getMessage() . "</p>";
}

// Test 5: Get All Tour Packages
echo "<h2>Test 5: Get All Tour Packages</h2>";
try {
    $tourPackage = new TourPackage();
    $packages = $tourPackage->getAllTourPackages();
    echo "<p style='color: green;'>✓ Fetched " . count($packages) . " tour packages</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Tour packages fetch error: " . $e->getMessage() . "</p>";
}

// Test 6: Get All Schedules
echo "<h2>Test 6: Get All Schedules</h2>";
try {
    $schedule = new Schedule();
    $schedules = $schedule->getAllSchedules();
    echo "<p style='color: green;'>✓ Fetched " . count($schedules) . " schedules</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Schedules fetch error: " . $e->getMessage() . "</p>";
}

// Test 7: Get Available Schedules
echo "<h2>Test 7: Get Available Schedules</h2>";
try {
    $schedule = new Schedule();
    $availableSchedules = $schedule->getAvailableSchedules();
    echo "<p style='color: green;'>✓ Fetched " . count($availableSchedules) . " available schedules</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Available schedules fetch error: " . $e->getMessage() . "</p>";
}

// Test 8: Get All Bookings
echo "<h2>Test 8: Get All Bookings</h2>";
try {
    $booking = new Booking();
    $bookings = $booking->getAllBookings();
    echo "<p style='color: green;'>✓ Fetched " . count($bookings) . " bookings</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Bookings fetch error: " . $e->getMessage() . "</p>";
}

// Test 9: Get All Users
echo "<h2>Test 9: Get All Users</h2>";
try {
    $user = new User();
    $users = $user->getAllUsers();
    echo "<p style='color: green;'>✓ Fetched " . count($users) . " users</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Users fetch error: " . $e->getMessage() . "</p>";
}

// Test 10: Get All Guides
echo "<h2>Test 10: Get All Guides</h2>";
try {
    $guide = new Guide();
    $guides = $guide->getAllGuides();
    echo "<p style='color: green;'>✓ Fetched " . count($guides) . " guides</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Guides fetch error: " . $e->getMessage() . "</p>";
}

// Test 11: Get All Tourists
echo "<h2>Test 11: Get All Tourists</h2>";
try {
    $tourist = new Tourist();
    $tourists = $tourist->getAllTourists();
    echo "<p style='color: green;'>✓ Fetched " . count($tourists) . " tourists</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Tourists fetch error: " . $e->getMessage() . "</p>";
}

// Test 12: Get Payment Methods
echo "<h2>Test 12: Get Payment Methods</h2>";
try {
    $payment = new Payment();
    $methods = $payment->getAllPaymentMethods();
    echo "<p style='color: green;'>✓ Fetched " . count($methods) . " payment methods</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Payment methods fetch error: " . $e->getMessage() . "</p>";
}

// Test 13: Get All Payments
echo "<h2>Test 13: Get All Payments</h2>";
try {
    $payment = new Payment();
    $payments = $payment->getAllPayments();
    echo "<p style='color: green;'>✓ Fetched " . count($payments) . " payments</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Payments fetch error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>All basic operations have been tested. Check the results above.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Make sure the database 'tour' exists and tables are created using tourguidesystem.sql</li>";
echo "<li>Import country codes using tourguidesystem-data.sql</li>";
echo "<li>Access admin panel: <a href='admin/'>admin/</a></li>";
echo "<li>Access tourist panel: <a href='tourist/'>tourist/</a></li>";
echo "<li>Access guide panel: <a href='guide/'>guide/</a></li>";
echo "</ul>";
?>
