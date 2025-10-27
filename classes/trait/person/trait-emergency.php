<?php

trait EmergencyTrait {

    public function addgetEmergencyID($countrycode_ID, $phone_number, $ename, $erelationship){

        try {

            $sql = "SELECT emergency_ID FROM Emergency_Info ei
                    INNER JOIN Phone_Number pn ON ei.phone_ID = pn.phone_ID
                    WHERE pn.countrycode_ID = :countrycode_ID AND pn.phone_number = :phone_number
                    AND ei.emergency_Name = :ename AND ei.emergency_Relationship = :erelationship";
            $query_select = $db->prepare($sql_select); 
            $query_select->bindParam(":countrycode_ID", $countrycode_ID);
            $query_select->bindParam(":phone_number", $phone_number);
            $query_select->bindParam(":ename", $ename);
            $query_select->bindParam(":erelationship", $erelationship);
            $query_select->execute();

            if($result = $query_select->fetch()){
                return $result["emergency_ID"];
            }
            
            $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number);

            if(!$phone_ID){
                $db->rollBack(); 
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
