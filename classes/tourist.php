<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/account/account-login.php";
require_once "trait/person/address.php";
require_once "trait/person/contact-info.php";
require_once "trait/person/emergency.php";
require_once "trait/person/name-info.php";
require_once "trait/person/person.php";
require_once "trait/person/phone.php";

class Tourist extends Database {
    use AccountLoginTrait, AddressTrait, ContactInfoTrait, NameInfoTrait, PersonTrait, PhoneTrait;

    public function addgetTourist($name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay, $country_ID, $phone_number, $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email, $person_nationality, $person_gender, $person_dateofbirth, $user_username, $user_password){
        
            $db = $this->connect();
            if (!$db) {
                $this->setLastError("Database connection failed");
                error_log("Database connection failed in addTourist");
                return false;
            }

            $db->beginTransaction();

            try{
                $person_ID = $this->addgetPerson($name_first, $name_second, $name_middle, $name_last, $name_suffix, $houseno, $street, $barangay, $country_ID, $phone_number, $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email, $person_nationality, $person_gender, $person_dateofbirth, $db);
                $user_ID = $this->addgetUserLogin($person_ID, $user_username, $user_password, $db);
                $role_ID = 3;

                if (!$person_ID || !$user_ID) {
                    $db->rollBack();
                    return false;
                }

                $accountlogin_ID = $this->addgetAccountLogin($user_ID, $role_ID, $db);

                if (!$accountlogin_ID) {
                    $db->rollBack();
                    return false;
                } else {
                    $db->commit();
                    return true;
                }

            } catch (PDOException $e) {
            $db->rollBack();
            error_log("Tourist Registration Error: " . $e->getMessage()); 
            return false;
        }


    }
}
