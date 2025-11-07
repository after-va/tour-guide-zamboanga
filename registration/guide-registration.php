<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once "../classes/guide.php";

// Start session to store assigned license for display after redirect
if (session_status() === PHP_SESSION_NONE) session_start();

$guideObj = new Guide();
$guide = [];
$errors = [];
$success = "";
$dbError = "";

// Function to test registration with sample data
function testRegistration($guideObj) {
    error_log("=== STARTING TEST REGISTRATION ===");
    
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
        'password' => 'Test@1234',
        // Guide-specific fields
        'guide_license' => 'TG-' . rand(1000, 9999) . '-ZC',
        'guide_experience' => '5',
        'languages' => ['English', 'Filipino', 'Chavacano'],
        'specializations' => ['Historical', 'Cultural'],
        'bio' => 'Professional tour guide with 5 years of experience in historical and cultural tours around Zamboanga City. Fluent in English, Filipino, and Chavacano.',
        'certifications' => 'DOT Accredited Tour Guide (2020)\nFirst Aid Certified (2023)'
    ];
    
    // Log test data
    error_log("Test Data: " . print_r($testData, true));
    
    // Call addguide directly
    try {
        error_log("Calling addguide with test data");
        $result = $guideObj->addguide(
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
                $testData['password'],
                // License and metadata (addGuide requires license_number as the next required parameter)
                $testData['guide_license'],
                null, // license_type
                date('Y-m-d'), // issue_date
                null, // expiry_date
                'DOT Zamboanga' // issuing_authority
            );
        
        if ($result) {
            error_log("Test registration SUCCESSFUL!");
            return "Test registration successful! Check error log for details.";
        } else {
            $error = $guideObj->getLastError();
            error_log("Test registration FAILED: " . $error);
            return "Test registration failed: " . $error;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Test registration EXCEPTION: " . $error);
        return "Test registration exception: " . $error;
    }
}


// Check if test registration was requested
if (isset($_GET['test_register'])) {
    $guideObj = new Guide();
    $testResult = testRegistration($guideObj);
    die($testResult);
}

