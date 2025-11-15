<?php

trait EmergencyTrait {

    public function addgetEmergencyID($country_ID, $phone_number, $ename, $erelationship, $db){

        try {

            $sql = "SELECT emergency_ID FROM Emergency_Info ei
                    INNER JOIN Phone_Number pn ON ei.phone_ID = pn.phone_ID
                    WHERE pn.country_ID = :country_ID AND pn.phone_number = :phone_number
                    AND ei.emergency_Name = :ename AND ei.emergency_Relationship = :erelationship";
            $query_select = $db->prepare($sql); 
            $query_select->bindParam(":country_ID", $country_ID);
            $query_select->bindParam(":phone_number", $phone_number);
            $query_select->bindParam(":ename", $ename);
            $query_select->bindParam(":erelationship", $erelationship);
            $query_select->execute();

            if($result = $query_select->fetch()){
                return $result["emergency_ID"];
            }

            $phone_ID = $this->addgetPhoneNumber($country_ID, $phone_number, $db);

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

    public function updateEmergencyID($emergency_ID, $country_ID, $phone_number, $ename, $erelationship, $db){
        try {
            $sql_count = "SELECT COUNT(DISTINCT emergency_ID) AS emergencyID_count
                FROM Contact_Info WHERE emergency_ID = :emergency_ID ";

            $q_count = $db->prepare($sql_count);
            $q_count->execute([':name_ID' => $name_ID]);
            $emergencyID_count = (int) $q_count->fetchColumn();

            if ($emergencyID_count > 1) {
                echo "Emergency ID {$emergency_ID} is shared by {$emergencyID_count} contact. Creating new name_ID for this person.\n";
                $emergency_ID = $this->addgetEmergencyID($country_ID, $phone_number, $ename, $erelationship, $db);

            } else {
                echo "Reusing existing Emergency ID: {$emergency_ID} (Linked to {$emergencyID_count} person).\n";
                
                $phone_ID = $this->updatePhoneNumber($phone_ID, $country_ID, $phone_number, $db);

                $sql_insert = "UPDATE emergency_info SET
                    emergency_Name = :emergency_Name,
                    emergency_Relationship = :emergency_Relationship,
                    phone_ID = :phone_ID,
                    WHERE emergency_ID = :emergency_ID";
                $q_insert = $db->prepare($sql_insert);
                $q_insert->bindParam(":firstname", $name_first);
                $q_insert->bindParam(":secondname", $name_second);
                $q_insert->bindParam(":middlename", $name_middle);
                $q_insert->bindParam(":lastname", $name_last);
                $q_insert->bindParam(":suffix", $name_suffix);
                $existing = $q_check->fetch(PDO::FETCH_ASSOC);
                if ($q_insert->execute()) {
                    return $db->lastInsertId();
                } else {
                    return false;
                }
                
            }


        } catch (PDOException $e) {
            return false;
        }
    }

}
