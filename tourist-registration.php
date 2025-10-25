<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "php/Tourist.php";

$touristObj = new Tourist();
$countryCodes = $touristObj->fetchCountryCode();
$tourist = [];
$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tourist["name_first"] = trim(htmlspecialchars($_POST["name_first"]));
    $tourist["name_second"] = trim(htmlspecialchars($_POST["name_second"]));
    $tourist["name_middle"] = trim(htmlspecialchars($_POST["name_middle"]));
    $tourist["name_last"] = trim(htmlspecialchars($_POST["name_last"]));
    $tourist["name_suffix"] = trim(htmlspecialchars($_POST["name_suffix"]));
    $tourist["houseno"] = trim(htmlspecialchars($_POST["houseno"]));
    $tourist["street"] = trim(htmlspecialchars($_POST["street"]));
    $tourist["barangay"] = trim(htmlspecialchars($_POST["barangay"]));
    $tourist["city"] = trim(htmlspecialchars($_POST["city"]));
    $tourist["province"] = trim(htmlspecialchars($_POST["province"]));
    $tourist["country"] = trim(htmlspecialchars($_POST["country"]));
    $tourist["countrycode_ID"] = trim(htmlspecialchars($_POST["countrycode_ID"]));
    $tourist["phone_number"] = trim(htmlspecialchars($_POST["phone_number"]));
    $tourist["emergency_name"] = trim(htmlspecialchars($_POST["emergency_name"]));
    $tourist["emergency_countrycode_ID"] = trim(htmlspecialchars($_POST["emergency_countrycode_ID"]));
    $tourist["emergency_phonenumber"] = trim(htmlspecialchars($_POST["emergency_phonenumber"]));
    $tourist["emergency_relationship"] = trim(htmlspecialchars($_POST["emergency_relationship"]));
    $tourist["contactinfo_email"] = trim(htmlspecialchars($_POST["contactinfo_email"]));
    $tourist["person_nationality"] = trim(htmlspecialchars($_POST["person_nationality"]));
    $tourist["person_gender"] = trim(htmlspecialchars($_POST["person_gender"]));
    $tourist["person_civilstatus"] = trim(htmlspecialchars($_POST["person_civilstatus"]));
    $tourist["person_dateofbirth"] = trim(htmlspecialchars($_POST["person_dateofbirth"]));
    $tourist["username"] = trim(htmlspecialchars($_POST["username"]));
    $tourist["password"] = trim(htmlspecialchars($_POST["password"]));

    if(empty($tourist["name_first"])){
        $error = "First name is required.";
    }

    if(empty($tourist["name_last"])){
        $error = "Last name is required.";
    }

    if(empty($tourist["person_dateofbirth"])){
        $error = "Date of birth is required.";
    }

    if(empty($tourist["username"])){
        $error = "Username is required.";
    }

    if(empty($tourist["password"])){
        $error = "Password is required.";
    }


 

    if(empty($error)){
        $result = $touristObj->registerTourist(
            $tourist["name_first"],
            !empty($tourist["name_second"]) ? $tourist["name_second"] : null,
            !empty($tourist["name_middle"]) ? $tourist["name_middle"] : null,
            $tourist["name_last"],
            !empty($tourist["name_suffix"]) ? $tourist["name_suffix"] : null,
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
        
        if ($result == true) {
            $success = "Registration successful! You can now login.";
        } elseif ($result == "email_exists") {
            $error = "This email address is already registered. Please use a different email or login to your existing account.";
        } elseif ($result == "phone_exists") {
            $error = "This phone number is already registered. Please use a different phone number or login to your existing account.";
        } elseif ($result == "person_exists") {
            $error = "An account with the same name, birthdate, and gender already exists. If this is you, please login to your existing account.";
        } else {
            $error = "Registration failed. Please try again.";
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
    
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
        <p><a href="index.php">Go to Login</a></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <h3>Personal Information</h3>
        <label>First Name:</label><br>
        <input type="text" name="name_first" value = "<?= $tourist["name_first"] ?? "" ?>"><br><br>
        <span><?= $error["name_first"] ?? "" ?></span>
        
        <label>Second Name:</label><br>
        <input type="text" name="name_second" value = "<?= $tourist["name_second"] ?? "" ?>"><br><br>
        <span><?= $error["name_second"] ?? "" ?></span>
        
        <label>Middle Name:</label><br>
        <input type="text" name="name_middle" value = "<?= $tourist["name_middle"] ?? "" ?>"><br><br>
        <span><?= $error["name_middle"] ?? "" ?></span>
        
        <label>Last Name:</label><br>
        <input type="text" name="name_last" value = "<?= $tourist["name_last"] ?? "" ?>"><br><br>
        <span><?= $error["name_last"] ?? "" ?></span>
        
        <label>Suffix:</label><br>
        <input type="text" name="name_suffix" value = "<?= $tourist["name_suffix"] ?? "" ?>"><br><br>
        <span><?= $error["name_suffix"] ?? "" ?></span>
        
        <label>Date of Birth:</label><br>
        <input type="date" name="person_dateofbirth" value = "<?= $tourist["person_dateofbirth"] ?? "" ?>"><br><br>
        <span><?= $error["person_dateofbirth"] ?? "" ?></span>
        
        <label>Gender:</label><br>
        <select name="person_gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?= isset($tourist["person_gender"]) && $tourist["person_gender"] == "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= isset($tourist["person_gender"]) && $tourist["person_gender"] == "Female" ? "selected" : "" ?>>Female</option>
            <option value="Other" <?= isset($tourist["person_gender"]) && $tourist["person_gender"] == "Other" ? "selected" : "" ?>>Other</option>
        </select><br><br>
        
        <label>Civil Status:</label><br>
        <select name="person_civilstatus" required>
            <option value="">Select Status</option>
            <option value="Single" <?= isset($tourist["person_civilstatus"]) && $tourist["person_civilstatus"] == "Single" ? "selected" : "" ?>>Single</option>
            <option value="Married" <?= isset($tourist["person_civilstatus"]) && $tourist["person_civilstatus"] == "Married" ? "selected" : "" ?>>Married</option>
            <option value="Widowed" <?= isset($tourist["person_civilstatus"]) && $tourist["person_civilstatus"] == "Widowed" ? "selected" : "" ?>>Widowed</option>
            <option value="Divorced" <?= isset($tourist["person_civilstatus"]) && $tourist["person_civilstatus"] == "Divorced" ? "selected" : "" ?>>Divorced</option>
        </select><br><br>
        
        <label>Nationality:</label><br>
        <input type="text" name="person_nationality" value = "<?= $tourist["person_nationality"] ?? "" ?>" required><br><br>
        <span><?= $error["person_nationality"] ?? "" ?></span>
        
        <h3>Contact Information</h3>
        <label>Email:</label><br>
        <input type="email" name="contactinfo_email" value = "<?= $tourist["contactinfo_email"] ?? "" ?>" required><br><br>
        <span><?= $error["contactinfo_email"] ?? "" ?></span>
        
        <h3>Phone Number</h3>
        <label for="countrycode_ID"> Country Code </label>
        <select name="countrycode_ID" id="countrycode_ID">
            <option value="">--SELECT COUNTRY CODE--</option>

            <?php foreach ($countryCodes as $country_code){ 
                $temp = $country_code["countrycode_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($tourist["countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["countrycode_name"] ?> <?= $country_code["countrycode_number"]?> </option> 
               

        <?php } ?>
        </select>
        <p class="errors"> <?= $errors["countrycode_ID"] ?? "" ?> </p>
        <br><br>
        
        <label>Phone Number:</label><br>
        <input type="text" name="phone_number" value = "<?= $tourist["phone_number"] ?? "" ?>" required><br><br>
        
        <h3>Address</h3>
        <label>House No:</label><br>
        <input type="text" name="houseno" value = "<?= $tourist["houseno"] ?? "" ?>" required><br><br>
        
        <label>Street:</label><br>
        <input type="text" name="street" value = "<?= $tourist["street"] ?? "" ?>" required><br><br>
        
        <label>Barangay:</label><br>
        <input type="text" name="barangay" value = "<?= $tourist["barangay"] ?? "" ?>" required><br><br>
        
        <label>City:</label><br>
        <input type="text" name="city" value = "<?= $tourist["city"] ?? "" ?>" required><br><br>
        
        <label>Province:</label><br>
        <input type="text" name="province" value = "<?= $tourist["province"] ?? "" ?>" required><br><br>
        
        <label>Country:</label><br>
        <input type="text" name="country" value = "<?= $tourist["country"] ?? "" ?>" required><br><br>
        
        <h3>Emergency Contact</h3>
        <label>Emergency Contact Name:</label><br>
        <input type="text" name="emergency_name" value = "<?= $tourist["emergency_name"] ?? "" ?>" required><br><br>
        
        <label for="emergency_countrycode_ID"> Country Code </label>
        <select name="emergency_countrycode_ID" id="emergency_countrycode_ID">
            <option value="">--SELECT COUNTRY CODE--</option>
            <?php foreach ($touristObj->fetchCountryCode() as $country_code){ 
                $temp = $country_code["countrycode_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($tourist["emergency_countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["countrycode_name"] ?> <?= $country_code["countrycode_number"]?> </option>    

        <?php } ?>
        <p class="errors"> <?= $errors["emergency_countrycode_ID"] ?? "" ?> </p>
        </select>
        
        <label>Emergency Phone Number:</label><br>
        <input type="text" name="emergency_phonenumber" value = "<?= $tourist["emergency_phonenumber"] ?? "" ?>" required><br><br>
        
        <label>Relationship:</label><br>
        <input type="text" name="emergency_relationship" value = "<?= $tourist["emergency_relationship"] ?? "" ?>" required><br><br>
        
        <h3>Login Credentials</h3>
        <label>Username:</label><br>
        <input type="text" name="username" value = "<?= $tourist["username"] ?? "" ?>" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" value = "<?= $tourist["password"] ?? "" ?>" required><br><br>
        
        <button type="submit">Register</button>
    </form>
    
    <p>Already have an account? <a href="index.php">Login here</a></p>
</body>
</html>
