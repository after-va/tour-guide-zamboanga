<?php

require_once "database.php";
//     person_ID
//     role_ID
//     name_ID
//     person_Nationality
//     person_Gender
//     person_CivilStatus
//     person_DateOfBirth
//     rating_ID
//     contactinfo_ID
class Tourists extends Database{
    public $person_ID = "";
    public $person_nationality = "";
    public $person_gender = "";
    public $person_civilstatus = "";
    public $person_dateOfbirth = "";

    // Check if the Person registering is exist Already
    public function isTouristExist($name_first, $name_second, $name_middle, $name_last, $name_suffix, $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth){
        $sql = "SELECT COUNT(*) AS total FROM Person p INNER JOIN Name_Info n ON p.name_ID=n.name_ID WHERE n.name_first =:name_first AND (n.name_second = :name_second OR (n.name_second IS NULL AND :name_second IS NULL)) AND (n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL)) AND n.name_last = :name_last AND (n.name_suffix = :name_suffix OR (n.name_suffix IS NULL AND :name_suffix IS NULL)) AND p.person_Nationality = :person_nationality AND p.person_Gender = :person_gender AND p.person_CivilStatus = :person_civilstatus AND p.person_DateOfBirth = :person_dateofbirth";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first",$name_first);
        $query->bindParam(":name_second",$name_second);
        $query->bindParam(":name_middle",$name_middle);
        $query->bindParam(":name_last",$name_last);
        $query->bindParam(":name_suffix",$name_suffix);
        $query->bindParam(":person_nationality",$person_nationality);
        $query->bindParam(":person_gender",$person_gender);
        $query->bindParam(":person_civilstatus",$person_civilstatus);
        $query->bindParam(":person_dateofbirth",$person_dateofbirth);

        $record = null;
        if($query->execute()){
            $record = $query->fetch();
        }

        if($record["total"] > 0){
            return true;
        }else{
            return false;
        }
    }

