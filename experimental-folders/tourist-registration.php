<?php

// $name_first, $name_second, $name_middle, $name_last, $name_suffix,$houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email,$person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth,
require_once "../classes/tourist.php";

$touristObj = new Tourist();

$tourist = [];
$errors = [];

if($_SERVER["REQUEST_METHOD" == "POST"]){
    // Name
    // $tourist[""] = trim(htmlspecialchars($_POST[""]));
    $tourist["name_first"] = trim(htmlspecialchars($_POST["name_first"]));
    $tourist["name_second"] = trim(htmlspecialchars($_POST["name_second"]));
    $tourist["name_middle"] = trim(htmlspecialchars($_POST["name_middle"]));
    $tourist["name_last"] = trim(htmlspecialchars($_POST["name_last"]));
    $tourist["name_suffix"] = trim(htmlspecialchars($_POST["name_suffix"]));

    // Address
    $tourist["address_houseno"] = trim(htmlspecialchars($_POST["houseno"]));
    $tourist["address_street"] = trim(htmlspecialchars($_POST["street"]));
    $tourist["address_barangay"] = trim(htmlspecialchars($_POST["barangay"]));
    $tourist["address_city"] = trim(htmlspecialchars($_POST["city"]));
    $tourist["address_province"] = trim(htmlspecialchars($_POST["province"]));
    $tourist["address_country"] = trim(htmlspecialchars($_POST["country"]));

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

    if (empty($tourists["address_street"] )) {
        $errors["c"]="is required";
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
    if(empty($contactinfo["contactinfo_email"])){
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
    
</body>
</html>