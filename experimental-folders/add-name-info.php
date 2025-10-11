<?php

require_once "../classes/name-info.php";

$nameinfoObj = new Name_Info ();

$name = [];
$errors = [];


    // name_first 
    // name_second 
    // name_middle
    // name_last
    // name_suffix 
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name["name_first"] = trim(htmlspecialchars($_POST["name_first"]));
    $name["name_second"] = trim(htmlspecialchars($_POST["name_second"]));
    $name["name_middle"] = trim(htmlspecialchars($_POST["name_middle"]));
    $name["name_last"] = trim(htmlspecialchars($_POST["name_last"]));
    $name["name_suffix"] = trim(htmlspecialchars($_POST["name_suffix"]));

    if(empty($name["name_first"] )){
        $errors["name_first"] = "First Name is required first";
    }
   
    if(empty($name["name_last"] )){
        $errors["name_last"] = "Last Name is required first";
    }
    
    if(empty(array_filter($errors))){
        $nameinfoObj->name_first = $name["name_first"];
        $nameinfoObj->name_second = $name["name_second"];
        $nameinfoObj->name_middle = $name["name_middle"];
        $nameinfoObj->name_last = $name["name_last"];
        $nameinfoObj->name_suffix =$name["name_suffix"];

        if($nameinfoObj->addNameInfo()){
            header("Location: add-name-info.php");
            exit;
        } else {
            echo failed;
        }

    }



}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Name</title>
</head>
<body>
    <h1>Add Name Info</h1>
    <form action="" method="post">
        <label for="name_first">First Name</label>
        <input type="text" name="name_first" id="name_first" value= "<?= $name["name_first"] ?? '' ?>">

        <label for="name_second"> Second Name </label>
        <input type="text" name="name_second" id="name_second" value= "<?=$name["name_second"] ?? "" ?>">

        <label for="name_middle">Middle Name</label>
        <input type="text" name="name_middle" id="name_middle" value= "<?=$name["name_middle"] ?? '' ?>">

        <label for="name_last">Last Name</label>
        <input type="text" name="name_last" id="name_last" value = "<?= $name["name_last"] ?? '' ?>">

        <label for="name_suffix">Suffix</label>
        <input type="text" name="name_suffix" id="name_suffix" value = "<?= $name["name_suffix"] ?? '' ?>">

        <input type="submit" value="Save Name">
    </form>
</body>
</html>