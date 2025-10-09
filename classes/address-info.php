<?php

require_once "database.php";

class Address_Info extends Database{

    public $address_ID = "";
    public $address_houseno = "";
    public $address_street = "";
    public $address_barangay = "";
    public $address_city = "";
    public $address_province = "";
    public $address_country = "";


    public function addAddress(){
        $sql = "INSERT INTO address_info (address_houseno, address_street, address_barangay, address_city, address_province, address_country) VALUES (:address_houseno, :address_street, :address_barangay, :address_city, :address_province, :address_country)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":address_houseno", $this->address_houseno);
        $query->bindParam(":address_street", $this->address_street);
        $query->bindParam(":address_barangay", $this->address_barangay);
        $query->bindParam(":address_city", $this->address_city);
        $query->bindParam(":address_province", $this->address_province);
        $query->bindParam(":address_country", $this->address_country);


        retun $query->execute();
    }

    public function getAddress($houseno, $street, $barangay, $city, $province, $country){
        $sql = "SELECT address_houseno, address_street, address_barangay, address_city, address_province, address_country FROM address_info WHERE address_houseno=:houseno, address_street=:street, address_barangay=:barangay, address_city=:city, address_province=:province, address_country=:country ";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":houseno",$houseno);
        $query->bindParam(":street",$street);
        $query->bindParam(":barangay",$barangay);
        $query->bindParam(":city",$city);
        $query->bindParam(":province",$province);
        $query->bindParam(":country",$country);

        $result = null;

        if($query->execute()){
            $result =$query->fetch();
        }

        if($result){
            return $result["address_ID"];
        }


    }




}