// Initialize guide object
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
    error_log("guide object initialization error: " . $e->getMessage());
}
// Process POST request
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

        // Auto-generate a license number (override any user input). The system will assign a random unique license.
        $generated = $guideObj->generateLicenseNumber();
        if ($generated === false) {
            $errors['guide_license'] = 'Failed to generate license number. Please try again later.';
        } else {
            $guide['guide_license'] = $generated;
        }

        // === Validation ===
        $required = [
            "name_first", "name_last", 
            "address_houseno", "address_street", "address_country_ID", 
            "country_ID", "emergency_name", "emergency_country_ID",
            "emergency_phonenumber", "emergency_relationship", "contactinfo_email",
            "person_nationality", "person_gender", "person_dateofbirth",
            "username", "password",
            // Guide-specific required fields
            "guide_license", "guide_experience", "languages", "specializations", "bio"
        ]; // Removed phone_number from required fields
        
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
    
    // Remove phone_number from required fields if empty
    if (empty($guide["phone_number"])) {
        $key = array_search("phone_number", $required);
        if ($key !== false) {
            unset($required[$key]);
        }
    }

        // Validate email
        if (!empty($guide["contactinfo_email"]) && !filter_var($guide["contactinfo_email"], FILTER_VALIDATE_EMAIL)) {
            $errors["contactinfo_email"] = "Invalid email format.";
        }

        // Guide-specific validations
        if (empty($guide["languages"]) || !is_array($guide["languages"]) || count($guide["languages"]) < 1) {
            $errors["languages"] = "Please select at least one language.";
        }

        if (empty($guide["specializations"]) || !is_array($guide["specializations"]) || count($guide["specializations"]) < 1) {
            $errors["specializations"] = "Please select at least one area of expertise.";
        }

        if (!empty($guide["guide_experience"]) && (!is_numeric($guide["guide_experience"]) || $guide["guide_experience"] < 0)) {
            $errors["guide_experience"] = "Please enter a valid number of years.";
        }

        if (empty($guide["bio"]) || strlen($guide["bio"]) < 50) {
            $errors["bio"] = "Please provide a bio of at least 50 characters.";
        }

        // Validate license number format (you can adjust this based on your requirements)
        if (empty($guide["guide_license"]) || !preg_match("/^[A-Z0-9-]+$/", $guide["guide_license"])) {
            $errors["guide_license"] = "Please enter a valid license number (uppercase letters, numbers, and hyphens only).";
        }
        
        if (!empty($guide["guide_type"]) && !in_array($guide["guide_type"], ['Local', 'Regional', 'National'])) {
            $errors["guide_type"] = "Please select a valid guide type.";
        }    // Proceed if no errors
    if (empty($errors)) {
        // Handle non-Philippines addresses by converting text to IDs
        try {
            $result = $touristObj->addTourist(
                $tourist['name_first'] ?? '',
                $tourist['name_second'] ?? '',
                $tourist['name_middle'] ?? '',
                $tourist['name_last'] ?? '',
                $tourist['name_suffix'] ?? '',
                $tourist['address_houseno'] ?? '',
                $tourist['address_street'] ?? '',
                $tourist['barangay_ID'] ?? '',
                $tourist['country_ID'] ?? '',
                $tourist['phone_number'] ?? '',
                $tourist['emergency_name'] ?? '',
                $tourist['emergency_country_ID'] ?? '',
                $tourist['emergency_phonenumber'] ?? '',
                $tourist['emergency_relationship'] ?? '',
                $tourist['contactinfo_email'] ?? '',
                $tourist['person_nationality'] ?? '',
                $tourist['person_gender'] ?? '',
                $tourist['person_dateofbirth'] ?? '',
                $tourist['username'] ?? '',
                $tourist['password'] ?? ''
            );

            if ($result) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit();
            } else {
                $errors["general"] = "Registration failed: " . $touristObj->getLastError();
            }

        } catch (Exception $e) {
            $errors["general"] = "System error: " . $e->getMessage();
        }
    }
}}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>guide Registration</title>
    
    
    <script>
        // Define functions in head to ensure they're available when HTML loads
        function toggleAddressFields(countryID) {
            console.log("Country ID selected:", countryID);
            
            // Get all elements first
            const regionDropdown = document.getElementById("region_dropdown_container");
            const regionText = document.getElementById("region_text_container");
            const provinceDropdown = document.getElementById("province_dropdown_container");
            const provinceText = document.getElementById("province_text_container");
            const cityDropdown = document.getElementById("city_dropdown_container");
            const cityText = document.getElementById("city_text_container");
            const barangayDropdown = document.getElementById("barangay_dropdown_container");
            const barangayText = document.getElementById("barangay_text_container");
            
            if (!countryID || countryID === "") {
                if (regionDropdown) regionDropdown.style.display = "none";
                if (regionText) regionText.style.display = "none";
                if (provinceDropdown) provinceDropdown.style.display = "none";
                if (provinceText) provinceText.style.display = "none";
                if (cityDropdown) cityDropdown.style.display = "none";
                if (cityText) cityText.style.display = "none";
                if (barangayDropdown) barangayDropdown.style.display = "none";
                if (barangayText) barangayText.style.display = "none";
                return;
            }
            
            const isPhilippines = (countryID == "161");
            console.log("Is Philippines?", isPhilippines);
            
            // Toggle Region
            if (regionDropdown) regionDropdown.style.display = isPhilippines ? "block" : "none";
            if (regionText) regionText.style.display = isPhilippines ? "none" : "block";
            const regionID = document.getElementById("region_ID");
            const regionName = document.getElementById("region_name");
            if (regionID) regionID.disabled = !isPhilippines;
            if (regionName) regionName.disabled = isPhilippines;
            
            // Toggle Province
            if (provinceDropdown) provinceDropdown.style.display = isPhilippines ? "block" : "none";
            if (provinceText) provinceText.style.display = isPhilippines ? "none" : "block";
            const provinceID = document.getElementById("province_ID");
            const provinceName = document.getElementById("province_name");
            if (provinceID) provinceID.disabled = !isPhilippines;
            if (provinceName) provinceName.disabled = isPhilippines;
            
            // Toggle City
            if (cityDropdown) cityDropdown.style.display = isPhilippines ? "block" : "none";
            if (cityText) cityText.style.display = isPhilippines ? "none" : "block";
            const cityID = document.getElementById("city_ID");
            const cityName = document.getElementById("city_name");
            if (cityID) cityID.disabled = !isPhilippines;
            if (cityName) cityName.disabled = isPhilippines;
            
            // Toggle Barangay
            if (barangayDropdown) barangayDropdown.style.display = isPhilippines ? "block" : "none";
            if (barangayText) barangayText.style.display = isPhilippines ? "none" : "block";
            const barangayID = document.getElementById("barangay_ID");
            const barangayName = document.getElementById("barangay_name");
            if (barangayID) barangayID.disabled = !isPhilippines;
            if (barangayName) barangayName.disabled = isPhilippines;
            
            // Clear values
            if (!isPhilippines) {
                if (regionID) regionID.value = "";
                if (provinceID) provinceID.value = "";
                if (cityID) cityID.value = "";
                if (barangayID) barangayID.value = "";
            } else {
                if (regionName) regionName.value = "";
                if (provinceName) provinceName.value = "";
                if (cityName) cityName.value = "";
                if (barangayName) barangayName.value = "";
                loadRegions(countryID);
            }
        }

        function loadRegions(countryID) {
            fetch("fetch-region.php?country_ID=" + countryID)
                .then(res => res.text())
                .then(data => {
                    const regionEl = document.getElementById("region_ID");
                    const provinceEl = document.getElementById("province_ID");
                    const cityEl = document.getElementById("city_ID");
                    const barangayEl = document.getElementById("barangay_ID");
                    
                    if (regionEl) regionEl.innerHTML = data;
                    if (provinceEl) provinceEl.innerHTML = "<option value=''>--SELECT PROVINCE--</option>";
                    if (cityEl) cityEl.innerHTML = "<option value=''>--SELECT CITY--</option>";
                    if (barangayEl) barangayEl.innerHTML = "<option value=''>--SELECT BARANGAY--</option>";
                })
                .catch(err => console.error("Error loading regions:", err));
        }

        function loadProvinces(regionID) {
            fetch("fetch-province.php?region_ID=" + regionID)
                .then(res => res.text())
                .then(data => {
                    const provinceEl = document.getElementById("province_ID");
                    const cityEl = document.getElementById("city_ID");
                    const barangayEl = document.getElementById("barangay_ID");
                    
                    if (provinceEl) {
                        provinceEl.innerHTML = data;
                        provinceEl.disabled = false; // Enable province dropdown
                    }
                    if (cityEl) {
                        cityEl.innerHTML = "<option value=''>--SELECT CITY--</option>";
                        cityEl.disabled = true; // Keep city disabled until province is selected
                    }
                    if (barangayEl) {
                        barangayEl.innerHTML = "<option value=''>--SELECT BARANGAY--</option>";
                        barangayEl.disabled = true; // Keep barangay disabled
                    }
                })
                .catch(err => console.error("Error loading provinces:", err));
        }

        function loadCities(provinceID) {
            fetch("fetch-city.php?province_ID=" + provinceID)
                .then(res => res.text())
                .then(data => {
                    const cityEl = document.getElementById("city_ID");
                    const barangayEl = document.getElementById("barangay_ID");
                    
                    if (cityEl) {
                        cityEl.innerHTML = data;
                        cityEl.disabled = false; // Enable city dropdown
                    }
                    if (barangayEl) {
                        barangayEl.innerHTML = "<option value=''>--SELECT BARANGAY--</option>";
                        barangayEl.disabled = true; // Keep barangay disabled until city is selected
                    }
                })
                .catch(err => console.error("Error loading cities:", err));
        }

        function loadBarangays(cityID) {
            fetch("fetch-barangay.php?city_ID=" + cityID)
                .then(res => res.text())
                .then(data => {
                    const barangayEl = document.getElementById("barangay_ID");
                    if (barangayEl) {
                        barangayEl.innerHTML = data;
                        barangayEl.disabled = false; // Enable barangay dropdown
                    }
                })
                .catch(err => console.error("Error loading barangays:", err));
        }

        window.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById("address_country_ID");
            if (countrySelect && countrySelect.value) {
                toggleAddressFields(countrySelect.value);
            }
        });
    </script>
