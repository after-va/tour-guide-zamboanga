<?php

require_once "database.php";

class Emergency_Info extends Database{
    public $emergency_id = "";
    public $phone_ID = "";
    public $emergency_Name = "";
    public $emergency_Relationship = "";

   
    public function fetchCountryCode(){
        $sql = "SELECT * FROM country_code";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function addgetPhoneNumber($countrycode_ID,$phone_number ){
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

    public function addEmergencyInfo($countrycode_ID, $phone_number, $emergency_Name, $emergency_Relationship){
        $db = $this->connect(); // Renamed $query to $db for clarity
        $db->beginTransaction(); // Use $db

        try {
            $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number);

            if(!$phone_ID){
                $db->rollBack();
                return false;
            }

            // Corrected the placeholder :erelationship to :emergency_Relationship
            $sql = "INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship) VALUES (:phone_ID, :emergency_Name, :emergency_Relationship)";
            $query = $db->prepare($sql); // Use $db to prepare the statement
            $query->bindParam(":phone_ID", $phone_ID);
            $query->bindParam(":emergency_Name", $emergency_Name);
            $query->bindParam(":emergency_Relationship", $emergency_Relationship);

            if ($query->execute()){
                $db->commit();
                return true;
            } else {
                $db->rollBack();
                return false;
            }

        } catch (PDOException $e) {
            $db->rollBack();
            // Optionally log $e->getMessage() for debugging
            return false;
        }
    }


}