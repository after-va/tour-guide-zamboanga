<?php

require "database.php";

class Country_Code extends Database{
    public $countrycode_ID = "";
    public $countrycode_name = "";
    public $countrycode_number = "";

    public function addCountryCode(){
        $sql = " INSERT INTO country_code(countrycode_name, countrycode_number) VALUE ( :countrycode_name, :countrycode_number) ";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":countrycode_name", $this->countrycode_name);
        $query->bindParam(":countrycode_number", $this->countrycode_number);

        return $query->execute();
    }

    public function viewCountryCode($search){
        if(!empty($search)){
            $sql = "SELECT * FROM country_code WHERE name LIKE CONCAT(:search, '%') ORDER BY countrycode_name ASC";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":search", $search);

        } else {
            $sql = "SELECT * FROM country_code";
            $query = $this->connect()->prepare($sql);

        }

        if($query->execute()) {
            return $query->fetchAll();
        } else {
        return null;
        }

    }

    public function isCountryNameExist($cname){
        $sql = "SELECT COUNT(*) as total FROM country_code WHERE countrycode_name = :countrycode_name";
        $query= $this->connect()->prepare($sql);
        $query->bindParam(":countrycode_name", $cname);
        $record = null;
        
        if($query->execute()){
            $record = quercy->fetch();
        }

        if($record["total"] > 0){
            return true;
        } else {
            return false;
        }

    }



    public function deleteCountryCode($cid){
        $sql = "DELETE FROM country_code WHERE countrycode_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $cid);

        return $query->execute();
    }


}
