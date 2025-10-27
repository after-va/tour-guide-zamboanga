<?php

trait UserTrait {
    
    public function checkUsernameExists($username, $db) {
        $sql = "SELECT COUNT(*) AS total FROM User_Login WHERE username = :username";
        $query = $db->prepare($sql);
        $query->bindParam(":username", $username);
        
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
        $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth, 
        $username, $password,
        $db
    ) {


        try {
            $person_ID = $this->addgetPerson($name_first, $name_second, $name_middle, $name_last, $name_suffix, 
            $houseno, $street, $barangay,
            $country_ID, $phone_number,
            $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
            $contactinfo_email,
            $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth, $db);

            if (!$person_ID) {
                return false;
            }

            $sql = "INSERT INTO User_Login (person_ID, username, password_hash) 
                    VALUES (:person_ID, :username, :password_hash)";
            $query = $db->prepare($sql);
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $query->bindParam(":person_ID", $person_ID);
            $query->bindParam(":username", $username);
            $query->bindParam(":password_hash", $password_hash);

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