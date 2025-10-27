<?php
require_once "classes/database.php";

$db = new Database();
$conn = $db->connect();

echo "<h2>Database Diagnostic</h2>";

// Check Philippines country_ID
echo "<h3>1. Philippines Country ID:</h3>";
$query = $conn->query("SELECT country_ID, country_name, country_codename FROM Country WHERE country_name = 'Philippines'");
$philippines = $query->fetch(PDO::FETCH_ASSOC);
if ($philippines) {
    echo "<p style='color: green;'><strong>Philippines found!</strong></p>";
    echo "<p>country_ID: <strong>" . $philippines['country_ID'] . "</strong></p>";
    echo "<p>country_name: " . $philippines['country_name'] . "</p>";
    echo "<p>country_codename: " . $philippines['country_codename'] . "</p>";
} else {
    echo "<p style='color: red;'>Philippines NOT found in Country table!</p>";
}

// Check Regions
echo "<h3>2. Philippine Regions:</h3>";
if ($philippines) {
    $query = $conn->prepare("SELECT COUNT(*) as count FROM Region WHERE country_ID = :country_ID");
    $query->bindParam(':country_ID', $philippines['country_ID']);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total Regions: <strong>" . $result['count'] . "</strong></p>";
    
    if ($result['count'] > 0) {
        $query = $conn->prepare("SELECT region_ID, region_name FROM Region WHERE country_ID = :country_ID LIMIT 5");
        $query->bindParam(':country_ID', $philippines['country_ID']);
        $query->execute();
        echo "<p>Sample Regions:</p><ul>";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>ID: " . $row['region_ID'] . " - " . $row['region_name'] . "</li>";
        }
        echo "</ul>";
    }
}

// Check Provinces
echo "<h3>3. Provinces:</h3>";
$query = $conn->query("SELECT COUNT(*) as count FROM Province");
$result = $query->fetch(PDO::FETCH_ASSOC);
echo "<p>Total Provinces: <strong>" . $result['count'] . "</strong></p>";

if ($result['count'] > 0) {
    $query = $conn->query("SELECT p.province_ID, p.province_name, r.region_name 
                           FROM Province p 
                           JOIN Region r ON p.region_ID = r.region_ID 
                           LIMIT 5");
    echo "<p>Sample Provinces:</p><ul>";
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>ID: " . $row['province_ID'] . " - " . $row['province_name'] . " (Region: " . $row['region_name'] . ")</li>";
    }
    echo "</ul>";
}

// Check Cities
echo "<h3>4. Cities:</h3>";
$query = $conn->query("SELECT COUNT(*) as count FROM City");
$result = $query->fetch(PDO::FETCH_ASSOC);
echo "<p>Total Cities: <strong>" . $result['count'] . "</strong></p>";

if ($result['count'] > 0) {
    $query = $conn->query("SELECT c.city_ID, c.city_name, p.province_name 
                           FROM City c 
                           JOIN Province p ON c.province_ID = p.province_ID 
                           LIMIT 10");
    echo "<p>Sample Cities:</p><ul>";
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>ID: " . $row['city_ID'] . " - " . $row['city_name'] . " (Province: " . $row['province_name'] . ")</li>";
    }
    echo "</ul>";
}

// Check Admin User
echo "<h3>5. Admin User:</h3>";
$query = $conn->query("SELECT ul.username, p.person_ID, ar.role_ID, ri.role_name
                       FROM User_Login ul
                       JOIN Person p ON ul.person_ID = p.person_ID
                       JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                       JOIN Role_Info ri ON ar.role_ID = ri.role_ID
                       WHERE ul.username = 'admin'");
$admin = $query->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    echo "<p style='color: green;'><strong>Admin user found!</strong></p>";
    echo "<p>Username: " . $admin['username'] . "</p>";
    echo "<p>Person ID: " . $admin['person_ID'] . "</p>";
    echo "<p>Role: " . $admin['role_name'] . " (ID: " . $admin['role_ID'] . ")</p>";
} else {
    echo "<p style='color: red;'>Admin user NOT found!</p>";
    echo "<p>You need to run setup-admin.sql</p>";
}

// Check all users
echo "<h3>6. All Users:</h3>";
$query = $conn->query("SELECT ul.username, ri.role_name
                       FROM User_Login ul
                       JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                       JOIN Role_Info ri ON ar.role_ID = ri.role_ID");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
echo "<p>Total Users: <strong>" . count($users) . "</strong></p>";
if (count($users) > 0) {
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li>" . $user['username'] . " (" . $user['role_name'] . ")</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index.php'>Go to Login Page</a></p>";
echo "<p><a href='registration/tourist-registration.php'>Go to Registration</a></p>";
?>
