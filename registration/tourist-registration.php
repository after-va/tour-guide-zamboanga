<?php
require_once "../classes/tourist.php";

$touristObj = new Tourist();
$tourist = [];
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize all inputs
    $fields = [
        "name_first", "name_second", "name_middle", "name_last", "name_suffix",
        "houseno", "street", "barangay", "city", "province", "country",
        "countrycode_ID", "phone_number", "emergency_name", "emergency_countrycode_ID",
        "emergency_phonenumber", "emergency_relationship", "contactinfo_email",
        "person_nationality", "person_gender", "person_civilstatus",
        "person_dateofbirth", "username", "password"
    ];

    foreach ($fields as $field) {
        $tourist[$field] = trim(htmlspecialchars($_POST[$field] ?? ""));
    }

    // Basic validation
    if (empty($tourist["name_first"])) $errors["name_first"] = "First name is required.";
    if (empty($tourist["name_last"])) $errors["name_last"] = "Last name is required.";
    if (empty($tourist["person_dateofbirth"])) $errors["person_dateofbirth"] = "Date of birth is required.";
    if (empty($tourist["username"])) $errors["username"] = "Username is required.";
    if (empty($tourist["password"])) $errors["password"] = "Password is required.";
    if (empty($tourist["contactinfo_email"])) $errors["contactinfo_email"] = "Email is required.";
    if (empty($tourist["countrycode_ID"])) $errors["countrycode_ID"] = "Country code is required.";

    // Only register if no errors
    if (empty($errors)) {
        $result = $touristObj->registerTourist(
            $tourist["name_first"],
            $tourist["name_second"] ?: null,
            $tourist["name_middle"] ?: null,
            $tourist["name_last"],
            $tourist["name_suffix"] ?: null,
            $tourist["houseno"],
            $tourist["street"],
            $tourist["barangay"],
            $tourist["city"],
            $tourist["province"],
            $tourist["country"],
            $tourist["countrycode_ID"],
            $tourist["phone_number"],
            $tourist["emergency_name"],
            $tourist["emergency_countrycode_ID"],
            $tourist["emergency_phonenumber"],
            $tourist["emergency_relationship"],
            $tourist["contactinfo_email"],
            $tourist["person_nationality"],
            $tourist["person_gender"],
            $tourist["person_civilstatus"],
            $tourist["person_dateofbirth"],
            $tourist["username"],
            $tourist["password"]
        );

        if ($result === true) {
            $success = "Registration successful! You can now log in.";
        } elseif ($result === "email_exists") {
            $errors["contactinfo_email"] = "This email address is already registered.";
        } elseif ($result === "phone_exists") {
            $errors["phone_number"] = "This phone number is already registered.";
        } elseif ($result === "person_exists") {
            $errors["name_first"] = "A person with the same name and birthdate already exists.";
        } else {
            $errors["general"] = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tourist Registration - Tour Guide System</title>
</head>
<body>
    <h1>Tourist Registration</h1>

    <?php if ($success): ?>
        <p style="color: green;"><?= $success ?></p>
        <p><a href="index.php">Go to Login</a></p>
    <?php endif; ?>

    <?php if (!empty($errors["general"])): ?>
        <p style="color: red;"><?= $errors["general"] ?></p>
    <?php endif; ?>

    <form method="POST">
        <h3>Personal Information</h3>
        <label>First Name:</label><br>
        <input type="text" name="name_first" value="<?= $tourist["name_first"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["name_first"] ?? "" ?></span><br>

        <label>Second Name:</label><br>
        <input type="text" name="name_second" value="<?= $tourist["name_second"] ?? "" ?>"><br><br>

        <label>Middle Name:</label><br>
        <input type="text" name="name_middle" value="<?= $tourist["name_middle"] ?? "" ?>"><br><br>

        <label>Last Name:</label><br>
        <input type="text" name="name_last" value="<?= $tourist["name_last"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["name_last"] ?? "" ?></span><br>

        <label>Date of Birth:</label><br>
        <input type="date" name="person_dateofbirth" value="<?= $tourist["person_dateofbirth"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["person_dateofbirth"] ?? "" ?></span><br>

        <label>Gender:</label><br>
        <select name="person_gender">
            <option value="">Select Gender</option>
            <option value="Male" <?= ($tourist["person_gender"] ?? "") == "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= ($tourist["person_gender"] ?? "") == "Female" ? "selected" : "" ?>>Female</option>
        </select><br><br>

        <h3>Contact Information</h3>
        <label>Email:</label><br>
        <input type="email" name="contactinfo_email" value="<?= $tourist["contactinfo_email"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["contactinfo_email"] ?? "" ?></span><br>

        <label>Country Code:</label><br>
            <select name="countrycode_ID" id="countrycode_ID">
            <option value="">--SELECT COUNTRY CODE--</option>

            <?php foreach ($touristObj->fetchCountryCode() as $country_code){ 
                $temp = $country_code["countrycode_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($tourist["countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["countrycode_name"] ?> <?= $country_code["countrycode_number"]?> </option> 
               

        <?php } ?>
        </select>
        <p class="errors"> <?= $errors["countrycode_ID"] ?? "" ?> </p>
        <br><br>
        <label>Phone Number:</label><br>
        <input type="text" name="phone_number" value="<?= $tourist["phone_number"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["phone_number"] ?? "" ?></span><br>

        <h3>Login Credentials</h3>
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= $tourist["username"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["username"] ?? "" ?></span><br>

        <label>Password:</label><br>
        <input type="password" name="password" value="<?= $tourist["password"] ?? "" ?>"><br>
        <span style="color:red;"><?= $errors["password"] ?? "" ?></span><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
