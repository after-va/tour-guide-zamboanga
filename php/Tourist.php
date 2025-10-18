<?php

require_once "Database.php";

class Tourist extends Database {
    
    // Check if tourist exists
    public function isTouristExist($name_first, $name_second, $name_middle, $name_last, $name_suffix, 
                                    $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth) {
        $sql = "SELECT COUNT(*) AS total FROM Person p 
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID 
                WHERE n.name_first = :name_first 
                AND (n.name_second = :name_second OR (n.name_second IS NULL AND :name_second IS NULL)) 
                AND (n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL)) 
                AND n.name_last = :name_last 
                AND (n.name_suffix = :name_suffix OR (n.name_suffix IS NULL AND :name_suffix IS NULL)) 
                AND p.person_Nationality = :person_nationality 
                AND p.person_Gender = :person_gender 
                AND p.person_CivilStatus = :person_civilstatus 
                AND p.person_DateOfBirth = :person_dateofbirth";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first", $name_first);
        $query->bindParam(":name_second", $name_second);
        $query->bindParam(":name_middle", $name_middle);
        $query->bindParam(":name_last", $name_last);
        $query->bindParam(":name_suffix", $name_suffix);
        $query->bindParam(":person_nationality", $person_nationality);
        $query->bindParam(":person_gender", $person_gender);
        $query->bindParam(":person_civilstatus", $person_civilstatus);
        $query->bindParam(":person_dateofbirth", $person_dateofbirth);
        
        if ($query->execute()) {
            $record = $query->fetch();
            return $record["total"] > 0;
        }
        return false;
    }
    
    // Register a new tourist
    public function registerTourist($name_first, $name_second, $name_middle, $name_last, $name_suffix,
                                     $houseno, $street, $barangay, $city, $province, $country,
                                     $countrycode_ID, $phone_number,
                                     $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship,
                                     $contactinfo_email, $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth,
                                     $username, $password) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Create name info
            $name_ID = $this->addNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix, $db);
            
            // Create contact info
            $contactinfo_ID = $this->addContactInfo($houseno, $street, $barangay, $city, $province, $country,
                                                     $countrycode_ID, $phone_number,
                                                     $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship,
                                                     $contactinfo_email, $db);
            
            // Role ID for tourist is 3
            $role_ID = 3;
            
            if (!$name_ID || !$contactinfo_ID) {
                $db->rollBack();
                return false;
            }
            
            // Insert person
            $sql = "INSERT INTO Person(role_ID, name_ID, person_Nationality, person_Gender, person_CivilStatus, person_DateOfBirth, contactinfo_ID) 
                    VALUES (:role_ID, :name_ID, :person_nationality, :person_gender, :person_civilstatus, :person_dateofbirth, :contactinfo_ID)";
            
            $query = $db->prepare($sql);
            $query->bindParam(":role_ID", $role_ID);
            $query->bindParam(":name_ID", $name_ID);
            $query->bindParam(":person_nationality", $person_nationality);
            $query->bindParam(":person_gender", $person_gender);
            $query->bindParam(":person_civilstatus", $person_civilstatus);
            $query->bindParam(":person_dateofbirth", $person_dateofbirth);
            $query->bindParam(":contactinfo_ID", $contactinfo_ID);
            
            if ($query->execute()) {
                $person_ID = $db->lastInsertId();
                
                // Create login credentials
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql_login = "INSERT INTO User_Login (person_ID, username, password_hash) VALUES (:person_ID, :username, :password_hash)";
                $query_login = $db->prepare($sql_login);
                $query_login->bindParam(":person_ID", $person_ID);
                $query_login->bindParam(":username", $username);
                $query_login->bindParam(":password_hash", $password_hash);
                
                if ($query_login->execute()) {
                    $db->commit();
                    return true;
                } else {
                    $db->rollBack();
                    return false;
                }
            } else {
                $db->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Tourist Registration Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all tourists
    public function getAllTourists() {
        $sql = "SELECT p.person_ID, CONCAT(n.name_first, ' ', n.name_last) as full_name,
                       ci.contactinfo_email, ph.phone_number, p.person_Nationality
                FROM Person p
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                INNER JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
                LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
                WHERE p.role_ID = 3
                ORDER BY p.person_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get tourist by ID
    public function getTouristById($tourist_ID) {
        $sql = "SELECT p.*, n.*, ci.*, a.*, ph.*, e.*, em.*
                FROM Person p
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                INNER JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
                LEFT JOIN Address_Info a ON ci.address_ID = a.address_ID
                LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
                LEFT JOIN Emergency_Info e ON ci.emergency_ID = e.emergency_ID
                LEFT JOIN Phone_Number em ON e.phone_ID = em.phone_ID
                WHERE p.person_ID = :tourist_ID AND p.role_ID = 3";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourist_ID", $tourist_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Fetch country codes
    public function fetchCountryCode() {
        $sql = "SELECT * FROM country_code ORDER BY countrycode_name ASC";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Helper methods
    private function addNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix, $db) {
        $sql_select = "SELECT name_ID FROM name_info WHERE name_first = :name_first AND name_last = :name_last 
                       AND (name_second = :name_second OR (name_second IS NULL AND :name_second IS NULL)) 
                       AND (name_middle = :name_middle OR (name_middle IS NULL AND :name_middle IS NULL)) 
                       AND (name_suffix = :name_suffix OR (name_suffix IS NULL AND :name_suffix IS NULL))";
        
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":name_first", $name_first);
        $query_select->bindParam(":name_second", $name_second);
        $query_select->bindParam(":name_middle", $name_middle);
        $query_select->bindParam(":name_last", $name_last);
        $query_select->bindParam(":name_suffix", $name_suffix);
        $query_select->execute();
        $result = $query_select->fetch();
        
        if ($result) {
            return $result["name_ID"];
        }
        
        $sql_insert = "INSERT INTO name_info (name_first, name_second, name_middle, name_last, name_suffix) 
                       VALUES (:name_first, :name_second, :name_middle, :name_last, :name_suffix)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":name_first", $name_first);
        $query_insert->bindParam(":name_second", $name_second);
        $query_insert->bindParam(":name_middle", $name_middle);
        $query_insert->bindParam(":name_last", $name_last);
        $query_insert->bindParam(":name_suffix", $name_suffix);
        
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    private function addContactInfo($houseno, $street, $barangay, $city, $province, $country,
                                    $countrycode_ID, $phone_number,
                                    $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship,
                                    $contactinfo_email, $db) {
        $address_ID = $this->addAddress($houseno, $street, $barangay, $city, $province, $country, $db);
        $phone_ID = $this->addPhoneNumber($countrycode_ID, $phone_number, $db);
        $emergency_ID = $this->addEmergencyInfo($emergency_countrycode_ID, $emergency_phonenumber, $emergency_name, $emergency_relationship, $db);
        
        if (!$address_ID || !$phone_ID || !$emergency_ID) {
            return false;
        }
        
        $sql = "INSERT INTO Contact_Info (address_ID, phone_ID, emergency_ID, contactinfo_email) 
                VALUES (:address_ID, :phone_ID, :emergency_ID, :contactinfo_email)";
        $query = $db->prepare($sql);
        $query->bindParam(":address_ID", $address_ID);
        $query->bindParam(":phone_ID", $phone_ID);
        $query->bindParam(":emergency_ID", $emergency_ID);
        $query->bindParam(":contactinfo_email", $contactinfo_email);
        
        if ($query->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    private function addAddress($houseno, $street, $barangay, $city, $province, $country, $db) {
        $sql_select = "SELECT address_ID FROM address_info WHERE address_houseno=:houseno AND address_street=:street 
                       AND address_barangay=:barangay AND address_city=:city AND address_province=:province AND address_country=:country";
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":houseno", $houseno);
        $query_select->bindParam(":street", $street);
        $query_select->bindParam(":barangay", $barangay);
        $query_select->bindParam(":city", $city);
        $query_select->bindParam(":province", $province);
        $query_select->bindParam(":country", $country);
        $query_select->execute();
        $result = $query_select->fetch();
        
        if ($result) {
            return $result["address_ID"];
        }
        
        $sql_insert = "INSERT INTO address_info (address_houseno, address_street, address_barangay, address_city, address_province, address_country) 
                       VALUES (:address_houseno, :address_street, :address_barangay, :address_city, :address_province, :address_country)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":address_houseno", $houseno);
        $query_insert->bindParam(":address_street", $street);
        $query_insert->bindParam(":address_barangay", $barangay);
        $query_insert->bindParam(":address_city", $city);
        $query_insert->bindParam(":address_province", $province);
        $query_insert->bindParam(":address_country", $country);
        
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    private function addPhoneNumber($countrycode_ID, $phone_number, $db) {
        $sql_select = "SELECT phone_ID FROM phone_number WHERE phone_number = :phone_number AND countrycode_ID = :countrycode_ID";
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":countrycode_ID", $countrycode_ID);
        $query_select->bindParam(":phone_number", $phone_number);
        $query_select->execute();
        $result = $query_select->fetch();
        
        if ($result) {
            return $result["phone_ID"];
        }
        
        $sql_insert = "INSERT INTO phone_number(countrycode_ID, phone_number) VALUES (:countrycode_ID, :phone_number)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":countrycode_ID", $countrycode_ID);
        $query_insert->bindParam(":phone_number", $phone_number);
        
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    private function addEmergencyInfo($countrycode_ID, $phone_number, $ename, $erelationship, $db) {
        $phone_ID = $this->addPhoneNumber($countrycode_ID, $phone_number, $db);
        
        if (!$phone_ID) {
            return false;
        }
        
        $sql = "INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship) 
                VALUES (:phone_ID, :ename, :erelationship)";
        $query = $db->prepare($sql);
        $query->bindParam(":phone_ID", $phone_ID);
        $query->bindParam(":ename", $ename);
        $query->bindParam(":erelationship", $erelationship);
        
        if ($query->execute()) {
            return $db->lastInsertId();
        }
        return false;
    }
}
