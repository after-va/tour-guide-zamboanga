<?php

trait EmergencyTrait {

    public function addgetEmergencyID($countrycode_ID, $phone_number, $ename, $erelationship, $db){

        try {
            $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number, $db);

            if(!$phone_ID){
                return false;
            }
            
            $sql = "INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship)
                    VALUES (:phone_ID, :ename, :erelationship)";
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

    public function deleteEmergencyIfUnused($emergency_ID){
        $db = $this->connect();

        $sql_check = "SELECT COUNT(*) AS total FROM contact_info WHERE emergency_ID = :id";
        $query_check = $db->prepare($sql_check);
        $query_check->bindParam(":id", $emergency_ID);
        $query_check->execute();
        $count = $query_check->fetch(PDO::FETCH_ASSOC)['total'];

        if($count == 0){
            $sql_delete = "DELETE FROM Emergency_Info WHERE emergency_ID = :id";
            $query_delete = $db->prepare($sql_delete);
            $query_delete->bindParam(":id", $emergency_ID);
            $query_delete->execute();
        }
    }


}
