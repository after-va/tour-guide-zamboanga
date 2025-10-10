<?php

require_once "database.php";

class Contact_Info extends Database{
    public $contactinfo_ID = "";
    public $address_ID = "";
    public $phone_ID = "";
    public $emergency_ID = "";
    public $contactinfo_email = "";

    // Address 
    public function addgetAddress($houseno, $street, $barangay, $city, $province, $country){
        $sql = "SELECT address_houseno, address_street, address_barangay, address_city, address_province, address_country FROM address_info WHERE address_houseno=:houseno, address_street=:street, address_barangay=:barangay, address_city=:city, address_province=:province, address_country=:country ";
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

        $sql = "INSERT INTO address_info (address_houseno, address_street, address_barangay, address_city, address_province, address_country) VALUES (:address_houseno, :address_street, :address_barangay, :address_city, :address_province, :address_country)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":houseno",$houseno);
        $query->bindParam(":street",$street);
        $query->bindParam(":barangay",$barangay);
        $query->bindParam(":city",$city);
        $query->bindParam(":province",$province);
        $query->bindParam(":country",$country);

        if ($query->execute()) {
            return $this->connect()->lastInsertId();
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

        $sql = "INSERT INTO phone_number(countrycode_ID, phone_number) VALUES (:countrycode_ID, :phone_number)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":countrycode_ID", $countrycode_ID);
        $query->bindParam(":phone_number", $phone_number);

        if ($query->execute()) {
            return $this->connect()->lastInsertId();
        } else {
            return false;
        }
    }

    // Emergency_ID
    public function addgetEmergencyID($countrycode_ID, $phone_number, $ename, $erelationship){
        $query = $this->connect();
        $db->beginTransaction();
        try {
            $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number);
            
            if(!$phone_ID){
                $db->rollback();
                return false;
            }

            $sql = "INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship) VALUES (:phone_ID, :ename, :erelationship)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":phone_ID", $phone_ID);
            $query->bindParam(":ename,", $ename);
            $query->bindParam(":erelationship,", $erelationship);

            if ($query->execute()){            
                return $this->connect()->lastInsertId();
            } else {
                $db->rollBack();
                return false;
            }

        } catch (PDOException $e) {
            $db->rollBack();
            return false;
        }

    }

    // Contact Info
    public function addContact_Info($houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $ename, $erelationship, $contactinfo_email){
        $query = $this->connect();
        $db->beginTransaction();
        
        try{
            $address_ID = $this->addgetAddress($houseno, $street, $barangay, $city, $province, $country);
            $phone_ID = $this->addgetPhoneNumber($countrycode_ID,$phone_number);
            $emergency_ID = $this->addgetEmergencyID($countrycode_ID, $phone_number, $ename, $erelationship);

            $sql = "INSERT INTO Contact_Info (address_ID,phone_ID,emergency_ID, contactinfo_email) VALUES (:address_ID, :phone_ID, :emergency_ID, :contactinfo_email)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":address_ID", $address_ID);
            $query->bindParam(":phone_ID",$phone_ID);
            $query->bindParam(":emergency_ID",$emergency_ID);
            $query->bindParam(":contactinfo_email",$contactinfo_email);
//            $query->bindParam(":",$);

        }catch (PDOException $e) {
            $db->rollBack();
            return false;
        }


    }


}