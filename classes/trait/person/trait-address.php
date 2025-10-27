<?php

trait AddressTrait {

    public function addgetAddress($houseno, $street, $barangay_ID, $db){
    
        $sql_select = "SELECT address_ID 
                       FROM address_info 
                       WHERE address_houseno = :houseno 
                       AND address_street = :street 
                       AND barangay_ID = :barangay_ID";

        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":houseno", $houseno);
        $query_select->bindParam(":street", $street);
        $query_select->bindParam(":barangay_ID", $barangay_ID);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["address_ID"];
        }

        $sql_insert = "INSERT INTO address_info (address_houseno, address_street, barangay_ID) 
                       VALUES (:houseno, :street, :barangay_ID)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":houseno", $houseno);
        $query_insert->bindParam(":street", $street);
        $query_insert->bindParam(":barangay_ID", $barangay_ID);

        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function fetchCountry(){
        $sql = "SELECT * FROM country";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    

    public function deleteAddress($address_ID){
        $sql = "DELETE FROM address_info WHERE address_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $address_ID);

        return $query->execute();
    }

    public function deleteAddressIfUnused($address_ID){
        $db = $this->connect();

        $sql_check = "SELECT COUNT(*) AS total FROM contact_info WHERE address_ID = :id";
        $query_check = $db->prepare($sql_check);
        $query_check->bindParam(":id", $address_ID);
        $query_check->execute();
        $count = $query_check->fetch(PDO::FETCH_ASSOC)['total'];

        if($count == 0){
            $sql_delete = "DELETE FROM address_info WHERE address_ID = :id";
            $query_delete = $db->prepare($sql_delete);
            $query_delete->bindParam(":id", $address_ID);
            $query_delete->execute();
        }
    }



}
