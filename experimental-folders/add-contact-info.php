<?php
require_once "../classes/contact-info.php";

$contactinfoOBj = new Contact_Info();

$contactinfo = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $contactinfo["houseno"] = trim(htmlspecialchars($_POST["houseno"] ?? ""));
    $contactinfo["street"]  = trim(htmlspecialchars($_POST["street"] ?? ""));
    $contactinfo["barangay"]  = trim(htmlspecialchars($_POST["barangay"] ?? ""));
    $contactinfo["city"]  = trim(htmlspecialchars($_POST["city"] ?? ""));
    $contactinfo["province"]  = trim(htmlspecialchars($_POST["province"] ?? ""));
    $contactinfo["country"]  = trim(htmlspecialchars($_POST["country"] ?? ""));
    $contactinfo["countrycode_ID"]  = trim(htmlspecialchars($_POST["countrycode_ID"] ?? ""));
    $contactinfo["phone_number"]  = trim(htmlspecialchars($_POST["phone_number"] ?? ""));
    $contactinfo["name"]  = trim(htmlspecialchars($_POST["name"] ?? ""));
    $contactinfo["relationship"]  = trim(htmlspecialchars($_POST["relationship"] ?? ""));
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
        

    </form>

</body>
</html>