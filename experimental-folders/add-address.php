<?php

require_once "../classes/address-info.php";

$addressObj = new Address_Info();

$address = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // $address["address_ID"] = trim(htmlspecialchars($_POST["address_ID"]));
    $address["address_houseno"] = trim(htmlspecialchars($_POST["address_houseno"]));
    $address["address_street"] = trim(htmlspecialchars($_POST["address_street"]));
    $address["address_barangay"] = trim(htmlspecialchars($_POST["address_barangay"]));
    $address["address_city"] = trim(htmlspecialchars($_POST["address_city"]));
    $address["address_province"] = trim(htmlspecialchars($_POST["address_province"]));
    $address["address_country"] = trim(htmlspecialchars($_POST["address_country"]));

    if (empty($address["address_houseno"] )) {
        $errors["address_houseno"] = "is required";
    }

    if (empty($address["address_street"] )) {
        $errors["c"]="is required";
    }
    if (empty($address["address_barangay"])) {
        $errors["address_barangay"] ="is required";
    }
    if (empty($address["address_city"] )) {
        $errors["address_city"] ="is required";
    }
    if (empty($address["address_province"] )) {
        $errors["address_province"] = "is required";
    }
    if (empty($address["address_country"] )) {
        $errors["address_country"] = "Country is required";
    }

    if(empty(array_filter($errors))){
        $addressObj->address_houseno =  $address["address_houseno"];
        $addressObj->address_street = $address["address_street"];
        $addressObj->address_barangay = $address["address_barangay"];
        $addressObj->address_city = $address["address_city"];
        $addressObj->address_province = $address["address_province"];
        $addressObj->address_country = $address["address_country"];
        

        if($addressObj->addAddress()){
            header("Location: add-address.php");
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
    <title>Add Address</title>
</head>
<body>
    <h1>Add Address</h1>    
    <form action="" method="post">

    <label for="address_houseno"> House No</label>
    <input type="text" name="address_houseno" id="address_houseno" value="<?= $address["address_houseno"] ?? "" ?>">

    <label for="address_street">Street</label>
    <input type="text" name="address_street" id="address_street" value="<?= $address["address_street"] ?? "" ?>">

    <label for="address_barangay">Barangay</label>
    <input type="text" name="address_barangay" id="address_barangay" value="<?= $address["address_barangay"] ?? "" ?>">

    <label for="address_city">City</label>
    <input type="text" name="address_city" id="address_city" value="<?= $address["address_city"] ?? "" ?>">

    <label for="address_province">Province</label>
    <input type="text" name="address_province" id="address_province" value="<?= $address["address_province"] ?? "" ?>">

    <label for="address_country">Country</label>
    <input type="text" name="address_country" id="address_country" value="<?= $address["address_country"] ?? "" ?>">

    <input type="submit" value="Save Address">

    </form>

</body>
</html>