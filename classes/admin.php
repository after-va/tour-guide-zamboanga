<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/account/account-login.php";
require_once "trait/booking/booking_bundle.php";
require_once "trait/booking/companion.php";
require_once "trait/tour/tour-packages.php";
require_once "trait/tour/tour-spots.php";
require_once "trait/tour/tour-packagespots.php";
require_once "trait/tour/schedule.php";
require_once "trait/tour/pricing.php";
require_once "trait/tour/people.php";
require_once "trait/payment-info/method.php";
require_once "trait/payment-info/transaction-reference.php";
require_once "trait/payment-info/payment-info.php";
require_once "trait/payment-info/payment-transaction.php";
require_once "trait/payment-info/refund.php";
require_once "trait/person/trait-phone.php";
require_once "trait/person/trait-name-info.php";
require_once "trait/person/trait-address.php";
require_once "trait/person/trait-emergency.php";
require_once "trait/person/trait-contact-info.php";
require_once "trait/person/trait-person.php";
require_once "trait/person/trait-account.php";

class Admin extends Database {
    use AccountLoginTrait; 
    use BookingBundleTrait, CompanionTrait;
    use AccountLoginTrait;
    use TourPackagesTrait, PeopleTrait, PricingTrait, ScheduleTrait;
    use TourSpotsTrait;
    use TourPackageSpot;
    use MethodTrait, TransactionReferenceTrait, PaymentInfo, PaymentTransaction, PhoneTrait, Refund;
    use PersonTrait, NameInfoTrait, AddressTrait, EmergencyTrait, ContactInfoTrait, Account_InfoTrait;

    public function getAllUsersDetails(){
        $sql = "SELECT * FROM user_login u JOIN account_info ai ON ai.user_ID = u.user_ID WHERE ai.role_ID != 1";
        $db = $this->connect();
        $query = $db->prepare($sql); 
        
        if($query->execute()){
            return $query->fetchAll();
        }
    }

    public function getAllRoles(){
        $sql = "SELECT * FROM role";
        $db = $this->connect();
        $query = $db->prepare($sql); 
        
        if($query->execute()){
            return $query->fetchAll();
        }

    }

    public function getUsersDetailsByID($user_ID){
        $sql = "SELECT u.user_ID, u.user_username, u.user_password,
        a.account_status, p.person_ID AS person_ID,
            GROUP_CONCAT(DISTINCT r.role_name 
                        ORDER BY r.role_name SEPARATOR ', ') AS role_name,
            GROUP_CONCAT(DISTINCT a.role_ID ORDER BY a.role_ID) AS role_ID,
            GROUP_CONCAT(DISTINCT a.account_ID ORDER BY a.account_ID) AS account_ID,
            ni.name_first, ni.name_last
            FROM User_Login      AS u
            LEFT JOIN Account_Info AS a ON a.user_ID = u.user_ID
            LEFT JOIN Role         AS r ON a.role_ID = r.role_ID
			JOIN person p ON u.person_ID = p.person_ID
			JOIN name_info ni ON p.name_ID = ni.name_ID
            WHERE u.user_ID = :user_ID
            GROUP BY u.user_ID, u.user_username";
        $db = $this->connect();
        $query = $db->prepare($sql); 
        $query->bindParam(':user_ID', $user_ID);
        
        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC);
        }
    }




}