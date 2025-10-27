<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once "../classes/guide.php";

// Initialize variables
$guide = [];
$errors = [];
$success = "";
$dbError = "";

// Function to test registration with sample data
function testRegistration($guideObj) {
    error_log("=== STARTING TEST GUIDE REGISTRATION ===");
    
    // Sample test data
    $testData = [
        'name_first' => 'Test',
        'name_last' => 'Guide',
        'name_middle' => 'Middle',
        'name_second' => '',
        'name_suffix' => '',
        'address_houseno' => '123',
        'address_street' => 'Test Street',
        'address_country_ID' => '161', // Philippines
        'region_ID' => '1',
        'province_ID' => '1',
        'city_ID' => '1',
        'barangay_ID' => '1',
        'country_ID' => '161',
        'phone_number' => '09123456789',
        'emergency_name' => 'Emergency Contact',
        'emergency_country_ID' => '161',
        'emergency_phonenumber' => '09123456780',
        'emergency_relationship' => 'Friend',
        'contactinfo_email' => 'testguide' . time() . '@example.com', // Unique email
        'person_nationality' => 'Filipino',
        'person_gender' => 'Male',
        'person_dateofbirth' => '1990-01-01',
        'username' => 'testguide' . time(), // Unique username
        'password' => 'Test@1234'
    ];
    
    // Log test data
    error_log("Test Data: " . print_r($testData, true));
    
    // Call addGuide directly
    try {
        error_log("Calling addGuide with test data");
        $result = $guideObj->addGuide(
            $testData['name_first'],
            $testData['name_second'],
            $testData['name_middle'],
            $testData['name_last'],
            $testData['name_suffix'],
            $testData['address_houseno'],
            $testData['address_street'],
            $testData['barangay_ID'],
            $testData['country_ID'],
            $testData['phone_number'],
            $testData['emergency_name'],
            $testData['emergency_country_ID'],
            $testData['emergency_phonenumber'],
            $testData['emergency_relationship'],
            $testData['contactinfo_email'],
            $testData['person_nationality'],
            $testData['person_gender'],
            $testData['person_dateofbirth'],
            $testData['username'],
            $testData['password']
        );
        
        if ($result) {
            error_log("Test guide registration SUCCESSFUL!");
            return "Test guide registration successful! Check error log for details.";
        } else {
            $error = $guideObj->getLastError();
            error_log("Test guide registration FAILED: " . $error);
            return "Test guide registration failed: " . $error;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Test guide registration EXCEPTION: " . $error);
        return "Test guide registration exception: " . $error;
    }
}

// Check if test registration was requested
if (isset($_GET['test_register'])) {
    $guideObj = new Guide();
    $testResult = testRegistration($guideObj);
    die($testResult);
}

// Initialize Guide object
try {
    $guideObj = new Guide();
    
    // Test database connection
    $db = $guideObj->connect();
    if (!$db) {
        $dbError = "Database connection failed. Please try again later.";
        error_log("Database connection error: " . $guideObj->getLastError());
    }
} catch (Exception $e) {
    $dbError = "System error. Please try again later.";
    error_log("Guide object initialization error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if database is available
    if (!empty($dbError)) {
        $errors["general"] = $dbError;
    } else {
        // Sanitize inputs
        $guide = array_map(function($value) {
            return is_string($value) ? trim(htmlspecialchars($value)) : $value;
        }, $_POST);

        // Debug: Log POST data
        error_log("POST Data: " . print_r($guide, true));

        // === Validation ===
        $required = [
            "name_first", "name_last", 
            "address_houseno", "address_street", "address_country_ID", 
            "country_ID", "emergency_name", "emergency_country_ID",
            "emergency_phonenumber", "emergency_relationship", "contactinfo_email",
            "person_nationality", "person_gender", "person_dateofbirth",
            "username", "password"
        ];

        foreach ($required as $field) {
            if (empty($guide[$field])) {
                $errors[$field] = ucfirst(str_replace("_", " ", $field)) . " is required.";
            }
        }

        // Additional validation based on country
        if (!empty($guide["address_country_ID"])) {
            if ($guide["address_country_ID"] == "161") {
                // Philippines - require dropdown IDs
                $required = [
                    "region_ID", "province_ID", "city_ID", "barangay_ID"
                ];
            } else {
                // Other countries - require text inputs
                $required = [
                    "region_name", "province_name", "city_name", "barangay_name"
                ];
            }

            foreach ($required as $field) {
                if (empty($guide[$field])) {
                    $errors[$field] = ucfirst(str_replace("_", " ", $field)) . " is required.";
                }
            }
        }

        // Validate phone numbers - make phone number optional
        if (!empty($guide["phone_number"]) && strlen($guide["phone_number"]) < 10) {
            $errors["phone_number"] = "Phone Number must be at least 10 digits.";
        }
        if (!empty($guide["emergency_phonenumber"]) && strlen($guide["emergency_phonenumber"]) < 10) {
            $errors["emergency_phonenumber"] = "Emergency Phone must be at least 10 digits.";
        }

        // Validate email
        if (!empty($guide["contactinfo_email"]) && !filter_var($guide["contactinfo_email"], FILTER_VALIDATE_EMAIL)) {
            $errors["contactinfo_email"] = "Invalid email format.";
        }

        // Proceed if no errors
        if (empty($errors)) {
            // Handle non-Philippines addresses by converting text to IDs
            $barangay_ID = null;
            
            if ($guide["address_country_ID"] == "161") {
                // Philippines - use existing barangay_ID
                $barangay_ID = $guide["barangay_ID"];
            } else {
                // Other countries - create/get IDs from text inputs
                $db = $guideObj->connect();
                
                // Get or create Region
                $region_ID = $guideObj->addgetRegion($guide["region_name"], $guide["address_country_ID"], $db);
                
                // Get or create Province
                $province_ID = $guideObj->addgetProvince($guide["province_name"], $region_ID, $db);
                
                // Get or create City
                $city_ID = $guideObj->addgetCity($guide["city_name"], $province_ID, $db);
                
                // Get or create Barangay
                $barangay_ID = $guideObj->addgetBarangay($guide["barangay_name"], $city_ID, $db);
            }

            try {
                error_log("Calling addGuide with data: " . print_r([
                    'name_first' => $guide["name_first"],
                    'name_last' => $guide["name_last"],
                    'username' => $guide["username"]
                ], true));

                $results = $guideObj->addGuide(
                    $guide["name_first"], 
                    $guide["name_second"] ?? null, 
                    $guide["name_middle"] ?? null, 
                    $guide["name_last"], 
                    $guide["name_suffix"] ?? null,
                    $guide["address_houseno"], 
                    $guide["address_street"], 
                    $barangay_ID,
                    $guide["country_ID"], 
                    $guide["phone_number"] ?? null,
                    $guide["emergency_name"], 
                    $guide["emergency_country_ID"], 
                    $guide["emergency_phonenumber"], 
                    $guide["emergency_relationship"],
                    $guide["contactinfo_email"],
                    $guide["person_nationality"], 
                    $guide["person_gender"], 
                    $guide["person_dateofbirth"], 
                    $guide["username"], 
                    $guide["password"]
                );
                
                error_log("addGuide result: " . ($results ? 'true' : 'false'));
                if (!$results) {
                    error_log("addGuide error: " . $guideObj->getLastError());
                }
            } catch (Exception $e) {
                error_log("Error in addGuide: " . $e->getMessage());
                $errors["general"] = "An error occurred during registration. Please try again.";
            }

            if($results){
                header("Location: guide-registration.php?success=1");
                $guide = [];
                exit;
            } else {
                // Log the actual error for debugging
                $errorDetails = $guideObj->getLastError();
                error_log("Registration failed: " . print_r($errorDetails, true));
            
                // More specific error message
                if (strpos(strtolower($errorDetails), 'duplicate entry') !== false) {
                    if (strpos(strtolower($errorDetails), 'username') !== false) {
                        $errors["username"] = "This username is already taken. Please choose another one.";
                    } elseif (strpos(strtolower($errorDetails), 'email') !== false) {
                        $errors["contactinfo_email"] = "This email is already registered. Please use a different email or try to log in.";
                    } else {
                        $errors["general"] = "This information is already registered. Please check your details and try again.";
                    }
                } else {
                    $errors["general"] = "Failed to save data. Please check your information and try again. " . 
                                         "If the problem persists, please contact support.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guide Registration</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            line-height: 1.6;
            color: #333;
        }
        form { 
            max-width: 600px; 
            margin: 20px auto; 
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .form-section h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        label { 
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="tel"],
        select { 
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
        .error { 
            color: #e74c3c; 
            font-size: 0.85em;
            margin-top: -5px;
            margin-bottom: 10px;
        }
        .success { 
            color: #27ae60; 
            font-weight: bold; 
            margin: 15px 0;
            padding: 12px;
            background-color: #e8f5e9; 
            border-radius: 5px;
            border-left: 4px solid #27ae60;
        }
        .error-message {
            color: #e74c3c;
            background-color: #fde8e8;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #e74c3c;
        }
        .test-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .test-button:hover {
            background-color: #218838;
            color: white;
        }
        button[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #2980b9;
        }
        .required:after {
            content: " *";
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div style="background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>Guide Registration</h2>
        <div style="margin: 15px 0;">
            <a href="?test_register=1" class="test-button" 
               onclick="return confirm('This will create a test guide registration. Continue?')">
                Run Test Registration
            </a>
            <small style="display: block; margin-top: 5px; color: #666;">
                This will create a test registration with sample data to help identify issues.
            </small>
        </div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success">Registration successful! Your guide account has been created and is pending admin approval. You will be notified once approved.</div>
    <?php endif; ?>

    <?php if (!empty($errors["general"])): ?>
        <p class="error"><?= $errors["general"] ?></p>
    <?php endif; ?>

    <form method="POST">
        <h3>Account Info</h3>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= $guide["username"] ?? "" ?>">
        <p class="error"><?= $errors["username"] ?? "" ?></p>

        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <p class="error"><?= $errors["password"] ?? "" ?></p>

        <h3>Basic Info</h3>
        <label for="name_first">First Name</label>
        <input type="text" name="name_first" id="name_first" value="<?= $guide["name_first"] ?? "" ?>">
        <p class="error"><?= $errors["name_first"] ?? "" ?></p>

        <label for="name_second">Second Name (Optional)</label>
        <input type="text" name="name_second" id="name_second" value="<?= $guide["name_second"] ?? "" ?>">
        <p class="error"><?= $errors["name_second"] ?? "" ?></p>

        <label for="name_middle">Middle Name (Optional)</label>
        <input type="text" name="name_middle" id="name_middle" value="<?= $guide["name_middle"] ?? "" ?>">
        <p class="error"><?= $errors["name_middle"] ?? "" ?></p>

        <label for="name_last">Last Name</label>
        <input type="text" name="name_last" id="name_last" value="<?= $guide["name_last"] ?? "" ?>">
        <p class="error"><?= $errors["name_last"] ?? "" ?></p>

        <label for="name_suffix">Suffix (Optional)</label>
        <input type="text" name="name_suffix" id="name_suffix" value="<?= $guide["name_suffix"] ?? "" ?>" placeholder="Jr., Sr., III, etc.">
        <p class="error"><?= $errors["name_suffix"] ?? "" ?></p>

        <label for="contactinfo_email">Email</label>
        <input type="email" name="contactinfo_email" id="contactinfo_email" value="<?= $guide["contactinfo_email"] ?? "" ?>">
        <p class="error"><?= $errors["contactinfo_email"] ?? "" ?></p>

        <label for="person_nationality">Nationality</label>
        <input type="text" name="person_nationality" id="person_nationality" value="<?= $guide["person_nationality"] ?? "" ?>">
        <p class="error"><?= $errors["person_nationality"] ?? "" ?></p>

        <label for="person_gender">Gender</label>
        <select name="person_gender" id="person_gender">
            <option value="">--Select--</option>
            <option value="Male" <?= ($guide["person_gender"] ?? "") === "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= ($guide["person_gender"] ?? "") === "Female" ? "selected" : "" ?>>Female</option>
        </select>
        <p class="error"><?= $errors["person_gender"] ?? "" ?></p>

        <label for="person_dateofbirth">Date of Birth</label>
        <input type="date" name="person_dateofbirth" id="person_dateofbirth" value="<?= $guide["person_dateofbirth"] ?? "" ?>">
        <p class="error"><?= $errors["person_dateofbirth"] ?? "" ?></p>

        <h3>Phone Number</h3>
        <label for="country_ID"> Country Code </label>
        <select name="country_ID" id="country_ID">
            <option value="">--SELECT COUNTRY CODE--</option>
            <?php foreach ($guideObj->fetchCountryCode() as $country_code){ 
                $temp = $country_code["country_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($guide["country_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["country_name"] ?> <?= $country_code["country_codenumber"]?> </option> 
        <?php } ?>
        </select>
        <p class="errors"> <?= $errors["country_ID"] ?? "" ?> </p>
    
        <label for="phone_number">Phone Number</label>
        <input type="text" name="phone_number" id="phone_number" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $guide["phone_number"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["phone_number"] ?? "" ?> </p>
        
        <br><br>
        <h3>Emergency Info</h3>
        <label for="emergency_name"> Emergency Name </label>
        <input type="text" name="emergency_name" id="emergency_name" value ="<?= $guide["emergency_name"] ?? "" ?>" >
        <p style="color: red; font-weight: bold;"> <?= $errors["emergency_name"] ?? "" ?> </p>

        <label for="emergency_relationship"> Emergency Relationship </label>
        <input type="text" name="emergency_relationship" id="emergency_relationship" value = "<?= $guide["emergency_relationship"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["emergency_relationship"] ?? "" ?> </p>

        <label for="emergency_country_ID"> Country Code </label>
        <select name="emergency_country_ID" id="emergency_country_ID">
            <option value="">--SELECT COUNTRY CODE--</option>
            <?php foreach ($guideObj->fetchCountryCode() as $country_code){ 
                $temp = $country_code["country_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($guide["emergency_country_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["country_name"] ?> <?= $country_code["country_codenumber"]?> </option>    
        <?php } ?>
        </select>
        <p class="errors"> <?= $errors["emergency_country_ID"] ?? "" ?> </p>

        <label for="emergency_phonenumber">Phone Number</label>
        <input type="text" name="emergency_phonenumber" id="emergency_phonenumber" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $guide["emergency_phonenumber"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["emergency_phonenumber"] ?? "" ?> </p>

        <h3>Address</h3>
        <label for="address_country_ID"> Country </label>
        <select name="address_country_ID" id="address_country_ID">
            <option value="">--SELECT COUNTRY--</option>
            <?php 
            foreach ($guideObj->fetchCountry() as $country){ 
            ?>
                <option value="<?= $country["country_ID"] ?>" 
                    <?= ($country["country_ID"] == ($guide["address_country_ID"] ?? "")) ? "selected" : "" ?>>
                    <?= $country["country_name"] ?>
                </option>
            <?php } ?>
        </select>
        <p class="error"><?= $errors["address_country_ID"] ?? "" ?></p>

        <label for="address_street"> Street </label>
        <input type="text" name="address_street" id="address_street" value="<?= $guide["address_street"] ?? "" ?>">
        <p class="error"><?= $errors["address_street"] ?? "" ?></p>

        <label for="address_houseno"> House No</label>
        <input type="text" name="address_houseno" id="address_houseno" value="<?= $guide["address_houseno"] ?? "" ?>">
        <p class="error"><?= $errors["address_houseno"] ?? "" ?></p>

        <label for="region_ID"> Region </label>
        <select name="region_ID" id="region_ID">
            <option value="">--SELECT REGION--</option>
            <?php 
            $regions = $guideObj->fetchRegion();
            if ($regions && is_array($regions)) {
                foreach ($regions as $region){ ?>
                    <option value="<?= $region["region_ID"] ?>" 
                        <?= ($region["region_ID"] == ($guide["region_ID"] ?? "")) ? "selected" : "" ?>>
                        <?= $region["region_name"] ?>
                    </option>
                <?php }
            } ?>
        </select>
        <p class="error"><?= $errors["region_ID"] ?? "" ?></p>

        <label for="province_ID"> Province </label>
        <select name="province_ID" id="province_ID">
            <option value="">--SELECT PROVINCE--</option>
            <?php 
            $selectedRegion = $guide["region_ID"] ?? "";
            $provinces = $guideObj->fetchProvince($selectedRegion);
            if ($provinces && is_array($provinces)) {
                foreach ($provinces as $province){ ?>
                    <option value="<?= $province["province_ID"] ?>" 
                        <?= ($province["province_ID"] == ($guide["province_ID"] ?? "")) ? "selected" : "" ?>>
                        <?= $province["province_name"] ?>
                    </option>
                <?php }
            } ?>
        </select>
        <p class="error"><?= $errors["province_ID"] ?? "" ?></p>

        <label for="city_ID"> City/Municipality </label>
        <select name="city_ID" id="city_ID">
            <option value="">--SELECT CITY--</option>
            <?php
            $selectedProvince = $guide["province_ID"] ?? "";
            $cities = $guideObj->fetchCity($selectedProvince);
            if ($cities && is_array($cities)) {
                foreach ($cities as $city){ ?>
                    <option value="<?= $city["city_ID"] ?>" 
                        <?= ($city["city_ID"] == ($guide["city_ID"] ?? "")) ? "selected" : "" ?>>
                        <?= $city["city_name"] ?>
                    </option>
                <?php }
            } ?>
        </select>
        <p class="error"><?= $errors["city_ID"] ?? "" ?></p>

        <label for="barangay_ID"> Barangay </label>
        <select name="barangay_ID" id="barangay_ID">
            <option value="">--SELECT BARANGAY--</option>
            <?php
            $selectedCity = $guide["city_ID"] ?? "";
            $barangays = $guideObj->fetchBarangay($selectedCity);
            if ($barangays && is_array($barangays)) {
                foreach ($barangays as $barangay){ ?>
                    <option value="<?= $barangay["barangay_ID"] ?>" 
                        <?= ($barangay["barangay_ID"] == ($guide["barangay_ID"] ?? "")) ? "selected" : "" ?>>
                        <?= $barangay["barangay_name"] ?>
                    </option>
                <?php }
            } ?>
        </select>
        <p class="error"><?= $errors["barangay_ID"] ?? "" ?></p>

        <button type="submit">Register</button>
    </form>

</body>

</html>
