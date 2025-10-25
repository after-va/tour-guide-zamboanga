<?php
session_start();
require_once "php/Database.php";

$success = "";
$error = "";
$step = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Personal Information
    $name_first = trim($_POST['name_first']);
    $name_last = trim($_POST['name_last']);
    $name_middle = trim($_POST['name_middle']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $nationality = trim($_POST['nationality']);
    $gender = trim($_POST['gender']);
    $date_of_birth = $_POST['date_of_birth'];
    
    // Address
    $address_houseno = trim($_POST['address_houseno']);
    $address_street = trim($_POST['address_street']);
    $address_barangay = trim($_POST['address_barangay']);
    $address_city = trim($_POST['address_city']);
    $address_province = trim($_POST['address_province']);
    
    // Certification
    $certification_type = trim($_POST['certification_type']);
    $certification_number = trim($_POST['certification_number']);
    $issue_date = $_POST['issue_date'];
    $expiry_date = $_POST['expiry_date'];
    
    // Login credentials
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name_first) || empty($name_last) || empty($email) || empty($phone_number)) {
        $error = "Please fill in all required personal information fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        try {
            $db = new Database();
            $conn = $db->connect();
            
            // Check if username already exists
            $checkSql = "SELECT COUNT(*) as count FROM User_Login WHERE username = :username";
            $checkQuery = $conn->prepare($checkSql);
            $checkQuery->bindParam(":username", $username);
            $checkQuery->execute();
            $result = $checkQuery->fetch();
            
            if ($result['count'] > 0) {
                $error = "Username already exists. Please choose another.";
            } else {
                // Check for duplicate email
                $emailCheckSql = "SELECT COUNT(*) as count FROM Contact_Info WHERE contactinfo_email = :email";
                $emailCheckQuery = $conn->prepare($emailCheckSql);
                $emailCheckQuery->bindParam(":email", $email);
                $emailCheckQuery->execute();
                $emailResult = $emailCheckQuery->fetch();
                
                if ($emailResult['count'] > 0) {
                    $error = "This email address is already registered. Please use a different email or login to your existing account.";
                } else {
                    // Get Philippines country code
                    $countrySql = "SELECT countrycode_ID FROM Country_Code WHERE countrycode_name = 'Philippines' LIMIT 1";
                    $countryQuery = $conn->prepare($countrySql);
                    $countryQuery->execute();
                    $country = $countryQuery->fetch();
                    
                    if (!$country) {
                        $error = "Country code for Philippines not found in database. Please contact administrator.";
                    } else {
                        $countrycode_ID = $country['countrycode_ID'];
                    
                    // Check for duplicate phone number
                    $phoneCheckSql = "SELECT COUNT(*) as count FROM Phone_Number WHERE countrycode_ID = :countrycode_ID AND phone_number = :phone_number";
                    $phoneCheckQuery = $conn->prepare($phoneCheckSql);
                    $phoneCheckQuery->bindParam(":countrycode_ID", $countrycode_ID);
                    $phoneCheckQuery->bindParam(":phone_number", $phone_number);
                    $phoneCheckQuery->execute();
                    $phoneResult = $phoneCheckQuery->fetch();
                    
                    if ($phoneResult['count'] > 0) {
                        $error = "This phone number is already registered. Please use a different phone number or login to your existing account.";
                    } else {
                        // Check for duplicate person (same name, birthdate, and gender)
                        $personCheckSql = "SELECT COUNT(*) as count FROM Person p 
                                          INNER JOIN Name_Info n ON p.name_ID = n.name_ID 
                                          WHERE n.name_first = :name_first 
                                          AND (n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL))
                                          AND n.name_last = :name_last 
                                          AND p.person_DateOfBirth = :dob
                                          AND p.person_Gender = :gender";
                        $personCheckQuery = $conn->prepare($personCheckSql);
                        $personCheckQuery->bindParam(":name_first", $name_first);
                        $personCheckQuery->bindParam(":name_middle", $name_middle);
                        $personCheckQuery->bindParam(":name_last", $name_last);
                        $personCheckQuery->bindParam(":dob", $date_of_birth);
                        $personCheckQuery->bindParam(":gender", $gender);
                        $personCheckQuery->execute();
                        $personResult = $personCheckQuery->fetch();
                        
                        if ($personResult['count'] > 0) {
                            $error = "An account with the same name, birthdate, and gender already exists. If this is you, please login to your existing account.";
                        } else {
                            $conn->beginTransaction();
                            
                            // Insert phone number
                $phoneSql = "INSERT INTO Phone_Number (countrycode_ID, phone_number) VALUES (:countrycode_ID, :phone_number)";
                $phoneQuery = $conn->prepare($phoneSql);
                $phoneQuery->bindParam(":countrycode_ID", $countrycode_ID);
                $phoneQuery->bindParam(":phone_number", $phone_number);
                $phoneQuery->execute();
                $phone_ID = $conn->lastInsertId();
                
                // Insert address
                $addressSql = "INSERT INTO Address_Info (address_houseno, address_street, address_barangay, address_city, address_province, address_country) 
                               VALUES (:houseno, :street, :barangay, :city, :province, 'Philippines')";
                $addressQuery = $conn->prepare($addressSql);
                $addressQuery->bindParam(":houseno", $address_houseno);
                $addressQuery->bindParam(":street", $address_street);
                $addressQuery->bindParam(":barangay", $address_barangay);
                $addressQuery->bindParam(":city", $address_city);
                $addressQuery->bindParam(":province", $address_province);
                $addressQuery->execute();
                $address_ID = $conn->lastInsertId();
                
                // Insert emergency contact (self for now)
                $emergencySql = "INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship) 
                                 VALUES (:phone_ID, :name, 'Self')";
                $emergencyQuery = $conn->prepare($emergencySql);
                $emergencyQuery->bindParam(":phone_ID", $phone_ID);
                $full_name = $name_first . ' ' . $name_last;
                $emergencyQuery->bindParam(":name", $full_name);
                $emergencyQuery->execute();
                $emergency_ID = $conn->lastInsertId();
                
                // Insert contact info
                $contactSql = "INSERT INTO Contact_Info (address_ID, phone_ID, emergency_ID, contactinfo_email) 
                               VALUES (:address_ID, :phone_ID, :emergency_ID, :email)";
                $contactQuery = $conn->prepare($contactSql);
                $contactQuery->bindParam(":address_ID", $address_ID);
                $contactQuery->bindParam(":phone_ID", $phone_ID);
                $contactQuery->bindParam(":emergency_ID", $emergency_ID);
                $contactQuery->bindParam(":email", $email);
                $contactQuery->execute();
                $contactinfo_ID = $conn->lastInsertId();
                
                // Insert name
                $nameSql = "INSERT INTO Name_Info (name_first, name_middle, name_last) VALUES (:first, :middle, :last)";
                $nameQuery = $conn->prepare($nameSql);
                $nameQuery->bindParam(":first", $name_first);
                $nameQuery->bindParam(":middle", $name_middle);
                $nameQuery->bindParam(":last", $name_last);
                $nameQuery->execute();
                $name_ID = $conn->lastInsertId();
                
                // Get Tour Guide role_ID (should be 2)
                $roleSql = "SELECT role_ID FROM Role_Info WHERE role_name = 'Tour Guide' LIMIT 1";
                $roleQuery = $conn->prepare($roleSql);
                $roleQuery->execute();
                $role = $roleQuery->fetch();
                $role_ID = $role['role_ID'];
                
                // Insert person
                $personSql = "INSERT INTO Person (role_ID, name_ID, contactinfo_ID, person_Nationality, person_Gender, person_DateOfBirth) 
                              VALUES (:role_ID, :name_ID, :contactinfo_ID, :nationality, :gender, :dob)";
                $personQuery = $conn->prepare($personSql);
                $personQuery->bindParam(":role_ID", $role_ID);
                $personQuery->bindParam(":name_ID", $name_ID);
                $personQuery->bindParam(":contactinfo_ID", $contactinfo_ID);
                $personQuery->bindParam(":nationality", $nationality);
                $personQuery->bindParam(":gender", $gender);
                $personQuery->bindParam(":dob", $date_of_birth);
                $personQuery->execute();
                $person_ID = $conn->lastInsertId();
                
                // Insert login credentials
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $loginSql = "INSERT INTO User_Login (person_ID, username, password_hash, is_active) 
                             VALUES (:person_ID, :username, :password_hash, 0)";
                $loginQuery = $conn->prepare($loginSql);
                $loginQuery->bindParam(":person_ID", $person_ID);
                $loginQuery->bindParam(":username", $username);
                $loginQuery->bindParam(":password_hash", $password_hash);
                $loginQuery->execute();
                
                // Insert certification (pending approval)
                $certSql = "INSERT INTO Guide_Certification (guide_ID, certification_type, certification_number, issue_date, expiry_date, status) 
                            VALUES (:guide_ID, :cert_type, :cert_number, :issue_date, :expiry_date, 'pending')";
                $certQuery = $conn->prepare($certSql);
                $certQuery->bindParam(":guide_ID", $person_ID);
                $certQuery->bindParam(":cert_type", $certification_type);
                $certQuery->bindParam(":cert_number", $certification_number);
                $certQuery->bindParam(":issue_date", $issue_date);
                $certQuery->bindParam(":expiry_date", $expiry_date);
                $certQuery->execute();
                
                $conn->commit();
                
                $success = "Registration successful! Your application is pending admin approval. You will be notified once approved.";
                $step = 3; // Show success message
                        }
                    }
                }
                    }
                }
            }
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tour Guide Registration - Tourismo Zamboanga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            text-align: center;
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .form-section:last-child {
            border-bottom: none;
        }
        .form-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .required {
            color: red;
        }
        small {
            color: #666;
            font-size: 0.9em;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tour Guide Registration</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo $success; ?>
            </div>
            <div class="back-link">
                <a href="index.html">← Back to Home</a>
            </div>
        <?php else: ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>Error!</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>Important:</strong> Your account will be activated after admin approval. You will need to provide valid tour guide certification information.
            </div>
            
            <form method="POST" action="">
                <!-- Personal Information -->
                <div class="form-section">
                    <h3>Personal Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="name_first" value="<?php echo isset($_POST['name_first']) ? htmlspecialchars($_POST['name_first']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="name_middle" value="<?php echo isset($_POST['name_middle']) ? htmlspecialchars($_POST['name_middle']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="name_last" value="<?php echo isset($_POST['name_last']) ? htmlspecialchars($_POST['name_last']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Gender <span class="required">*</span></label>
                            <select name="gender" required>
                                <option value="">-- Select --</option>
                                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth <span class="required">*</span></label>
                            <input type="date" name="date_of_birth" value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nationality <span class="required">*</span></label>
                            <input type="text" name="nationality" value="<?php echo isset($_POST['nationality']) ? htmlspecialchars($_POST['nationality']) : 'Filipino'; ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="form-section">
                    <h3>Contact Information</h3>
                    
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" placeholder="9123456789" required>
                        <small>Enter 10-digit number without country code</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>House No. <span class="required">*</span></label>
                            <input type="text" name="address_houseno" value="<?php echo isset($_POST['address_houseno']) ? htmlspecialchars($_POST['address_houseno']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Street <span class="required">*</span></label>
                            <input type="text" name="address_street" value="<?php echo isset($_POST['address_street']) ? htmlspecialchars($_POST['address_street']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Barangay <span class="required">*</span></label>
                            <input type="text" name="address_barangay" value="<?php echo isset($_POST['address_barangay']) ? htmlspecialchars($_POST['address_barangay']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>City <span class="required">*</span></label>
                            <input type="text" name="address_city" value="<?php echo isset($_POST['address_city']) ? htmlspecialchars($_POST['address_city']) : 'Zamboanga City'; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Province <span class="required">*</span></label>
                        <input type="text" name="address_province" value="<?php echo isset($_POST['address_province']) ? htmlspecialchars($_POST['address_province']) : 'Zamboanga del Sur'; ?>" required>
                    </div>
                </div>
                
                <!-- Certification Information -->
                <div class="form-section">
                    <h3>Tour Guide Certification</h3>
                    
                    <div class="form-group">
                        <label>Certification Type <span class="required">*</span></label>
                        <select name="certification_type" required>
                            <option value="">-- Select --</option>
                            <option value="DOT Accredited Tour Guide">DOT Accredited Tour Guide</option>
                            <option value="Local Tourism Office Certified">Local Tourism Office Certified</option>
                            <option value="Professional Tour Guide License">Professional Tour Guide License</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Certification Number <span class="required">*</span></label>
                        <input type="text" name="certification_number" value="<?php echo isset($_POST['certification_number']) ? htmlspecialchars($_POST['certification_number']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Issue Date <span class="required">*</span></label>
                            <input type="date" name="issue_date" value="<?php echo isset($_POST['issue_date']) ? $_POST['issue_date'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Expiry Date <span class="required">*</span></label>
                            <input type="date" name="expiry_date" value="<?php echo isset($_POST['expiry_date']) ? $_POST['expiry_date'] : ''; ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Login Credentials -->
                <div class="form-section">
                    <h3>Login Credentials</h3>
                    
                    <div class="form-group">
                        <label>Username <span class="required">*</span></label>
                        <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        <small>This will be used to login to your account</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" name="password" required>
                        <small>Minimum 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <input type="password" name="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </form>
            
            <div class="back-link">
                <a href="index.html">← Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
