<?php

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/trait-name-core.php';
require_once __DIR__ . '/trait-phone-core.php';
require_once __DIR__ . '/trait-address-core.php';
require_once __DIR__ . '/trait-emergency-core.php';
require_once __DIR__ . '/trait-contact-core.php';
trait PersonTrait {

    // Check if person with same name and birthdate exists
    public function checkPersonExists($name_first, $name_second, $name_middle, $name_last, $name_suffix, 
                                     $person_dateofbirth, $person_gender) {
        $sql = "SELECT COUNT(*) AS total FROM Person p 
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID 
                WHERE n.name_first = :name_first 
                AND (n.name_second = :name_second OR (n.name_second IS NULL AND :name_second IS NULL)) 
                AND (n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL)) 
                AND n.name_last = :name_last 
                AND (n.name_suffix = :name_suffix OR (n.name_suffix IS NULL AND :name_suffix IS NULL)) 
                AND p.person_DateOfBirth = :person_dateofbirth
                AND p.person_Gender = :person_gender";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first", $name_first);
        $query->bindParam(":name_second", $name_second);
        $query->bindParam(":name_middle", $name_middle);
        $query->bindParam(":name_last", $name_last);
        $query->bindParam(":name_suffix", $name_suffix);
        $query->bindParam(":person_dateofbirth", $person_dateofbirth);
        $query->bindParam(":person_gender", $person_gender);
        
        if ($query->execute()) {
            $record = $query->fetch();
            return $record["total"] > 0;
        }
        return false;
    }


    // Add Person
    public function addPerson(
        $name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay, $city, $province, $country,
        $countrycode_ID, $phone_number,
        $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship,
        $contactinfo_email,
        $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth
    ){

        $db = $this->connect();
        $db->beginTransaction();

        try {

            $name_ID = $this->addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix, $db);

            $contactinfo_ID = $this->addgetContact_Info(
                $houseno, $street, $barangay, $city, $province, $country,
                $countrycode_ID, $phone_number,
                $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship,
                $contactinfo_email,
                $db
            );

            if (!$name_ID || !$contactinfo_ID) {
                $db->rollBack();
                return false;
            }

            $sql = "INSERT INTO Person(name_ID, person_Nationality, person_Gender, person_CivilStatus, person_DateOfBirth, contactinfo_ID)
                    VALUES (:name_ID, :person_nationality, :person_gender, :person_civilstatus, :person_dateofbirth, :contactinfo_ID)";

            $query = $db->prepare($sql);
            $query->bindParam(":name_ID", $name_ID);
            $query->bindParam(":person_nationality", $person_nationality);
            $query->bindParam(":person_gender", $person_gender);
            $query->bindParam(":person_civilstatus", $person_civilstatus);
            $query->bindParam(":person_dateofbirth", $person_dateofbirth);
            $query->bindParam(":contactinfo_ID", $contactinfo_ID);

            if ($query->execute()) {
                $db->commit();
                return true;
            }

            $db->rollBack();
            return false;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Add Person Error: " . $e->getMessage());
            return false;
        }
    }


    // Delete Person
    public function deletePerson($person_ID){
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $sql = "SELECT name_ID, contactinfo_ID FROM Person WHERE person_ID = :person_ID";
            $query = $db->prepare($sql);
            $query->bindParam(":person_ID", $person_ID);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data) return false;

            $name_ID = $data['name_ID'];
            $contactinfo_ID = $data['contactinfo_ID'];

            $sql_delete = "DELETE FROM Person WHERE person_ID = :person_ID";
            $query_delete = $db->prepare($sql_delete);
            $query_delete->bindParam(":person_ID", $person_ID);
            $query_delete->execute();

            $sql_count_name = "SELECT COUNT(*) AS total FROM Person WHERE name_ID = :name_ID";
            $query_name = $db->prepare($sql_count_name);
            $query_name->bindParam(":name_ID", $name_ID);
            $query_name->execute();
            if ($query_name->fetch(PDO::FETCH_ASSOC)['total'] == 0){
                $this->deleteName($name_ID);
            }

            $sql_count_contact = "SELECT COUNT(*) AS total FROM Person WHERE contactinfo_ID = :contactinfo_ID";
            $query_contact = $db->prepare($sql_count_contact);
            $query_contact->bindParam(":contactinfo_ID", $contactinfo_ID);
            $query_contact->execute();
            if ($query_contact->fetch(PDO::FETCH_ASSOC)['total'] == 0){
                $this->deleteContactInfoSafe($contactinfo_ID);
            }

            $db->commit();
            return true;

        } catch (PDOException $e){
            $db->rollBack();
            error_log("Delete Person Error: " . $e->getMessage());
            return false;
        }
    }


    // View Person
    public function viewPerson($person_ID){
        $db = $this->connect();

        $sql = "
            SELECT 
                p.person_ID, p.person_Nationality, p.person_Gender, p.person_DateOfBirth,
                n.name_first, n.name_second, n.name_middle, n.name_last, n.name_suffix,
                c.contactinfo_email,
                pn.phone_number,
                cc.country_codename AS phone_country_code,
                e.emergency_Name, e.emergency_Relationship,
                epn.phone_number AS emergency_phone_number,
                ecc.country_codename AS emergency_country_code,
                a.address_houseno, a.address_street,
                b.barangay_name, ct.city_name, pr.province_name, co.country_name
            FROM Person p
            JOIN Name_Info n ON p.name_ID = n.name_ID
            JOIN Contact_Info c ON p.contactinfo_ID = c.contactinfo_ID
            LEFT JOIN Phone_Number pn ON c.phone_ID = pn.phone_ID
            LEFT JOIN Country cc ON pn.country_ID = cc.country_ID
            LEFT JOIN Emergency_Info e ON c.emergency_ID = e.emergency_ID
            LEFT JOIN Phone_Number epn ON e.phone_ID = epn.phone_ID
            LEFT JOIN Country ecc ON epn.country_ID = ecc.country_ID
            LEFT JOIN Address_Info a ON c.address_ID = a.address_ID
            LEFT JOIN Barangay b ON a.barangay_ID = b.barangay_ID
            LEFT JOIN City ct ON b.city_ID = ct.city_ID
            LEFT JOIN Province pr ON ct.province_ID = pr.province_ID
            LEFT JOIN Country co ON pr.country_ID = co.country_ID
            WHERE p.person_ID = :person_ID
        ";

        $query = $db->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    // Search People
    public function searchPersons($searchTerm){
        $db = $this->connect();
        $searchTerm = "%".$searchTerm."%";

        $sql = "
            SELECT 
                p.person_ID,
                CONCAT(n.name_first, ' ', n.name_last) AS full_name,
                c.contactinfo_email,
                pn.phone_number,
                a.address_street,
                ct.city_name,
                co.country_name
            FROM Person p
            JOIN Name_Info n ON p.name_ID = n.name_ID
            JOIN Contact_Info c ON p.contactinfo_ID = c.contactinfo_ID
            LEFT JOIN Phone_Number pn ON c.phone_ID = pn.phone_ID
            LEFT JOIN Address_Info a ON c.address_ID = a.address_ID
            LEFT JOIN Barangay b ON a.barangay_ID = b.barangay_ID
            LEFT JOIN City ct ON b.city_ID = ct.city_ID
            LEFT JOIN Province pr ON ct.province_ID = pr.province_ID
            LEFT JOIN Country co ON pr.country_ID = co.country_ID
            WHERE 
                n.name_first LIKE :search
                OR n.name_last LIKE :search
                OR pn.phone_number LIKE :search
                OR c.contactinfo_email LIKE :search
                OR co.country_name LIKE :search
                OR ct.city_name LIKE :search
            ORDER BY full_name ASC
        ";

        $query = $db->prepare($sql);
        $query->bindParam(":search", $searchTerm);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
