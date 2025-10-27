<?php
require_once "classes/tourist.php";
require_once "classes/auth.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);


$touristObj = new Tourist();
$authObj = new Auth();

$tourist = [];
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs dynamically
    foreach ($_POST as $key => $value) {
        $tourist[$key] = trim(htmlspecialchars($value));
    }

    // === Validation ===
    $required = [
        "name_first", "name_last", "address_houseno", "address_street", "address_barangay",
        "address_city", "address_province", "address_country", "countrycode_ID",
        "phone_number", "emergency_name", "emergency_countrycode_ID",
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
        $person_ID = $touristObj->addTourist(
            $tourist["name_first"], $tourist["name_second"] ?? null, $tourist["name_middle"] ?? null,
            $tourist["name_last"], $tourist["name_suffix"] ?? null,
            $tourist["address_houseno"], $tourist["address_street"], $tourist["address_barangay"],
            $tourist["address_city"], $tourist["address_province"], $tourist["address_country"],
            $tourist["countrycode_ID"], $tourist["phone_number"],
            $tourist["emergency_name"], $tourist["emergency_countrycode_ID"],
            $tourist["emergency_phonenumber"], $tourist["emergency_relationship"],
            $tourist["contactinfo_email"], $tourist["person_nationality"],
            $tourist["person_gender"], $tourist["person_civilstatus"], $tourist["person_dateofbirth"]
        );

        if ($person_ID) {
            // Create login for this tourist
            $create = $authObj->createUserLogin($person_ID, $tourist["username"], $tourist["password"], "Tourist");

            if ($create === "username_exists") {
                $errors["username"] = "Username already exists.";
            } elseif ($create === "role_missing") {
                $errors["general"] = "Tourist role is not found in Role_Info table.";
            } elseif ($create === false) {
                $errors["general"] = "Failed to create login account.";
            } else {
                $success = "Tourist successfully registered!";
                $tourist = [];
            }
        } else {
            $errors["general"] = "Failed to save tourist info (duplicate or invalid data).";
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

        <h3>Contact Info</h3>
        <label>Phone Number</label>
        <input type="text" name="phone_number" maxlength="10" value="<?= $tourist["phone_number"] ?? "" ?>">
        <p class="error"><?= $errors["phone_number"] ?? "" ?></p>

        <label>Emergency Contact Name</label>
        <input type="text" name="emergency_name" value="<?= $tourist["emergency_name"] ?? "" ?>">
        <p class="error"><?= $errors["emergency_name"] ?? "" ?></p>

        <label>Emergency Phone</label>
        <input type="text" name="emergency_phonenumber" maxlength="10" value="<?= $tourist["emergency_phonenumber"] ?? "" ?>">
        <p class="error"><?= $errors["emergency_phonenumber"] ?? "" ?></p>

        <h3>Address</h3>
        <input type="text" name="address_houseno" placeholder="House No." value="<?= $tourist["address_houseno"] ?? "" ?>">
        <p class="error"><?= $errors["address_houseno"] ?? "" ?></p>

        <input type="text" name="address_street" placeholder="Street" value="<?= $tourist["address_street"] ?? "" ?>">
        <p class="error"><?= $errors["address_street"] ?? "" ?></p>

        <input type="text" name="address_city" placeholder="City" value="<?= $tourist["address_city"] ?? "" ?>">
        <p class="error"><?= $errors["address_city"] ?? "" ?></p>

        <input type="text" name="address_country" placeholder="Country" value="<?= $tourist["address_country"] ?? "" ?>">
        <p class="error"><?= $errors["address_country"] ?? "" ?></p>

        <button type="submit">Register</button>
    </form>
</body>
</html>
