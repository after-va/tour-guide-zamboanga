<?php

// $name_first, $name_second, $name_middle, $name_last, $name_suffix,$houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email,$person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth,
require_once "../assets/classes/tourist.php";

$touristObj = new Tourist();

$tourist = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Name
    // $tourist[""] = trim(htmlspecialchars($_POST[""]));
    $tourist["name_first"] = trim(htmlspecialchars($_POST["name_first"]));
    $tourist["name_second"] = trim(htmlspecialchars($_POST["name_second"]));
    $tourist["name_middle"] = trim(htmlspecialchars($_POST["name_middle"]));
    $tourist["name_last"] = trim(htmlspecialchars($_POST["name_last"]));
    $tourist["name_suffix"] = trim(htmlspecialchars($_POST["name_suffix"]));
    

    // Address
    $tourist["address_houseno"] = trim(htmlspecialchars($_POST["address_houseno"]));
    $tourist["address_street"] = trim(htmlspecialchars($_POST["address_street"]));
    $tourist["address_barangay"] = trim(htmlspecialchars($_POST["address_barangay"]));
    $tourist["address_city"] = trim(htmlspecialchars($_POST["address_city"]));
    $tourist["address_province"] = trim(htmlspecialchars($_POST["address_province"]));
    $tourist["address_country"] = trim(htmlspecialchars($_POST["address_country"]));

    //Phone
    $tourist["countrycode_ID"] = trim(htmlspecialchars($_POST["countrycode_ID"]));
    $tourist["phone_number"] = trim(htmlspecialchars($_POST["phone_number"]));
    
    //Emergency Contact
    $tourist["emergency_name"] = trim(htmlspecialchars($_POST["emergency_name"]));
    $tourist["emergency_countrycode_ID"] = trim(htmlspecialchars($_POST["emergency_countrycode_ID"]));
    $tourist["emergency_phonenumber"] = trim(htmlspecialchars($_POST["emergency_phonenumber"]));
    $tourist["emergency_relationship"] = trim(htmlspecialchars($_POST["emergency_relationship"]));
    
    // Email
    $tourist["contactinfo_email"] = trim(htmlspecialchars($_POST["contactinfo_email"]));
    
    // Tourist Data Information
    $tourist["person_nationality"] = trim(htmlspecialchars($_POST["person_nationality"]));
    $tourist["person_gender"] = trim(htmlspecialchars($_POST["person_gender"]));
    $tourist["person_civilstatus"] = trim(htmlspecialchars($_POST["person_civilstatus"]));
    $tourist["person_dateofbirth"] = trim(htmlspecialchars($_POST["person_dateofbirth"]));

    // Name
    if(empty($tourist["name_first"] )){
        $errors["name_first"] = "First Name is required first";
    }
   
    if(empty($tourist["name_last"] )){
        $errors["name_last"] = "Last Name is required first";
    }

    // Address
    if (empty($tourist["address_houseno"] )) {
        $errors["address_houseno"] = "is required";
    }

    if (empty($tourist["address_street"] )) {
        $errors["address_street"]="is required";
    }
    if (empty($tourist["address_barangay"])) {
        $errors["address_barangay"] ="is required";
    }
    if (empty($tourist["address_city"] )) {
        $errors["address_city"] ="is required";
    }
    if (empty($tourist["address_province"] )) {
        $errors["address_province"] = "is required";
    }
    if (empty($tourist["address_country"] )) {
        $errors["address_country"] = "Country is required";
    }

    // Phone
    if(empty($tourist["countrycode_ID"])){
        $errors["countrycode_ID"] = "Country Code is Required";
    }

    if(empty($tourist["phone_number"])){
        $errors["phone_number"] = "Phone Number is needed.";
    } else if (strlen($tourist["phone_number"]) < 10 ){
        $errors["phone_number"] = "Phone Number must be 10 digits long.";
    }

    // Emergency Contact
    if(empty($tourist["emergency_countrycode_ID"])){
        $errors["emergency_countrycode_ID"] = "Country Code is required.";
    }
    if(empty($tourist["emergency_phonenumber"])){
        $errors["emergency_phonenumber"] = "Phone Number is required.";
    } elseif (strlen($tourist["emergency_phonenumber"]) < 10) {
        $errors["emergency_phonenumber"] = "Phone Number must be exactly 10 digits long.";
    }

    if(empty($tourist["emergency_name"])){
        $errors["emergency_name"] = "Emergency Contact Name is required.";
    }

    if(empty($tourist["emergency_relationship"])){
        $errors["emergency_relationship"] = "Relationship is required.";
    }

    // Email
    if(empty($tourist["contactinfo_email"])){
        $errors["contactinfo_email"] = "Email address is required.";
    }

    // Other Tourist Info
    if(empty($tourist["person_nationality"])){
        $errors["person_nationality"] = "Nationality is required.";
    }
    if(empty($tourist["person_gender"])){
        $errors["person_gender"] = "Gender is required.";
    }
    if(empty($tourist["person_civilstatus"])){
        $errors["person_civilstatus"] = "Civil Status is required.";
    }
    if(empty($tourist["person_dateofbirth"])){
        $errors["person_dateofbirth"] = "Date of Birth is required.";
    }


    if(empty(array_filter($errors))){
        $results = $touristObj->addTourist($tourist["name_first"], $tourist["name_second"], $tourist["name_middle"], $tourist["name_last"], $tourist["name_suffix"], $tourist["address_houseno"], $tourist["address_street"], $tourist["address_barangay"], $tourist["address_city"], $tourist["address_province"],  $tourist["address_country"], $tourist["countrycode_ID"], $tourist["phone_number"], $tourist["emergency_name"], $tourist["emergency_countrycode_ID"],  $tourist["emergency_phonenumber"],$tourist["emergency_relationship"], $tourist["contactinfo_email"], $tourist["person_nationality"], $tourist["person_gender"], $tourist["person_civilstatus"], $tourist["person_dateofbirth"]);

        if($results){
            header("Location: tourist-registration.php");
            exit;
        } else {
            $errors["general"] = "Failed to save data. Please check for duplicate phone number entries.";
        }
    }

    // $tourist["name_first"] = trim(htmlspecialchars($_POST["name_first"]));
    // $tourist["name_second"] = trim(htmlspecialchars($_POST["name_second"]));
    // $tourist["name_middle"] = trim(htmlspecialchars($_POST["name_middle"]));
    // $tourist["name_last"] = trim(htmlspecialchars($_POST["name_last"]));
    // $tourist["name_suffix"] = trim(htmlspecialchars($_POST["name_suffix"]));
    
        // Address
    // $tourist["address_houseno"] = trim(htmlspecialchars($_POST["address_houseno"]));
    // $tourist["address_street"] = trim(htmlspecialchars($_POST["address_street"]));
    // $tourist["address_barangay"] = trim(htmlspecialchars($_POST["address_barangay"]));
    // $tourist["address_city"] = trim(htmlspecialchars($_POST["address_city"]));
    // $tourist["address_province"] = trim(htmlspecialchars($_POST["address_province"]));
    // $tourist["address_country"] = trim(htmlspecialchars($_POST["address_country"]));

    // $tourist["person_nationality"]
    // $tourist["person_gender"]
    // $tourist["person_civilstatus"]
    // tourist["person_dateofbirth"]

    // $tourist["contactinfo_email"]
    // $tourist["emergency_relationship"]
    // $tourist["emergency_phonenumber"]
    // $tourist["emergency_countrycode_ID"]
    // $tourist["emergency_name"]
    // $tourist["phone_number"]
    // $tourist["countrycode_ID"]
    // $tourist["address_country"]
    // $tourist["address_province"]
    // $tourist["address_city"]
    // $tourist["address_barangay"]
    // $tourist["address_street"]
    // $tourist["address_houseno"]
    // $tourist["name_suffix"]
    // $tourist["name_last"]
    // $tourist["name_middle"]
    // $tourist["name_second"]
    // $tourist["name_first"]






}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>
    <form action="" method="post">
        <h3>Name </h3>
        <label for="name_first">First Name</label>
        <input type="text" name="name_first" id="name_first" value= "<?= $tourist["name_first"] ?? '' ?>">

        <label for="name_second"> Second Name </label>
        <input type="text" name="name_second" id="name_second" value= "<?=$tourist["name_second"] ?? "" ?>">

        <label for="name_middle">Middle Name</label>
        <input type="text" name="name_middle" id="name_middle" value= "<?=$tourist["name_middle"] ?? '' ?>">

        <label for="name_last">Last Name</label>
        <input type="text" name="name_last" id="name_last" value = "<?= $tourist["name_last"] ?? '' ?>">

        <label for="name_suffix">Suffix</label>
        <input type="text" name="name_suffix" id="name_suffix" value = "<?= $tourist["name_suffix"] ?? '' ?>">

        <br><br>
        <h3>Address</h3>
        <label for="address_houseno">House No</label>
        <input type="text" name="address_houseno" id="address_houseno" value="<?= $tourist["address_houseno"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["address_houseno"] ?? "" ?> </p>

        <label for="address_street">Street</label>
        <input type="text" name="address_street" id="address_street" value="<?= $tourist["address_street"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["address_street"] ?? "" ?> </p>

        <label for="address_barangay">Barangay</label>
        <input type="text" name="address_barangay" id="address_barangay" value="<?= $tourist["address_barangay"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["address_barangay"] ?? "" ?> </p>

        <label for="address_city">City</label>
        <input type="text" name="address_city" id="address_city" value="<?= $tourist["address_city"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["address_city"] ?? "" ?> </p>

        <label for="address_province">Province</label>
        <input type="text" name="address_province" id="address_province" value="<?= $tourist["address_province"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["address_province"] ?? "" ?> </p>

        <label for="address_country">Country</label>
        <input type="text" name="address_country" id="address_country" value="<?= $tourist["address_country"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["address_country"] ?? "" ?> </p>

        <br> <br>

        <h3>Phone Number</h3>
        <label for="countrycode_ID"> Country Code </label>
        <select name="countrycode_ID" id="countrycode_ID">
            <option value="">--SELECT COUNTRY CODE--</option>

            <?php foreach ($touristObj->fetchCountryCode() as $country_code){ 
                $temp = $country_code["countrycode_ID"];
            ?>
            <option value="<?= $temp ?>" <?= ($temp == ($tourist["countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["countrycode_name"] ?> <?= $country_code["countrycode_number"]?> </option> 
               

        <?php } ?>
        <p class="errors"> <?= $errors["countrycode_ID"] ?? "" ?> </p>
        </select>
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

        <label for="emergency_phonenumber">Phone Number</label>
        <input type="text" name="emergency_phonenumber" id="emergency_phonenumber" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $tourist["emergency_phonenumber"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["emergency_phonenumber"] ?? "" ?> </p>

        <label for="contactinfo_email"> Email Address</label>
        <input type="email" name="contactinfo_email" id="contactinfo_email" value ="<?= $tourist["contactinfo_email"] ?? "" ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["contactinfo_email"] ?? "" ?> </p>
        
        <label for="person_dateofbirth"> Date Of Birth</label>
        <input type="date" name="person_dateofbirth" id="person_dateofbirth" value = "<?= $tourist["person_dateofbirth"] ?? '' ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["person_dateofbirth"] ?? "" ?> </p>
        
        <label for="person_nationality">Nationality</label>
        <input type="text" name="person_nationality" id="" value="<?= $tourist["person_nationality"] ?? '' ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["person_nationality"] ?? "" ?> </p>

        <label for="person_gender">Gender</label>
        <input type="text" name="person_gender" id="" value="<?= $tourist["person_gender"] ?? '' ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["person_gender"] ?? "" ?> </p>
        
        <label for="person_civilstatus"> Civil Status </label>
        <input type="text" name="person_civilstatus" id="" value="<?= $tourist["person_civilstatus"] ?? '' ?>">
        <p style="color: red; font-weight: bold;"> <?= $errors["person_civilstatus"] ?? "" ?> </p>
 
        <input type="submit" value="Register">
    </form>
    
</body>
</html>