</head>
<body>
    <div style="background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h2>guide Registration</h2>
        <div style="margin: 15px 0;">
            <a href="?test_register=1" class="test-button" 
               onclick="return confirm('This will create a test registration. Continue?')">
                Run Test Registration
            </a>
            <small style="display: block; margin-top: 5px; color: #666;">
                This will create a test registration with sample data to help identify issues.
            </small>
        </div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success">
            Registration successful! Your guide account has been created.
            <?php if (!empty($_SESSION['assigned_license'])): ?>
                <p>Your Guide License Number: <strong><?= htmlspecialchars($_SESSION['assigned_license']) ?></strong></p>
                <?php unset($_SESSION['assigned_license']); ?>
            <?php endif; ?>
        </div>
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

        <h3>Guide Information</h3>
        <!-- Tour Guide License Number is auto-generated and not editable by the applicant -->
        <label for="guide_type">Guide Type</label>
        <select name="guide_type" id="guide_type">
            <option value="">Select Guide Type</option>
            <option value="Local" <?= ($guide["guide_type"] ?? "") === "Local" ? "selected" : "" ?>>Local Guide</option>
            <option value="Regional" <?= ($guide["guide_type"] ?? "") === "Regional" ? "selected" : "" ?>>Regional Guide</option>
            <option value="National" <?= ($guide["guide_type"] ?? "") === "National" ? "selected" : "" ?>>National Guide</option>
        </select>
        <p class="error"><?= $errors["guide_type"] ?? "" ?></p>

        <label for="guide_experience">Years of Experience</label>
        <input type="number" name="guide_experience" id="guide_experience" min="0" step="1" value="<?= $guide["guide_experience"] ?? "" ?>">
        <p class="error"><?= $errors["guide_experience"] ?? "" ?></p>

        <label>Languages Spoken</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="languages[]" value="English" <?= isset($guide["languages"]) && in_array("English", $guide["languages"]) ? 'checked' : '' ?>>
                English
            </label>
            <label>
                <input type="checkbox" name="languages[]" value="Filipino" <?= isset($guide["languages"]) && in_array("Filipino", $guide["languages"]) ? 'checked' : '' ?>>
                Filipino
            </label>
            <label>
                <input type="checkbox" name="languages[]" value="Chavacano" <?= isset($guide["languages"]) && in_array("Chavacano", $guide["languages"]) ? 'checked' : '' ?>>
                Chavacano
            </label>
            <label>
                <input type="checkbox" name="languages[]" value="Other" <?= isset($guide["languages"]) && in_array("Other", $guide["languages"]) ? 'checked' : '' ?>>
                Other
            </label>
        </div>
        <p class="error"><?= $errors["languages"] ?? "" ?></p>

        <label for="specializations">Areas of Expertise (Select all that apply)</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="specializations[]" value="Historical" <?= isset($guide["specializations"]) && in_array("Historical", $guide["specializations"]) ? 'checked' : '' ?>>
                Historical Sites
            </label>
            <label>
                <input type="checkbox" name="specializations[]" value="Cultural" <?= isset($guide["specializations"]) && in_array("Cultural", $guide["specializations"]) ? 'checked' : '' ?>>
                Cultural Tours
            </label>
            <label>
                <input type="checkbox" name="specializations[]" value="Nature" <?= isset($guide["specializations"]) && in_array("Nature", $guide["specializations"]) ? 'checked' : '' ?>>
                Nature & Adventure
            </label>
            <label>
                <input type="checkbox" name="specializations[]" value="Food" <?= isset($guide["specializations"]) && in_array("Food", $guide["specializations"]) ? 'checked' : '' ?>>
                Food & Culinary
            </label>
        </div>
        <p class="error"><?= $errors["specializations"] ?? "" ?></p>

        <label for="certifications">Certifications/Training (Optional)</label>
        <textarea name="certifications" id="certifications" rows="3" placeholder="List any relevant certifications or training programs"><?= $guide["certifications"] ?? "" ?></textarea>
        <p class="error"><?= $errors["certifications"] ?? "" ?></p>

        <label for="bio">Brief Bio</label>
        <textarea name="bio" id="bio" rows="4" placeholder="Introduce yourself and describe your experience as a tour guide"><?= $guide["bio"] ?? "" ?></textarea>
        <p class="error"><?= $errors["bio"] ?? "" ?></p>

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
        <select name="address_country_ID" id="address_country_ID" onchange="toggleAddressFields(this.value)">
            <option value="">--SELECT COUNTRY--</option>
            <?php 
            foreach ($guideObj->fetchCountry() as $country){ 
                // Debug: Print Philippines ID to HTML comment
                if (stripos($country["country_name"], "Philippines") !== false) {
                    echo "<!-- Philippines country_ID: " . $country["country_ID"] . " -->";
                }
            ?>
                <option value="<?= $country["country_ID"] ?>" 
                    <?= ($country["country_ID"] == ($guide["address_country_ID"] ?? "")) ? "selected" : "" ?>>
                    <?= $country["country_name"] ?>
                </option>
            <?php } ?>
        </select>
        <p class="error"><?= $errors["address_country_ID"] ?? "" ?></p>

        <!-- Region Field - Dropdown for PH, Text Input for others -->
        <div id="region_dropdown_container" style="display: none;">
            <label for="region_ID"> Region </label>
            <select name="region_ID" id="region_ID" onchange="loadProvinces(this.value)" disabled>
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
        </div>
        <div id="region_text_container" style="display: none;">
            <label for="region_name"> Region </label>
            <input type="text" name="region_name" id="region_name" value="<?= $guide["region_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["region_name"] ?? "" ?></p>
        </div>

        <!-- Province Field - Dropdown for PH, Text Input for others -->
        <div id="province_dropdown_container" style="display: none;">
            <label for="province_ID"> Province </label>
            <select name="province_ID" id="province_ID" onchange="loadCities(this.value)" disabled>
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
        </div>
        <div id="province_text_container" style="display: none;">
            <label for="province_name"> Province </label>
            <input type="text" name="province_name" id="province_name" value="<?= $guide["province_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["province_name"] ?? "" ?></p>
        </div>

        <!-- City Field - Dropdown for PH, Text Input for others -->
        <div id="city_dropdown_container" style="display: none;">
            <label for="city_ID"> City/Municipality </label>
            <select name="city_ID" id="city_ID" onchange="loadBarangays(this.value)" disabled>
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
        </div>
        <div id="city_text_container" style="display: none;">
            <label for="city_name"> City/Municipality </label>
            <input type="text" name="city_name" id="city_name" value="<?= $guide["city_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["city_name"] ?? "" ?></p>
        </div>

        <!-- Barangay Field - Dropdown for PH, Text Input for others -->
        <div id="barangay_dropdown_container" style="display: none;">
            <label for="barangay_ID"> Barangay </label>
            <select name="barangay_ID" id="barangay_ID" disabled>
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
        </div>
        <div id="barangay_text_container" style="display: none;">
            <label for="barangay_name"> Barangay </label>
            <input type="text" name="barangay_name" id="barangay_name" value="<?= $guide["barangay_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["barangay_name"] ?? "" ?></p>
        </div>

        <label for="address_street"> Street </label>
        <input type="text" name="address_street" id="address_street" value="<?= $guide["address_street"] ?? "" ?>">
        <p class="error"><?= $errors["address_street"] ?? "" ?></p>

        <label for="address_houseno"> House No</label>
        <input type="text" name="address_houseno" id="address_houseno" value="<?= $guide["address_houseno"] ?? "" ?>">
        <p class="error"><?= $errors["address_houseno"] ?? "" ?></p>

        <button type="submit">Register</button>
    </form>

</body>

</html>
