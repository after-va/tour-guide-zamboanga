<?php

trait PhoneTrait {

    // Check if phone number already exists
    public function checkPhoneExists($country_ID, $phone_number) {
        $sql = "SELECT COUNT(*) AS total FROM Phone_Number 
                WHERE country_ID = :country_ID AND phone_number = :phone_number";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":country_ID", $country_ID);
        $query->bindParam(":phone_number", $phone_number);
        
        if ($query->execute()) {
            $record = $query->fetch();
            return $record["total"] > 0;
        }
        return false;
    }
    

    public function addgetPhoneNumber($country_ID, $phone_number, $db){
        
        $sql_select = "SELECT phone_ID FROM phone_number WHERE phone_number = :phone_number AND country_ID = :country_ID";
        $query_select = $db->prepare($sql_select); 
        $query_select->bindParam(":country_ID", $country_ID);
        $query_select->bindParam(":phone_number", $phone_number);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["phone_ID"];
        }
        
        $sql_insert = "INSERT INTO phone_number(country_ID, phone_number) VALUES (:country_ID, :phone_number)";
        $query_insert = $db->prepare($sql_insert); 
        $query_insert->bindParam(":country_ID", $country_ID);
        $query_insert->bindParam(":phone_number", $phone_number);

        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function fetchCountryCode(){
        $sql = "SELECT * FROM country";
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

    public function deletePhoneIfUnused($phone_ID){
        $db = $this->connect();

        $sql_check = "SELECT COUNT(*) AS total FROM contact_info WHERE phone_ID = :id
                    UNION ALL 
                    SELECT COUNT(*) AS total FROM Emergency_Info WHERE phone_ID = :id";
        $query_check = $db->prepare($sql_check);
        $query_check->bindParam(":id", $phone_ID);
        $query_check->execute();
        $counts = $query_check->fetchAll(PDO::FETCH_COLUMN);

        // If total references == 0 in both tables
        if(array_sum($counts) == 0){
            $sql_delete = "DELETE FROM phone_number WHERE phone_ID = :id";
            $query_delete = $db->prepare($sql_delete);
            $query_delete->bindParam(":id", $phone_ID);
            $query_delete->execute();
        }
    }


}
