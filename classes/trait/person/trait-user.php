<?php

trait UserTrait {
    
    public function checkUsernameExists($user_username, $db) {
        $sql = "SELECT COUNT(*) AS total FROM User_Login WHERE user_username = :user_username";
        $query = $db->prepare($sql);
        $query->bindParam(":user_username", $user_username);
        
        if ($query->execute()) {
            $record = $query->fetch();
            return $record["total"] > 0;
        }
        return false;
    }
    
    public function addUser($name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay,
        $country_ID, $phone_number,
        $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
        $contactinfo_email,
        $person_nationality, $person_gender, $person_dateofbirth, 
        $user_username, $user_password,
        $db
    ) {


        try {
            $person_ID = $this->addgetPerson($name_first, $name_second, $name_middle, $name_last, $name_suffix, 
            $houseno, $street, $barangay,
            $country_ID, $phone_number,
            $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
            $contactinfo_email,
            $person_nationality, $person_gender, $person_dateofbirth, $db);

            if (!$person_ID) {
                return false;
            }

            $sql = "INSERT INTO User_Login (person_ID, user_username, user_password) 
                    VALUES (:person_ID, :user_username, :user_password)";
            $query = $db->prepare($sql);
            $query->bindParam(":person_ID", $person_ID);
            $query->bindParam(":user_username", $user_username);
            $query->bindParam(":user_password", $user_password);

            if ($query->execute()) {
                return $db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            if (method_exists($this, 'setLastError')) {
                $this->setLastError("User creation error: " . $e->getMessage());
            }
            error_log("User creation error: " . $e->getMessage());
            return false;
        }
    }

}