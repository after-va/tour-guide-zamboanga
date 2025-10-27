<?php
require_once "database.php";
require_once "trait/person/trait-name-info.php";
require_once "trait/person/trait-address.php";
require_once "trait/person/trait-phone.php";
require_once "trait/person/trait-emergency.php";
require_once "trait/person/trait-contact-info.php";
require_once "trait/person/trait-person.php";
require_once "trait/person/trait-user.php";


class Tourist extends Database {
    use PersonTrait, UserTrait, NameInfoTrait, AddressTrait, PhoneTrait, EmergencyTrait, ContactInfoTrait;
    
    private $lastError = "";

    public function addTourist($name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay,
        $country_ID, $phone_number,
        $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
        $contactinfo_email,
        $person_nationality, $person_gender, $person_dateofbirth, 
        $username, $password) {
    
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            error_log("Database connection failed in addTourist");
            return false;
        }

        $db->beginTransaction();

        try {
            error_log("Calling addUser from addTourist");
            $login_ID = $this->addUser(
                $name_first, 
                $name_second, 
                $name_middle, 
                $name_last, 
                $name_suffix,
                $houseno, 
                $street, 
                $barangay,
                $country_ID, 
                $phone_number,
                $emergency_name, 
                $emergency_country_ID, 
                $emergency_phonenumber, 
                $emergency_relationship,
                $contactinfo_email,
                $person_nationality, 
                $person_gender, 
                $person_dateofbirth, 
                $username, 
                $password,
                $db
            );

            error_log("addUser returned login_ID: " . ($login_ID ?: 'false'));

            if (!$login_ID) {
                $error = $this->getLastError() ?: "Failed to create user account";
                error_log("addUser failed: " . $error);
                $db->rollBack();
                $this->setLastError($error);
                return false;
            }

            $role_ID = 3; // Tourist role_ID is 3
            $created_at = date('Y-m-d H:i:s');

            $sql = "INSERT INTO Account_Role (login_ID, role_ID, created_at) VALUES (:login_ID, :role_ID, :created_at)";
            $query = $db->prepare($sql);
            $query->bindParam(":login_ID", $login_ID, PDO::PARAM_INT);
            $query->bindParam(":role_ID", $role_ID, PDO::PARAM_INT);
            $query->bindParam(":created_at", $created_at);

            $result = $query->execute();
            
            if ($result) {
                $db->commit();
                error_log("Tourist registration successful for user: " . $username);
                return true; 
            } else {
                $errorInfo = $query->errorInfo();
                $error = "Database error: " . ($errorInfo[2] ?? 'Unknown error');
                error_log("Failed to add role: " . $error);
                $db->rollBack();
                $this->setLastError($error);
                return false;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $this->setLastError($e->getMessage());
            error_log("Tourist Registration Error: " . $e->getMessage()); 
            return false;
        }
    }
   

    public function fetchCountries(){
        $sql = "SELECT country_ID, country_name, country_codenumber FROM Country ORDER BY country_name";
        $q = $this->connect()->prepare($sql);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLastError() {
        return $this->lastError;
    }
    
    public function setLastError($error) {
        $this->lastError = $error;
        return $this;
    }
}
