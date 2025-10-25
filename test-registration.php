<?php
// Test Registration Setup
require_once __DIR__ . "/php/Database.php";
require_once __DIR__ . "/php/Tourist.php";

$db = new Database();
$conn = $db->connect();

echo "<h1>Registration System Test</h1>";

// Check if Country Codes exist
echo "<h2>1. Country Codes Check</h2>";
$countrySql = "SELECT * FROM Country_Code";
$countryQuery = $conn->prepare($countrySql);
$countryQuery->execute();
$countries = $countryQuery->fetchAll();

if (count($countries) > 0) {
    echo "<p style='color: green;'>✓ Country codes found: " . count($countries) . "</p>";
    echo "<ul>";
    foreach ($countries as $c) {
        echo "<li>{$c['countrycode_name']} ({$c['countrycode_number']})</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ No country codes found! Run setup-initial-data.sql</p>";
}

// Check if Roles exist
echo "<h2>2. Roles Check</h2>";
$roleSql = "SELECT * FROM Role_Info";
$roleQuery = $conn->prepare($roleSql);
$roleQuery->execute();
$roles = $roleQuery->fetchAll();

if (count($roles) > 0) {
    echo "<p style='color: green;'>✓ Roles found: " . count($roles) . "</p>";
    echo "<ul>";
    foreach ($roles as $r) {
        echo "<li>Role ID {$r['role_ID']}: {$r['role_name']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ No roles found! Run setup-initial-data.sql</p>";
}

// Check Tourist class
echo "<h2>3. Tourist Class Check</h2>";
try {
    $tourist = new Tourist();
    echo "<p style='color: green;'>✓ Tourist class loaded successfully</p>";
    
    $countryCodes = $tourist->fetchCountryCode();
    echo "<p style='color: green;'>✓ fetchCountryCode() works: " . count($countryCodes) . " codes</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Check User_Login table
echo "<h2>4. User Login Table Check</h2>";
$loginSql = "SELECT COUNT(*) as total FROM User_Login";
$loginQuery = $conn->prepare($loginSql);
$loginQuery->execute();
$loginResult = $loginQuery->fetch();
echo "<p style='color: green;'>✓ User_Login table exists. Total users: " . $loginResult['total'] . "</p>";

// Check for existing tourists
echo "<h2>5. Existing Tourists</h2>";
$touristSql = "SELECT p.person_ID, CONCAT(n.name_first, ' ', n.name_last) as name, ci.contactinfo_email, ul.username
               FROM Person p
               INNER JOIN Name_Info n ON p.name_ID = n.name_ID
               INNER JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
               LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
               WHERE p.role_ID = 3
               ORDER BY p.person_ID DESC
               LIMIT 10";
$touristQuery = $conn->prepare($touristSql);
$touristQuery->execute();
$tourists = $touristQuery->fetchAll();

if (count($tourists) > 0) {
    echo "<p style='color: green;'>✓ Found " . count($tourists) . " tourists:</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Username</th></tr>";
    foreach ($tourists as $t) {
        echo "<tr>";
        echo "<td>{$t['person_ID']}</td>";
        echo "<td>{$t['name']}</td>";
        echo "<td>{$t['contactinfo_email']}</td>";
        echo "<td>{$t['username']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠ No tourists registered yet</p>";
}

// Check for existing guides
echo "<h2>6. Existing Tour Guides</h2>";
$guideSql = "SELECT p.person_ID, CONCAT(n.name_first, ' ', n.name_last) as name, ci.contactinfo_email, ul.username, ul.is_active
             FROM Person p
             INNER JOIN Name_Info n ON p.name_ID = n.name_ID
             INNER JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
             LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
             WHERE p.role_ID = 2
             ORDER BY p.person_ID DESC
             LIMIT 10";
$guideQuery = $conn->prepare($guideSql);
$guideQuery->execute();
$guides = $guideQuery->fetchAll();

if (count($guides) > 0) {
    echo "<p style='color: green;'>✓ Found " . count($guides) . " tour guides:</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Username</th><th>Status</th></tr>";
    foreach ($guides as $g) {
        $status = $g['is_active'] ? '<span style="color: green;">Active</span>' : '<span style="color: red;">Pending</span>';
        echo "<tr>";
        echo "<td>{$g['person_ID']}</td>";
        echo "<td>{$g['name']}</td>";
        echo "<td>{$g['contactinfo_email']}</td>";
        echo "<td>{$g['username']}</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠ No tour guides registered yet</p>";
}

echo "<hr>";
echo "<h2>Quick Actions</h2>";
echo "<p><a href='tourist-registration.php'>Register as Tourist</a></p>";
echo "<p><a href='guide-registration.php'>Register as Tour Guide</a></p>";
echo "<p><a href='index.php'>Go to Login</a></p>";
?>
