<?php
// Debug login script
require_once "php/Database.php";

$username = 'admin@tourismozamboanga.com';
$password = 'admin123';

echo "<h2>Debug Login Test</h2>";
echo "<strong>Testing credentials:</strong><br>";
echo "Username: " . $username . "<br>";
echo "Password: " . $password . "<br><br>";

try {
    $db = new Database();
    $conn = $db->connect();
    
    $sql = "SELECT ul.*, p.person_ID, p.role_ID, r.role_name,
                   CONCAT(n.name_first, ' ', n.name_last) as full_name
            FROM User_Login ul
            INNER JOIN Person p ON ul.person_ID = p.person_ID
            INNER JOIN Role_Info r ON p.role_ID = r.role_ID
            INNER JOIN Name_Info n ON p.name_ID = n.name_ID
            WHERE ul.username = :username AND ul.is_active = 1";
    
    $query = $conn->prepare($sql);
    $query->bindParam(":username", $username);
    
    if ($query->execute()) {
        $user = $query->fetch();
        
        if ($user) {
            echo "✅ <strong>User found in database!</strong><br><br>";
            echo "<strong>User details:</strong><br>";
            echo "- person_ID: " . $user['person_ID'] . "<br>";
            echo "- role_ID: " . $user['role_ID'] . "<br>";
            echo "- role_name: " . $user['role_name'] . "<br>";
            echo "- full_name: " . $user['full_name'] . "<br>";
            echo "- username: " . $user['username'] . "<br>";
            echo "- is_active: " . $user['is_active'] . "<br>";
            echo "- password_hash: " . $user['password_hash'] . "<br><br>";
            
            echo "<strong>Password verification:</strong><br>";
            if (password_verify($password, $user['password_hash'])) {
                echo "✅ <strong style='color: green;'>PASSWORD MATCHES!</strong><br>";
                echo "Login should work. Check if role_ID = 1 for admin.<br>";
                
                if ($user['role_ID'] == 1) {
                    echo "✅ <strong style='color: green;'>User IS an admin (role_ID = 1)</strong><br>";
                    echo "<br><strong>LOGIN SHOULD WORK!</strong><br>";
                } else {
                    echo "❌ <strong style='color: red;'>User is NOT an admin (role_ID = " . $user['role_ID'] . ")</strong><br>";
                }
            } else {
                echo "❌ <strong style='color: red;'>PASSWORD DOES NOT MATCH!</strong><br>";
                echo "The hash in database doesn't match 'admin123'<br><br>";
                
                echo "<strong>Solution:</strong> Generate new hash and update database<br>";
                $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
                echo "<pre>";
                echo "UPDATE User_Login \n";
                echo "SET password_hash = '" . $new_hash . "' \n";
                echo "WHERE username = 'admin@tourismozamboanga.com';";
                echo "</pre>";
            }
        } else {
            echo "❌ <strong style='color: red;'>User NOT found in database!</strong><br>";
            echo "The admin user doesn't exist or is_active = 0<br>";
        }
    } else {
        echo "❌ Query failed to execute<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
}
?>
