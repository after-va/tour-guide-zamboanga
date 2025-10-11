<?php

require_once "../classes/emergency-info.php";

$emergencyObj = new Emergency_Info();

$emergency=[];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $emergency["emergency_name"] = trim(htmlspecialchars($_POST["emergency_name"] ?? ""));
    $emergency["emergency_relationship"] = trim(htmlspecialchars($_POST["emergency_relationship"] ?? ""));
    $emergency["phone_number"] = trim(htmlspecialchars($_POST["phone_number"] ?? ""));
    $emergency["countrycode_ID"] = trim(htmlspecialchars($_POST["countrycode_ID"] ?? ""));


     
     if(empty($emergency["countrycode_ID"])){
        $errors["countrycode_ID"] = "Country Code is required.";
    }
    if(empty($emergency["phone_number"])){
        $errors["phone_number"] = "Phone Number is required.";
    } elseif (strlen($emergency["phone_number"]) < 10) {
        $errors["phone_number"] = "Phone Number must be exactly 10 digits long.";
    }

    // Corrected array key from "emergency_Name" to "emergency_name"
    if(empty($emergency["emergency_name"])){
        $errors["emergency_name"] = "Emergency Contact Name is required.";
    }

    // Corrected array key from "emergency_Relationship" to "emergency_relationship"
    if(empty($emergency["emergency_relationship"])){
        $errors["emergency_relationship"] = "Relationship is required.";
    }


    if(empty(array_filter($errors))){
        // Corrected array keys passed to the method
        $result = $emergencyObj->addEmergencyInfo($emergency["countrycode_ID"], $emergency["phone_number"], $emergency["emergency_name"], $emergency["emergency_relationship"] );

        // Corrected variable from $success to $result
        if($result){
            header("Location: add-emergency-info.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Emergency Info</title>
</head>
<body>
    <h1> Add Emergency Info</h1>
    <form action="" method="post">

    <label for="emergency_name"> Emergency Name </label>
        <input type="text" name="emergency_name" id="emergency_name" value ="<?= $emergency["emergency_name"] ?? "" ?>" >

    <label for="emergency_relationship"> Emergency Relationship </label>
        <input type="text" name="emergency_relationship" id="" value = "<?= $emergency["emergency_relationship"] ?? "" ?>">

    
    <label for="countrycode_ID"> Country Code </label>
        <select name="countrycode_ID" id="countrycode_ID">
            <option value="">--SELECT COUNTRY CODE--</option>
        <?php foreach ($emergencyObj->fetchCountryCode() as $e){ 
            $temp = $e["countrycode_ID"];
        ?>
            <option value="<?= $temp ?>" <?= ($temp == ($emergency["countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $e["countrycode_name"] ?> <?= $e["countrycode_number"] ?> </option>    

        <?php } ?>
        </select>
        <p class="errors"> <?= $errors["countrycode_ID"] ?? "" ?> </p>
    <input type="text" name="phone_number" id="phone_number" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $emergency["phone_number"] ?? "" ?>">
    <input type="submit" value="Save Phone Number">
    </form>


</body>
</html>