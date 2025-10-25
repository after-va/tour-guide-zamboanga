<?php

trait PhoneNumberTrait {

    public function addgetPhoneNumber($countrycode_ID, $phone_number, $db){
        
        $sql_select = "SELECT phone_ID FROM phone_number WHERE phone_number = :phone_number AND countrycode_ID = :countrycode_ID";
        $query_select = $db->prepare($sql_select); 
        $query_select->bindParam(":countrycode_ID", $countrycode_ID);
        $query_select->bindParam(":phone_number", $phone_number);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["phone_ID"];
        }
        
        $sql_insert = "INSERT INTO phone_number(countrycode_ID, phone_number) VALUES (:countrycode_ID, :phone_number)";
        $query_insert = $db->prepare($sql_insert); 
        $query_insert->bindParam(":countrycode_ID", $countrycode_ID);
        $query_insert->bindParam(":phone_number", $phone_number);

        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function fetchCountryCode(){
        $sql = "SELECT * FROM country_code";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function deletePhoneNumber($pid){
        $sql = "DELETE FROM phone_number WHERE phone_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $pid);
        return $query->execute();
    }
}
