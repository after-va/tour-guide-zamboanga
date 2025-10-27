<?php

trait ContactInfoTrait {

    // Check Email If Exists
    public function checkEmailExists($email) {
        $sql = "SELECT COUNT(*) AS total FROM Contact_Info WHERE contactinfo_email = :email";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":email", $email);
        
        if ($query->execute()) {
            $record = $query->fetch();
            return $record["total"] > 0;
        }
        return false;
    }

    public function addContact_Info( $houseno, $street, $barangay, $city,  
        $countrycode_ID, $phone_number, 
        $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, 
        $contactinfo_email, $db)
        {

        try{
            // These functions come from other traits:
            $address_ID = $this->addgetAddress($houseno, $street, $barangay, $db);
            $phone_ID = $phone_ID = $this->addgetPhoneNumber($countrycode_ID, $phone_number, $db);
            $emergency_ID = $this->addgetEmergencyID($emergency_countrycode_ID, $emergency_phonenumber, $emergency_name, $emergency_relationship, $db);

            
           if (!$address_ID || !$phone_ID || !$emergency_ID) {
                $db->rollBack();
                return false;
            }

            $sql = "INSERT INTO Contact_Info (address_ID, phone_ID, emergency_ID, contactinfo_email) 
                    VALUES (:address_ID, :phone_ID, :emergency_ID, :contactinfo_email)";
            $query = $db->prepare($sql);
            $query->bindParam(":address_ID", $address_ID);
            $query->bindParam(":phone_ID", $phone_ID);
            $query->bindParam(":emergency_ID", $emergency_ID);
            $query->bindParam(":contactinfo_email", $contactinfo_email);

            if ($query->execute()){
                $db->commit();
                return true; 
            } else {
                $db->rollBack();
                return false;
            }

        } catch (PDOException $e) {
            $db->rollBack();
            return false;
        }
    }


    public function deleteContactInfo($contact_ID){
        $sql = "DELETE FROM contact_info WHERE contact_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $contact_ID);

        return $query->execute();
    }

    public function deleteContactInfoSafe($contactinfo_ID){
        $db = $this->connect();

        // Step 1: Get linked phone, address, emergency IDs
        $sql = "SELECT address_ID, phone_ID, emergency_ID FROM contact_info WHERE contactinfo_ID = :id";
        $query = $db->prepare($sql);
        $query->bindParam(":id", $contactinfo_ID);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if(!$data){ return false; }

        $address_ID = $data['address_ID'];
        $phone_ID = $data['phone_ID'];
        $emergency_ID = $data['emergency_ID'];

        // Step 2: Delete Contact_Info Row
        $sql_delete = "DELETE FROM contact_info WHERE contactinfo_ID = :id";
        $query_delete = $db->prepare($sql_delete);
        $query_delete->bindParam(":id", $contactinfo_ID);
        $query_delete->execute();

        // Step 3: Individually remove unused pieces
        $this->deleteAddressIfUnused($address_ID);
        $this->deletePhoneIfUnused($phone_ID);
        $this->deleteEmergencyIfUnused($emergency_ID);

        return true;
    }


}
