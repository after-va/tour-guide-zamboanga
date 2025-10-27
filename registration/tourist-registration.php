<?php
require_once "../classes/tourist.php";

ini_set('display_errors', 1);
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
        "address_houseno", "address_street", "address_barangay",
        "address_city", "address_province", "address_region", "address_country", 
        "country_ID", "phone_number", "emergency_name", "emergency_country_ID",
        "emergency_phonenumber", "emergency_relationship", "contactinfo_email",
        "person_nationality", "person_gender", "person_civilstatus", "person_dateofbirth",
        "username", "password"
    ];

    foreach ($required as $field) {
        if (empty($tourist[$field])) {
            $errors[$field] = ucfirst(str_replace("_", " ", $field)) . " is required.";
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
        $country_ID = $tourist["country_ID"];

        if ($country_ID == 161) {
            // Use existing dropdown IDs
            $region_ID = $_POST["region_ID"];
            $province_ID = $_POST["province_ID"];
            $city_ID = $_POST["city_ID"];
            $barangay_ID = $_POST["barangay_ID"];
        } else {
            // Insert new Region if not exists
            $region_ID = $touristObj->addgetRegion($_POST["region_name"], $country_ID);
            $province_ID = $touristObj->addgetProvince($_POST["province_name"], $region_ID);
            $city_ID = $touristObj->addgetCity($_POST["city_name"], $province_ID);
            $barangay_ID = $touristObj->addgetBarangay($_POST["barangay_name"], $city_ID);
        }


        $results = $touristObj->addTourist($tourist["name_first"], $tourist["name_second"] ?? null, $tourist["name_middle"] ?? null, $tourist["name_last"], $tourist["name_suffix"] ?? null,
        $tourist["address_houseno"], $tourist["address_street"], $tourist["address_barangay"],
        $tourist["country_ID"], $tourist["phone_number"],
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
            <label for="country_ID"> Country Code </label>
            <select name="country_ID" id="country_ID">
                <option value="">--SELECT COUNTRY CODE--</option>

                <?php foreach ($touristObj->fetchCountryCode() as $country_code){ 
                    $temp = $country_code["country_ID"];
                ?>
                <option value="<?= $temp ?>" <?= ($temp == ($tourist["country_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["country_name"] ?> <?= $country_code["country_codenumber"]?> </option> 
            <?php } ?>
            </select>
            <p class="errors"> <?= $errors["country_ID"] ?? "" ?> </p>
        
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

        <div id="other-fields" style="display:none;">
            <input type="text" name="region_name" placeholder="Region">
            <input type="text" name="province_name" placeholder="Province">
            <input type="text" name="city_name" placeholder="City">
            <input type="text" name="barangay_name" placeholder="Barangay">
        </div>


        <div id="ph-fields" style="display:none;">
            <label for="address_barangay"> Barangay </label>
            <select name ="address_barangay" id="address_barangay">
                <option value="">--SELECT BARANGAY--</option>
                <?php foreach ($touristObj->fetchBarangay() as $barangay){ ?>
                    <option value="<?= $barangay["barangay_ID"] ?>"><?= $barangay["barangay_name"] ?></option>
                <?php } ?>
            </select>
            <p class="error"><?= $errors["address_barangay"] ?? "" ?></p>

            <label for="address_city"> City/Municipality</label>
            <select name ="address_city" id="address_city" onchange="loadBarangays()">
                <option value="">--SELECT CITY/MUNICIPALITY--</option>
                <?php foreach ($touristObj->fetchCityMunicipality() as $citymunicipality){ ?>
                    <option value="<?= $citymunicipality["citymunicipality_ID"] ?>"><?= $citymunicipality["citymunicipality_name"] ?></option>
                <?php } ?>
            </select>
            <p class="error"><?= $errors["address_city"] ?? "" ?></p>


            <label for="address_province"> Province </label>
            <select name ="address_province" id="address_province" onchange="loadCities()">
                <option value="">--SELECT PROVINCE--</option>
                <?php foreach ($touristObj->fetchProvince() as $province){ ?>
                    <option value="<?= $province["province_ID"] ?>"><?= $province["province_name"] ?></option>
                <?php } ?>
            </select>
            <p class="error"><?= $errors["address_province"] ?? "" ?></p>

            <label for="address_region"> Region </label>
            <select name ="address_region" id="address_region" onchange="loadProvinces()">
                <option value="">--SELECT REGION--</option>
                <?php foreach ($touristObj->fetchRegion() as $region){ ?>
                    <option value="<?= $region["region_ID"] ?>"><?= $region["region_name"] ?></option>
                <?php } ?>
            </select>
            <p class="error"><?= $errors["address_region"] ?? "" ?></p>
        </div>

        <label for="address_country"> Country </label>
        <select name="address_country" id="address_country" onchange="toggleAddressFields()">
            <option value="">--SELECT COUNTRY--</option>
            <?php foreach ($touristObj->fetchCountry() as $country){ ?>
                <option value="<?= $country["country_ID"] ?>"><?= $country["country_name"] ?></option>
            <?php } ?>
        </select>
        <p class="error"><?= $errors["address_country"] ?? "" ?></p>

        <button type="submit">Register</button>
    </form>

    <script>
        function toggleAddressFields() {
            let country = document.getElementById("address_country").value;
            let phFields = document.getElementById("ph-fields");
            let otherFields = document.getElementById("other-fields");

            if (country == "161") { // Philippines
                phFields.style.display = "block";
                otherFields.style.display = "none";
            } else {
                phFields.style.display = "none";
                otherFields.style.display = "block";
            }
        }
    </script>
</body>

</html>
