<?php
require_once "../classes/contact-info.php";

$contactinfoOBj = new Contact_Info();

$contactinfo = [];
$errors = [];

    
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $contactinfo["address_houseno"] = trim(htmlspecialchars($_POST["address_houseno"] ?? ""));
    $contactinfo["address_street"]  = trim(htmlspecialchars($_POST["address_street"] ?? ""));
    $contactinfo["address_barangay"]  = trim(htmlspecialchars($_POST["address_barangay"] ?? ""));
    $contactinfo["address_city"]  = trim(htmlspecialchars($_POST["address_city"] ?? ""));
    $contactinfo["address_province"]  = trim(htmlspecialchars($_POST["address_province"] ?? ""));
    $contactinfo["address_country"]  = trim(htmlspecialchars($_POST["address_country"] ?? ""));
    
    $contactinfo["countrycode_ID"]  = trim(htmlspecialchars($_POST["countrycode_ID"] ?? ""));
    $contactinfo["phone_number"]  = trim(htmlspecialchars($_POST["phone_number"] ?? ""));
    
    $contactinfo["emergency_name"]  = trim(htmlspecialchars($_POST["emergency_name"] ?? ""));
    $contactinfo["emergency_relationship"]  = trim(htmlspecialchars($_POST["emergency_relationship"] ?? ""));
    
    $contactinfo["contactinfo_email"]  = trim(htmlspecialchars($_POST["contactinfo_email"] ?? ""));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contact Info</title>
</head>
<body>
    <h1>Add Contact form</h1>

    <form action="" method="post">
        <h3>Address</h3>
        <label for="address_houseno">House No</label>
        <input type="text" name="address_houseno" id="address_houseno" value="<?= $contactinfo["address_houseno"] ?? "" ?>">

        <label for="address_street">Street</label>
        <input type="text" name="address_street" id="address_street" value="<?= $contactinfo["address_street"] ?? "" ?>">

        <label for="address_barangay">Barangay</label>
        <input type="text" name="address_barangay" id="address_barangay" value="<?= $contactinfo["address_barangay"] ?? "" ?>">

        <label for="address_city">City</label>
        <input type="text" name="address_city" id="address_city" value="<?= $contactinfo["address_city"] ?? "" ?>">

        <label for="address_province">Province</label>
        <input type="text" name="address_province" id="address_province" value="<?= $contactinfo["address_province"] ?? "" ?>">

        <label for="address_country">Country</label>
        <input type="text" name="address_country" id="address_country" value="<?= $contactinfo["address_country"] ?? "" ?>">

        <br> <br>

        <h3>Phone Number</h3>
        <label for="countrycode_ID"> Country Code </label>
        <select name="countrycode_ID" id="countrycode_ID">
            <option value="">--SELECT COUNTRY CODE--</option>
        <?php foreach ($contactinfoObj->fetchCountryCode() as $country_code){ 
            $temp = $country_code["countrycode_ID"];
        ?>
            <option value="<?= $temp ?>" <?= ($temp == ($contactinfo["countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["countrycode_name"] ?> <?= $country_code["countrycode_number"]?> </option>    

        <?php } ?>
        <p class="errors"> <?= $errors["countrycode_ID"] ?? "" ?> </p>
        </select>
        <label for="phone_number">Phone Number</label>
        <input type="text" name="phone_number" id="phone_number" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $contactinfo["phone_number"] ?? "" ?>">
        
        <input type="submit" value="Save Phone Number">
    </form>

</body>
</html>