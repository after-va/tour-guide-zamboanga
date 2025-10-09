<?php

require_once "database.php";

class Emergency_Info extends Database{
    public $emergency_id = "";
    public $phone_id = "";
    public $emergency_name = "";
    public $emergency_relationship = "";

   
    public function fetchCountryCode(){
        $sql = "SELECT * FROM country_code";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function isPhoneExist($countrycode_id, $phone_number){
        $sql = "SELECT p.phone_ID AS phone_ID FROM phone_number p JOIN country_code c ON p.countrycode_ID=c.countrycode_ID WHERE p.phone_number = :phone_number AND c.countrycode_id = :countrycode_id ";
        $query = $this->connect()->prepare($sql);

        $result = $query->fetch();
        
        if($result){
            return $result["phone_ID"];
        }
    }




}