<?php
require_once "../classes/tourist.php";

ini_set('display_errors', 0); // Temporarily disabled to prevent breaking JavaScript
error_reporting(E_ALL);


$touristObj = new Tourist();

$tourist = [];
$errors = [];
$success = "";
//addTourist($firstname, $middlename, $lastname, $suffix,
                                    // $houseno, $street, $barangay,
                                    // $country_ID, $phone_number,
                                    // $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
                                    // $contactinfo_email,
                                    // $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth,
                                    // $username, $password)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs dynamically
    foreach ($_POST as $key => $value) {
        $tourist[$key] = trim(htmlspecialchars($value));
    }

    // === Validation ===
    $required = [
        "name_first", "name_last", 
        "address_houseno", "address_street", "address_country_ID", 
        "phone_country_ID", "phone_number", "emergency_name", "emergency_country_ID",
        "emergency_phonenumber", "emergency_relationship", "contactinfo_email",
        "person_nationality", "person_gender", "person_civilstatus", "person_dateofbirth",
        "username", "password"
    ];

    foreach ($required as $field) {
        if (empty($tourist[$field])) {
            $errors[$field] = ucfirst(str_replace("_", " ", $field)) . " is required.";
        }
    }

    // Additional validation based on country
    if (!empty($tourist["address_country_ID"])) {
        if ($tourist["address_country_ID"] == "161") {
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
            if (empty($tourist[$field])) {
                $errors[$field] = ucfirst(str_replace("_", " ", $field)) . " is required.";
            }
        }
    }


    // Validate phone numbers
    if (!empty($tourist["phone_number"]) && strlen($tourist["phone_number"]) < 10) {
        $errors["phone_number"] = "Phone Number must be at least 10 digits.";
    }
    if (!empty($tourist["emergency_phonenumber"]) && strlen($tourist["emergency_phonenumber"]) < 10) {
        $errors["emergency_phonenumber"] = "Emergency Phone must be at least 10 digits.";
    }

    // Validate email
    if (!empty($tourist["contactinfo_email"]) && !filter_var($tourist["contactinfo_email"], FILTER_VALIDATE_EMAIL)) {
        $errors["contactinfo_email"] = "Invalid email format.";
    }

    // Proceed if no errors
    if (empty($errors)) {
        // Handle non-Philippines addresses by converting text to IDs
        $barangay_ID = null;
        
        if ($tourist["address_country_ID"] == "161") {
            // Philippines - use existing barangay_ID
            $barangay_ID = $tourist["barangay_ID"];
        } else {
            // Other countries - create/get IDs from text inputs
            $db = $touristObj->connect();
            
            // Get or create Region
            $region_ID = $touristObj->addgetRegion($tourist["region_name"], $tourist["address_country_ID"], $db);
            
            // Get or create Province
            $province_ID = $touristObj->addgetProvince($tourist["province_name"], $region_ID, $db);
            
            // Get or create City
            $city_ID = $touristObj->addgetCity($tourist["city_name"], $province_ID, $db);
            
            // Get or create Barangay
            $barangay_ID = $touristObj->addgetBarangay($tourist["barangay_name"], $city_ID, $db);
        }

        $results = $touristObj->addTourist($tourist["name_first"], $tourist["name_second"] ?? null, $tourist["name_middle"] ?? null, $tourist["name_last"], $tourist["name_suffix"] ?? null,
        $tourist["address_houseno"], $tourist["address_street"], $barangay_ID,
        $tourist["phone_country_ID"], $tourist["phone_number"],
        $tourist["emergency_name"], $tourist["emergency_country_ID"], $tourist["emergency_phonenumber"], $tourist["emergency_relationship"],
        $tourist["contactinfo_email"],
        $tourist["person_nationality"], $tourist["person_gender"], $tourist["person_civilstatus"], $tourist["person_dateofbirth"], 
        $tourist["username"], $tourist["password"]);

        // $name_first, $name_second, $name_middle, $name_last, $name_suffix,
        // $houseno, $street, $barangay,
        // $country_ID, $phone_number,
        // $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
        // $contactinfo_email,
        // $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth, 
        // $username, $password

        if($results){
            header("Location: tourist-registration.php");
            $tourist = [];
            exit;
        } else {
            $errors["general"] = "Failed to save data. Please check for duplicate phone number entries.";
        }
    }
        
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tourist Registration</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        form { max-width: 600px; margin: auto; display: flex; flex-direction: column; gap: 10px; }
        label { font-weight: bold; }
        input, select { padding: 6px; }
        .error { color: red; font-size: 0.9em; }
        .success { color: green; font-weight: bold; }
    </style>
    
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
                    
                    if (provinceEl) provinceEl.innerHTML = data;
                    if (cityEl) cityEl.innerHTML = "<option value=''>--SELECT CITY--</option>";
                    if (barangayEl) barangayEl.innerHTML = "<option value=''>--SELECT BARANGAY--</option>";
                })
                .catch(err => console.error("Error loading provinces:", err));
        }

        function loadCities(provinceID) {
            fetch("fetch-city.php?province_ID=" + provinceID)
                .then(res => res.text())
                .then(data => {
                    const cityEl = document.getElementById("city_ID");
                    const barangayEl = document.getElementById("barangay_ID");
                    
                    if (cityEl) cityEl.innerHTML = data;
                    if (barangayEl) barangayEl.innerHTML = "<option value=''>--SELECT BARANGAY--</option>";
                })
                .catch(err => console.error("Error loading cities:", err));
        }

        function loadBarangays(cityID) {
            fetch("fetch-barangay.php?city_ID=" + cityID)
                .then(res => res.text())
                .then(data => {
                    const barangayEl = document.getElementById("barangay_ID");
                    if (barangayEl) barangayEl.innerHTML = data;
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
    <h2>Tourist Registration</h2>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <?php if (!empty($errors["general"])): ?>
        <p class="error"><?= $errors["general"] ?></p>
    <?php endif; ?>

    <form method="POST">
        <h3>Account Info</h3>
        <label>Username</label>
        <input type="text" name="username" value="<?= $tourist["username"] ?? "" ?>">
        <p class="error"><?= $errors["username"] ?? "" ?></p>

        <label>Password</label>
        <input type="password" name="password">
        <p class="error"><?= $errors["password"] ?? "" ?></p>

        <h3>Basic Info</h3>
        <label>First Name</label>
        <input type="text" name="name_first" value="<?= $tourist["name_first"] ?? "" ?>">
        <p class="error"><?= $errors["name_first"] ?? "" ?></p>

        <label>Last Name</label>
        <input type="text" name="name_last" value="<?= $tourist["name_last"] ?? "" ?>">
        <p class="error"><?= $errors["name_last"] ?? "" ?></p>

        <label>Email</label>
        <input type="email" name="contactinfo_email" value="<?= $tourist["contactinfo_email"] ?? "" ?>">
        <p class="error"><?= $errors["contactinfo_email"] ?? "" ?></p>

        <label>Nationality</label>
        <input type="text" name="person_nationality" value="<?= $tourist["person_nationality"] ?? "" ?>">
        <p class="error"><?= $errors["person_nationality"] ?? "" ?></p>

        <label>Gender</label>
        <select name="person_gender">
            <option value="">--Select--</option>
            <option value="Male" <?= ($tourist["person_gender"] ?? "") === "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= ($tourist["person_gender"] ?? "") === "Female" ? "selected" : "" ?>>Female</option>
        </select>
        <p class="error"><?= $errors["person_gender"] ?? "" ?></p>

        <label>Date of Birth</label>
        <input type="date" name="person_dateofbirth" value="<?= $tourist["person_dateofbirth"] ?? "" ?>">
        <p class="error"><?= $errors["person_dateofbirth"] ?? "" ?></p>

        <h3>Phone Number</h3>
            <label for="phone_country_ID"> Country Code </label>
            <select name="phone_country_ID" id="phone_country_ID">
                <option value="">--SELECT COUNTRY CODE--</option>

                <?php foreach ($touristObj->fetchCountryCode() as $country_code){ 
                    $temp = $country_code["country_ID"];
                ?>
                <option value="<?= $temp ?>" <?= ($temp == ($tourist["phone_country_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["country_name"] ?> <?= $country_code["country_codenumber"]?> </option> 
            <?php } ?>
            </select>
            <p class="errors"> <?= $errors["phone_country_ID"] ?? "" ?> </p>
        
        <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $tourist["phone_number"] ?? "" ?>">
            <p style="color: red; font-weight: bold;"> <?= $errors["phone_number"] ?? "" ?> </p>
            
        <br><br>
        <h3>Emergency Info</h3>
            <label for="emergency_name"> Emergency Name </label>
                <input type="text" name="emergency_name" id="emergency_name" value ="<?= $tourist["emergency_name"] ?? "" ?>" >
                <p style="color: red; font-weight: bold;"> <?= $errors["emergency_name"] ?? "" ?> </p>

        <label for="emergency_relationship"> Emergency Relationship </label>
            <input type="text" name="emergency_relationship" id="" value = "<?= $tourist["emergency_relationship"] ?? "" ?>">
            <p style="color: red; font-weight: bold;"> <?= $errors["emergency_relationship"] ?? "" ?> </p>

        
        <label for="emergency_country_ID"> Country Code </label>
        <select name="emergency_country_ID" id="emergency_country_ID">
            <option value="">--SELECT COUNTRY CODE--</option>
            <?php foreach ($touristObj->fetchCountryCode() as $country_code){ 
                $temp = $country_code["country_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($tourist["emergency_country_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["country_name"] ?> <?= $country_code["country_codenumber"]?> </option>    

        <?php } ?>
        </select>
        <p class="errors"> <?= $errors["emergency_country_ID"] ?? "" ?> </p>
        

        <label for="emergency_phonenumber">Phone Number</label>
        <input type="text" name="emergency_phonenumber" id="emergency_phonenumber" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $tourist["emergency_phonenumber"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["emergency_phonenumber"] ?? "" ?> </p>


        <h3>Address</h3>

        <label for="address_country_ID"> Country </label>
        <select name="address_country_ID" id="address_country_ID" onchange="toggleAddressFields(this.value)">
            <option value="">--SELECT COUNTRY--</option>
            <?php 
            foreach ($touristObj->fetchCountry() as $country){ 
                // Debug: Print Philippines ID to HTML comment
                if (stripos($country["country_name"], "Philippines") !== false) {
                    echo "<!-- Philippines country_ID: " . $country["country_ID"] . " -->";
                }
            ?>
                <option value="<?= $country["country_ID"] ?>" 
                    <?= ($country["country_ID"] == ($tourist["address_country_ID"] ?? "")) ? "selected" : "" ?>>
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
                $regions = $touristObj->fetchRegion();
                if ($regions && is_array($regions)) {
                    foreach ($regions as $region){ ?>
                        <option value="<?= $region["region_ID"] ?>" 
                            <?= ($region["region_ID"] == ($tourist["region_ID"] ?? "")) ? "selected" : "" ?>>
                            <?= $region["region_name"] ?>
                        </option>
                    <?php }
                } ?>
            </select>
            <p class="error"><?= $errors["region_ID"] ?? "" ?></p>
        </div>
        <div id="region_text_container" style="display: none;">
            <label for="region_name"> Region </label>
            <input type="text" name="region_name" id="region_name" value="<?= $tourist["region_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["region_name"] ?? "" ?></p>
        </div>

        <!-- Province Field - Dropdown for PH, Text Input for others -->
        <div id="province_dropdown_container" style="display: none;">
            <label for="province_ID"> Province </label>
            <select name="province_ID" id="province_ID" onchange="loadCities(this.value)" disabled>
                <option value="">--SELECT PROVINCE--</option>
                <?php 
                $selectedRegion = $tourist["region_ID"] ?? "";
                $provinces = $touristObj->fetchProvince($selectedRegion);
                if ($provinces && is_array($provinces)) {
                    foreach ($provinces as $province){ ?>
                        <option value="<?= $province["province_ID"] ?>" 
                            <?= ($province["province_ID"] == ($tourist["province_ID"] ?? "")) ? "selected" : "" ?>>
                            <?= $province["province_name"] ?>
                        </option>
                    <?php }
                } ?>
            </select>
            <p class="error"><?= $errors["province_ID"] ?? "" ?></p>
        </div>
        <div id="province_text_container" style="display: none;">
            <label for="province_name"> Province </label>
            <input type="text" name="province_name" id="province_name" value="<?= $tourist["province_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["province_name"] ?? "" ?></p>
        </div>

        <!-- City Field - Dropdown for PH, Text Input for others -->
        <div id="city_dropdown_container" style="display: none;">
            <label for="city_ID"> City/Municipality </label>
            <select name="city_ID" id="city_ID" onchange="loadBarangays(this.value)" disabled>
                <option value="">--SELECT CITY--</option>
                <?php
                $selectedProvince = $tourist["province_ID"] ?? "";
                $cities = $touristObj->fetchCity($selectedProvince);
                if ($cities && is_array($cities)) {
                    foreach ($cities as $city){ ?>
                        <option value="<?= $city["city_ID"] ?>" 
                            <?= ($city["city_ID"] == ($tourist["city_ID"] ?? "")) ? "selected" : "" ?>>
                            <?= $city["city_name"] ?>
                        </option>
                    <?php }
                } ?>
            </select>
            <p class="error"><?= $errors["city_ID"] ?? "" ?></p>
        </div>
        <div id="city_text_container" style="display: none;">
            <label for="city_name"> City/Municipality </label>
            <input type="text" name="city_name" id="city_name" value="<?= $tourist["city_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["city_name"] ?? "" ?></p>
        </div>

        <!-- Barangay Field - Dropdown for PH, Text Input for others -->
        <div id="barangay_dropdown_container" style="display: none;">
            <label for="barangay_ID"> Barangay </label>
            <select name="barangay_ID" id="barangay_ID" disabled>
                <option value="">--SELECT BARANGAY--</option>
                <?php
                $selectedCity = $tourist["city_ID"] ?? "";
                $barangays = $touristObj->fetchBarangay($selectedCity);
                if ($barangays && is_array($barangays)) {
                    foreach ($barangays as $barangay){ ?>
                        <option value="<?= $barangay["barangay_ID"] ?>" 
                            <?= ($barangay["barangay_ID"] == ($tourist["barangay_ID"] ?? "")) ? "selected" : "" ?>>
                            <?= $barangay["barangay_name"] ?>
                        </option>
                    <?php }
                } ?>
            </select>
            <p class="error"><?= $errors["barangay_ID"] ?? "" ?></p>
        </div>
        <div id="barangay_text_container" style="display: none;">
            <label for="barangay_name"> Barangay </label>
            <input type="text" name="barangay_name" id="barangay_name" value="<?= $tourist["barangay_name"] ?? "" ?>" disabled>
            <p class="error"><?= $errors["barangay_name"] ?? "" ?></p>
        </div>

        <label for="address_street"> Street </label>
        <input type="text" name="address_street" id="address_street" value="<?= $tourist["address_street"] ?? "" ?>">
        <p class="error"><?= $errors["address_street"] ?? "" ?></p>

        <label for="address_house"> House No</label>
        <input type="text" name="address_houseno" id="address_houseno" value="<?= $tourist["address_houseno"] ?? "" ?>">
        <p class="error"><?= $errors["address_houseno"] ?? "" ?></p>

        <button type="submit">Register</button>
    </form>

</body>

</html>
