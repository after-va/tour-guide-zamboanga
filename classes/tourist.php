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

    public function addTourist($name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay,
        $country_ID, $phone_number,
        $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
        $contactinfo_email,
        $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth, 
        $username, $password) {
    
        $db = $this->connect();
        $db->beginTransaction();

        try {

            $login_ID = $this->addUser($name_first, $name_second, $name_middle, $name_last, $name_suffix,
                        $houseno, $street, $barangay,
                        $country_ID, $phone_number,
                        $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
                        $contactinfo_email,
                        $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth, 
                        $username, $password,
                        $db);

            if (!$login_ID) {
                $db->rollBack();
                return false;
            }

            $role_ID = 1; //  Tourist role_ID is 1
            $created_at = date('Y-m-d H:i:s');

            $sql = "INSERT INTO Account_Role (login_ID, role_ID, created_at) VALUES (:login_ID, :role_ID, created_at)";
            $query = $db->prepare($sql);
            $query->bindParam(":login_ID", $login_ID);
            $query->bindParam(":role_ID", $role_ID);
            $query->bindParam(":created_at", $created_at);

            if ($query->execute()){
                $db->commit();
                return true; 
            } else {
                $db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $db->rollBack();
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
}
