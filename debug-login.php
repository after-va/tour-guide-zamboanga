<?php
require_once "classes/database.php";

$db = new Database();
$conn = $db->connect();

echo "<h2>Debug Admin Login</h2>";

// Check if admin user exists
echo "<h3>1. Check User_Login table:</h3>";
$query = $conn->query("SELECT * FROM User_Login WHERE username = 'admin'");
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<p style='color: green;'>✓ Admin user found in User_Login</p>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Admin user NOT found in User_Login!</p>";
    echo "<p>You need to run setup-admin.sql</p>";
    exit;
}

// Check Account_Role
echo "<h3>2. Check Account_Role:</h3>";
$query = $conn->prepare("SELECT * FROM Account_Role WHERE login_ID = :login_ID");
$query->bindParam(':login_ID', $user['login_ID']);
$query->execute();
$role = $query->fetch(PDO::FETCH_ASSOC);

if ($role) {
    echo "<p style='color: green;'>✓ Account role found</p>";
    echo "<pre>";
    print_r($role);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ No role assigned to admin user!</p>";
    exit;
}

// Check Role_Info
echo "<h3>3. Check Role_Info:</h3>";
$query = $conn->prepare("SELECT * FROM Role_Info WHERE role_ID = :role_ID");
$query->bindParam(':role_ID', $role['role_ID']);
$query->execute();
$roleInfo = $query->fetch(PDO::FETCH_ASSOC);

if ($roleInfo) {
    echo "<p style='color: green;'>✓ Role info found</p>";
    echo "<pre>";
    print_r($roleInfo);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Role info not found!</p>";
}

// Test password verification
echo "<h3>4. Test Password Verification:</h3>";
$test_password = 'admin123';
$stored_hash = $user['password_hash'];

echo "<p>Testing password: <strong>admin123</strong></p>";
echo "<p>Stored hash: <code>" . htmlspecialchars($stored_hash) . "</code></p>";

if (password_verify($test_password, $stored_hash)) {
    echo "<p style='color: green;'>✓ Password verification PASSED</p>";
} else {
    echo "<p style='color: red;'>✗ Password verification FAILED</p>";
    echo "<p>The password hash might be incorrect. Try regenerating it.</p>";
    
    // Generate correct hash
    $correct_hash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "<p>Correct hash for 'admin123': <code>" . $correct_hash . "</code></p>";
    echo "<p>Run this SQL to fix:</p>";
    echo "<pre>UPDATE User_Login SET password_hash = '" . $correct_hash . "' WHERE username = 'admin';</pre>";
}

// Full login query test
echo "<h3>5. Full Login Query Test:</h3>";
$sql = "SELECT ul.*, p.person_ID, 
        CONCAT(n.name_first, ' ', n.name_last) as full_name,
        ar.account_role_ID, ar.role_ID, ri.role_name
        FROM User_Login ul
        INNER JOIN Person p ON ul.person_ID = p.person_ID
        LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
        LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
        LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
        WHERE ul.username = 'admin'
        LIMIT 1";

$query = $conn->query($sql);
$fullUser = $query->fetch(PDO::FETCH_ASSOC);

if ($fullUser) {
    echo "<p style='color: green;'>✓ Full user data retrieved</p>";
    echo "<pre>";
    print_r($fullUser);
    echo "</pre>";
    
    if (empty($fullUser['role_name'])) {
        echo "<p style='color: red;'>⚠ WARNING: role_name is empty! Check Account_Role and Role_Info tables.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Could not retrieve full user data</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Login</a></p>";
?>
