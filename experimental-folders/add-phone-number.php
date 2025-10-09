<?php

require_once "../classes/phone-number.php";

$phonenumberObj = new Phone_Number();

$phone = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $phone["phone_number"] = trim(htmlspecialchars($_POST["phone_number"]));
    $phone["countrycode_ID"] = trim(htmlspecialchars($_POST["countrycode_ID"]));
    if(empty($phone["countrycode_ID"])){
        $errors["countrycode_ID"] = "Country Code is Required";
    }

    if(empty($phone["phone_number"])){
        $errors["phone_number"] = "Phone Number is needed.";
    } else if (strlen($phone["phone_number"]) < 10 ){
        $errors["phone_number"] = "Phone Number must be 10 digits long.";
    }

    if(empty(array_filter($errors))){
        $phonenumberObj->countrycode_ID = $phone["countrycode_ID"];
        $phonenumberObj->phone_number = $phone["phone_number"];
        
        if($phonenumberObj->addPhoneNumber()){
            header("Location:add-phone-number.php");
        }
        else{
            echo "failed";
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Phone Number</title>
</head>
<body>
    <h1>Add Phone Number</h1>
    <form action="" method="post">
    <label for="countrycode_ID"> Country Code </label>
    <select name="countrycode_ID" id="countrycode_ID">
        <option value="">--SELECT COUNTRY CODE--</option>
    <?php foreach ($phonenumberObj->fetchCountryCode() as $country_code){ 
        $temp = $country_code["countrycode_ID"];
    ?>
        <option value="<?= $temp ?>" <?= ($temp == ($phone["countrycode_ID"] ?? "")) ? "selected" : "" ?>> <?= $country_code["countrycode_name"] ?> <?= $country_code["countrycode_number"]?> </option>    

    <?php } ?>
     <p class="errors"> <?= $errors["countrycode_ID"] ?? "" ?> </p>
    </select>
    <input type="text" name="phone_number" id="phone_number" maxlength="10" inputmode="numeric" pattern="[0-9]*" value = "<?= $phone["phone_number"] ?? "" ?>">
    <input type="submit" value="Save Phone Number">
    </form>
    
</body>
</html>