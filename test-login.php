<?php
// Test Login Page - Debug the login issue
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "classes/database.php";
require_once "classes/auth.php";

$debug_info = [];
$test_result = "";

// Test 1: Check database connection
$db = new Database();
try {
    $conn = $db->connect();
    $debug_info[] = "✓ Database connection successful";
} catch (Exception $e) {
    $debug_info[] = "✗ Database connection failed: " . $e->getMessage();
}

// Test 2: Check if admin user exists
try {
    $sql = "SELECT login_ID, username, password_hash FROM User_Login WHERE username = 'admin' LIMIT 1";
    $query = $conn->prepare($sql);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $debug_info[] = "✓ Admin user found (login_ID: " . $user['login_ID'] . ")";
        $debug_info[] = "  Username: " . $user['username'];
        $debug_info[] = "  Password hash: " . substr($user['password_hash'], 0, 20) . "...";
    } else {
        $debug_info[] = "✗ Admin user NOT found in database";
    }
} catch (Exception $e) {
    $debug_info[] = "✗ Error checking admin user: " . $e->getMessage();
}

// Test 3: Check if admin has a role
try {
    $sql = "SELECT ar.account_role_ID, ar.role_ID, ri.role_name 
            FROM Account_Role ar
            LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
            WHERE ar.login_ID IN (SELECT login_ID FROM User_Login WHERE username = 'admin')";
    $query = $conn->prepare($sql);
    $query->execute();
    $role = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($role) {
        $debug_info[] = "✓ Admin role found (role_ID: " . $role['role_ID'] . ", role_name: " . $role['role_name'] . ")";
    } else {
        $debug_info[] = "✗ Admin has NO role assigned";
    }
} catch (Exception $e) {
    $debug_info[] = "✗ Error checking admin role: " . $e->getMessage();
}

// Test 4: Test password verification
if ($user) {
    $test_password = "admin123";
    $password_match = password_verify($test_password, $user['password_hash']);
    
    if ($password_match) {
        $debug_info[] = "✓ Password 'admin123' matches the hash";
    } else {
        $debug_info[] = "✗ Password 'admin123' does NOT match the hash";
        $debug_info[] = "  Trying to generate new hash for 'admin123'...";
        $new_hash = password_hash("admin123", PASSWORD_DEFAULT);
        $debug_info[] = "  New hash: " . $new_hash;
    }
}

// Test 5: Test the actual login function
$auth = new Auth();
$login_result = $auth->login("admin", "admin123");

if ($login_result) {
    $test_result = "✓ LOGIN SUCCESSFUL!";
    $debug_info[] = "✓ Auth::login() returned success";
    $debug_info[] = "  User data: " . json_encode($login_result);
} else {
    $test_result = "✗ LOGIN FAILED";
    $debug_info[] = "✗ Auth::login() returned false";
}

// Test 6: Check all roles in database
try {
    $sql = "SELECT role_ID, role_name FROM Role_Info ORDER BY role_ID";
    $query = $conn->prepare($sql);
    $query->execute();
    $roles = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $debug_info[] = "Roles in database:";
    foreach ($roles as $r) {
        $debug_info[] = "  - role_ID: " . $r['role_ID'] . ", role_name: " . $r['role_name'];
    }
} catch (Exception $e) {
    $debug_info[] = "✗ Error checking roles: " . $e->getMessage();
}

// Test 7: Full login query test
try {
    $sql = "SELECT ul.login_ID, ul.username, p.person_ID, 
            CONCAT(n.name_first, ' ', n.name_last) as full_name,
            ar.account_role_ID, ar.role_ID, ri.role_name
            FROM User_Login ul
            INNER JOIN Person p ON ul.person_ID = p.person_ID
            LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
            LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
            LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
            WHERE ul.username = 'admin'
            LIMIT 1";
    $query = $conn->prepare($sql);
    $query->execute();
    $full_user = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($full_user) {
        $debug_info[] = "✓ Full login query returned data:";
        $debug_info[] = "  login_ID: " . $full_user['login_ID'];
        $debug_info[] = "  username: " . $full_user['username'];
        $debug_info[] = "  full_name: " . $full_user['full_name'];
        $debug_info[] = "  role_ID: " . $full_user['role_ID'];
        $debug_info[] = "  role_name: " . $full_user['role_name'];
    } else {
        $debug_info[] = "✗ Full login query returned NO data";
    }
} catch (Exception $e) {
    $debug_info[] = "✗ Error in full login query: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Test - Tourismo Zamboanga</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #00ff00;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #0d0d0d;
            border: 2px solid #00ff00;
            padding: 20px;
            border-radius: 5px;
        }
        h1 {
            color: #00ff00;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 10px;
        }
        .test-result {
            font-size: 24px;
            font-weight: bold;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background: #003300;
            border: 2px solid #00ff00;
            color: #00ff00;
        }
        .failure {
            background: #330000;
            border: 2px solid #ff0000;
            color: #ff0000;
        }
        .debug-section {
            background: #1a1a1a;
            border-left: 3px solid #00ff00;
            padding: 10px;
            margin: 10px 0;
        }
        .debug-line {
            padding: 5px 0;
        }
        .success-line {
            color: #00ff00;
        }
        .error-line {
            color: #ff0000;
        }
        .info-line {
            color: #ffff00;
        }
        .button-group {
            margin-top: 20px;
            text-align: center;
        }
        a, button {
            background: #00ff00;
            color: #000;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        a:hover, button:hover {
            background: #00cc00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Login Test & Debug</h1>
        
        <div class="test-result <?php echo strpos($test_result, 'SUCCESS') !== false ? 'success' : 'failure'; ?>">
            <?php echo $test_result; ?>
        </div>

        <div class="debug-section">
            <h2>Debug Information:</h2>
            <?php foreach ($debug_info as $info): ?>
                <div class="debug-line <?php 
                    if (strpos($info, '✓') === 0) echo 'success-line';
                    elseif (strpos($info, '✗') === 0) echo 'error-line';
                    else echo 'info-line';
                ?>">
                    <?php echo htmlspecialchars($info); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="button-group">
            <a href="index.php">← Back to Login</a>
            <button onclick="location.reload()">🔄 Refresh Test</button>
        </div>
    </div>
</body>
</html>
