<?php

require_once "../classes/country-code.php";

$countrycodeObj = new Country_Code();

$countrycode = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $countrycode["countrycode_name"] = trim(htmlspecialchars($_POST["countrycode_name"]));
    $countrycode["countrycode_number"] = trim(htmlspecialchars($_POST["countrycode_number"]));

    if(empty($countrycode["countrycode_name"])){
        $errors["countrycode_name"] = "Country Name is required";
    } else if($$countrycodeObj->isCountryNameExist($countrycode["countrycode_name"])){
        $errors["countrycode_name"] = "Country name is already exist";
    }
    
    if(empty($countrycode["countrycode_number"])){
        $errors["countrycode_number"] = "Country Number is required";
    }

    if(empty(array_filter($errors))){
        $countrycodeObj->countrycode_name = $countrycode["countrycode_name"];
        $countrycodeObj->countrycode_number = $countrycode["countrycode_number"];

        if($countrycodeObj->addCountryCode()){
            header ("Location: add-country-code.php");

        } else {
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
    <title>Add Country Code</title>
</head>
<body>
    <h1>Add Country Code</h1>
    <form action="" method = "POST"> 
    
    <label for="">Name </label>
    <input type="text" name="countrycode_name" id="countrycode_name" value="<?= $countrycode["countrycode_name"] ?? ""?>">
    <p class="errors"> <?= $errors["countrycode_name"] ?? "" ?> </p>

    <label for="">Number </label>
    <input type="text" name="countrycode_number" id="countrycode_number" value="<?= $countrycode["countrycode_number"] ?? ""?>">
    <p class="errors"> <?= $errors["countrycode_number"] ?? "" ?> </p>

    <input type="submit" value="Save Country Code">
    </form>
    
</body>
</html>