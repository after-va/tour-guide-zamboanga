<?php

require_once "../classes/phone-number.php";

$emergencyObj;

$emergency=[];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $emergency["emergency_name"] = trim(htmlspecialchars($_POST["emergency_name"] ?? ""));
    $emergency["emergency_relationship"] = trim(htmlspecialchars($_POST["emergency_relationship"] ?? ""));
    $emergency["phone_number"] = trim(htmlspecialchars($_POST["phone_number"] ?? ""));
    $emergency["countrycode_ID"] = trim(htmlspecialchars($_POST["countrycode_ID"] ?? ""));

     
    if(empty($phone["countrycode_ID"])){
        $errors["countrycode_ID"] = "Country Code is required.";
    }
    if(empty($phone["phone_number"])){
        $errors["phone_number"] = "Phone Number is required.";
    } elseif (strlen($phone["phone_number"]) < 10) {
        $errors["phone_number"] = "Phone Number must be exactly 10 digits long.";
    }
    
    if(empty($phone["emergency_Name"])){
        $errors["emergency_Name"] = "Emergency Contact Name is required.";
    }

    if(empty($phone["emergency_Relationship"])){
        $errors["emergency_Relationship"] = "Relationship is required.";
    }


    if(empty(array_filter($errors))){
        $result = $emergencyObj->isPhoneExist($phone["countrycode_ID"], $phone["phone_number"]);
        if(empty($result["phone_ID"])){
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
}

?>