<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tour Guide') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended'){
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending'){
    header('Location: account-pending.php');
}
require_once "../../classes/guide.php";

$guideObj = new Guide();

$guide_ID = $guideObj->getGuide_ID($_SESSION['user']['account_ID']);
// user_ID" => $user['user_ID'],
//                         "user_username" => $username,
//                         "role_name" => $user['role_name'],
//                         "role_ID" => $user['role_ID'],
//                         "account_status" => $user['account_status'],
//                         "account_ID" => $user['account_ID']

if (isset($_SESSION['user']) || $_SESSION['user']['role_name'] == 'Tour Guide'){
    $result = $guideObj->changeAccountToTourist($_SESSION['user']['user_ID']);
    $account_ID = $result['account_ID'];

    if ($result){
        $_SESSION["account_ID"] = $account_ID ; 
        $_SESSION["role_ID"] = 3;
        $_SESSION['user']['role_name'] = 'Tourist';
        header('Location: ../tourist/dashboard.php');

    
    }



}

?>