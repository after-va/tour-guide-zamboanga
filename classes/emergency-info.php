<?php

require_once "database.php";
require_once "phone-number.php";

class EmergencyInfo extends PhoneNumber{

    // Emergency_ID
    public function addgetEmergencyID($countrycode_ID, $phone_number, $ename, $erelationship, $db){

        try {
             $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number, $db);

            if(!$phone_ID){
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

    public function deleteEmergencyInfo($emergency_ID){
        $sql = "DELETE FROM Emergency_Info WHERE emergency_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $emergency_ID);

        return $query->execute();
    }


}