    // Name Info
    public function addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix){
        $sql = "SELECT name_ID FROM name_info n WHERE n.name_first = :name_first AND n.name_last = :name_last AND (n.name_second = :name_second OR (n.name_second IS NULL AND :name_second IS NULL)) AND ( n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL) ) AND (n.name_suffix = :name_suffix OR (n.name_suffix IS NULL AND :name_suffix IS NULL));";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first",$name_first);
        $query->bindParam(":name_second",$name_second);
        $query->bindParam(":name_middle",$name_middle);
        $query->bindParam(":name_last",$name_last);
        $query->bindParam(":name_suffix",$name_suffix);

        $result = $query->fetch();

        if($result){
            return $result["name_ID"];
        }

        $db = $this->connect();
        $sql = "INSERT INTO name_info (name_first, name_second, name_middle, name_last, name_suffix ) VALUES (:name_first, :name_second, :name_middle, :name_last, :name_suffix)";
        $query->bindParam(":name_first",$name_first);
        $query->bindParam(":name_second",$name_second);
        $query->bindParam(":name_middle",$name_middle);
        $query->bindParam(":name_last",$name_last);
        $query->bindParam(":name_suffix",$name_suffix);
        if ($query->execute()) {
            return $db->lastInsertId(); 
        } else {
            return false;
        }

    }

    // Address 
    public function addgetAddress($houseno, $street, $barangay, $city, $province, $country){
        $sql = "SELECT address_ID, address_houseno, address_street, address_barangay, address_city, address_province, address_country FROM address_info WHERE address_houseno=:houseno AND address_street=:street AND address_barangay=:barangay AND address_city=:city AND address_province=:province AND address_country=:country ";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":houseno",$houseno);
        $query->bindParam(":street",$street);
        $query->bindParam(":barangay",$barangay);
        $query->bindParam(":city",$city);
        $query->bindParam(":province",$province);
        $query->bindParam(":country",$country);
        $query->execute();
        $result = $query->fetch();

        if($result){
            return $result["address_ID"];
        }
        $db = $this->connect();
        $sql = "INSERT INTO address_info (address_houseno, address_street, address_barangay, address_city, address_province, address_country) VALUES (:address_houseno, :address_street, :address_barangay, :address_city, :address_province, :address_country)";
        $query = $db->prepare($sql);
        $query->bindParam(":address_houseno", $houseno);
        $query->bindParam(":address_street", $street);
        $query->bindParam(":address_barangay", $barangay);
        $query->bindParam(":address_city", $city);
        $query->bindParam(":address_province", $province);
        $query->bindParam(":address_country", $country);

        if ($query->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }

    }
    
    
    //Phone_ID
    public function addgetPhoneNumber($countrycode_ID,$phone_number){
        $sql = "SELECT phone_ID FROM phone_number WHERE phone_number = :phone_number AND countrycode_ID = :countrycode_ID ";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":countrycode_ID", $countrycode_ID);
        $query->bindParam(":phone_number", $phone_number);
        $query->execute();
        $result = $query->fetch();

        if($result){
            return $result["phone_ID"];
        }
        $db = $this->connect();
        $sql = "INSERT INTO phone_number(countrycode_ID, phone_number) VALUES (:countrycode_ID, :phone_number)";
        $query = $db->prepare($sql);
        $query->bindParam(":countrycode_ID", $countrycode_ID);
        $query->bindParam(":phone_number", $phone_number);

        if ($query->execute()) {
            return $db->lastInsertId(); 
        } else {
            return false;
        }
    }

    // Emergency_ID
    public function addgetEmergencyID($countrycode_ID, $phone_number, $ename, $erelationship){

        $db = $this->connect(); 

        try {
             $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number);

            if(!$phone_ID){
                $db->rollBack(); 
                return false;
            }
            
            $sql = "INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship) VALUES (:phone_ID, :ename, :erelationship)";
            $query = $db->prepare($sql);
            $query->bindParam(":phone_ID", $phone_ID);
            $query->bindParam(":ename", $ename); 
            $query->bindParam(":erelationship", $erelationship); 

            if ($query->execute()){
                return $db->lastInsertId();
            } else {
                return false;
            }

        } catch (PDOException $e) {
            return false;
        }
    }

    // Contact Info
    public function addgetContact_Info($houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email){
        
        $db = $this->connect();

        try{
            
            $address_ID = $this->addgetAddress($houseno, $street, $barangay, $city, $province, $country);
            $phone_ID = $this->addgetPhoneNumber($countrycode_ID,$phone_number);
            $emergency_ID = $this->addgetEmergencyID($emergency_countrycode_ID, $emergency_phonenumber, $emergency_name, $emergency_relationship);
            
           if (!$address_ID || !$phone_ID || !$emergency_ID) {
                return false;
            }

            $sql = "INSERT INTO Contact_Info (address_ID,phone_ID,emergency_ID, contactinfo_email) VALUES (:address_ID, :phone_ID, :emergency_ID, :contactinfo_email)";
            $query = $db->prepare($sql);
            $query->bindParam(":address_ID", $address_ID);
            $query->bindParam(":phone_ID", $phone_ID);
            $query->bindParam(":emergency_ID", $emergency_ID);
            $query->bindParam(":contactinfo_email", $contactinfo_email);

            if ($query->execute()){
                return $db->lastInsertId();
            } else {
                return false;
            }


        }catch (PDOException $e) {
            return false;
        }
    }

    //     role_ID, name_ID, person_Nationality, person_Gender, person_CivilStatus, person_DateOfBirth, contactinfo_ID
   
    //     person_ID,
    //     rating_ID

    // Add Tourist
    public function addTourist($name_first, $name_second, $name_middle, $name_last, $name_suffix,$houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email,$person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth, $role_ID ){
        $db = $this->connect();
        $db->beginTransaction();
        try{
            $name_ID = $this->addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix);
            $contactinfo_ID =$this->addgetContact_Info($houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email);
            
            
           if (!$name_ID  || !$contactinfo_ID) {
                $db->rollBack();
                return false;
            }

            $sql = "INSERT INTO Person_Info(role_ID, name_ID, person_Nationality, person_Gender, person_CivilStatus, person_DateOfBirth, contactinfo_ID) VALUES (1, :name_ID, :person_nationality, :person_gender, :person_civilstatus, :person_dateofbirth, :contactinfo_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(":name_ID", $name_ID);
            $query->bindParam(":person_nationality", $person_nationality);
            $query->bindParam(":person_gender", $person_gender);
            $query->bindParam(":person_civilstatus", $person_civilstatus);
            $query->bindParam(":person_dateofbirth", $person_dateofbirth);
            $query->bindParam(":contactinfo_ID", $contactinfo_ID);

            if ($query->execute()){
                $db->commit();
                return true; 
            } else {
                $db->rollBack();
                return false;
            }


        }catch (PDOException $e) {
            $db->rollBack();
            return false;
        }

    }

    // fetch Country Code
    public function fetchCountryCode(){
        $sql = "SELECT * FROM country_code";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }



}