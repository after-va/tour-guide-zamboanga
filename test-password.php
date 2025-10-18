<?php
// Test password verification

$password = 'admin123';
$hash_from_db = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Testing password: " . $password . "<br>";
echo "Hash from database: " . $hash_from_db . "<br><br>";

if (password_verify($password, $hash_from_db)) {
    echo "✅ PASSWORD MATCHES! The hash is correct.<br>";
} else {
    echo "❌ PASSWORD DOES NOT MATCH! The hash is incorrect.<br>";
}

echo "<br><br>";
echo "Generating a new hash for 'admin123':<br>";
$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
echo $new_hash . "<br><br>";

echo "Testing new hash:<br>";
if (password_verify('admin123', $new_hash)) {
    echo "✅ New hash works!<br>";
}

echo "<br><br>";
echo "<strong>If the original hash doesn't work, use this SQL to update it:</strong><br>";
echo "<pre>";
echo "UPDATE User_Login \n";
echo "SET password_hash = '" . $new_hash . "' \n";
echo "WHERE username = 'admin@tourismozamboanga.com';";
echo "</pre>";
?>
