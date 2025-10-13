<?php

require_once "database.php";

class Phone_Number extends Database {
    public $phone_ID = "";
    public $countrycode_ID = "";
    public $phone_number = "";

    public function addPhoneNumber(){
        $sql = "INSERT INTO phone_number(countrycode_ID, phone_number) VALUES (:countrycode_ID, :phone_number)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":countrycode_ID", $this->countrycode_ID);
        $query->bindParam(":phone_number", $this->phone_number);

        return $query->execute();
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
