<?php 

require_once 'database.php';
require_once "trait/trait-address.php";
require_once "trait/trait-contact-info.php";
require_once "trait/trait-name.php";
require_once "trait/trait-phone.php";
require_once "trait/trait-emergency.php";


class Tourist extends Database {

    use NameTrait, AddressTrait, PhoneTrait, EmergencyTrait, ContactInfoTrait;

    // Add Tourist
    public function addTourist($name_first, $name_second, $name_middle, $name_last, $name_suffix,$houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email,$person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth){
        $db = $this->connect();
        $db->beginTransaction();
        try{
            // Pass the single $db connection to all helper methods
            $name_ID = $this->addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix, $db);
            $contactinfo_ID = $this->addgetContact_Info($houseno, $street, $barangay, $city, $province, $country, $countrycode_ID,$phone_number, $emergency_name, $emergency_countrycode_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email, $db);
            
            $role_ID = 1;
            
            if (!$name_ID || !$contactinfo_ID) {
                $db->rollBack();
                return false;
            }

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

            if ($query->execute()){
                $db->commit();
                return true; 
            } else {
                $db->rollBack();
                return false;
            }


        }catch (PDOException $e) {
            $db->rollBack();
            error_log("Tourist Registration Error: " . $e->getMessage()); 
            return false;
        }
    }

    public function deleteTourist($person_ID){
        $db = $this->connect();
        $db->beginTransaction();

        try {
            // STEP 1: Get linked IDs from Person
            $sql = "SELECT name_ID, contactinfo_ID FROM Person WHERE person_ID = :person_ID";
            $query = $db->prepare($sql);
            $query->bindParam(":person_ID", $person_ID);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if (!$data){
                return false;
            }

            $name_ID = $data['name_ID'];
            $contactinfo_ID = $data['contactinfo_ID'];

            // STEP 2: Delete the Person row
            $sql_delete_person = "DELETE FROM Person WHERE person_ID = :person_ID";
            $query_person = $db->prepare($sql_delete_person);
            $query_person->bindParam(":person_ID", $person_ID);
            $query_person->execute();

            // STEP 3: Check if the name is used by anyone else
            $sql_count_name = "SELECT COUNT(*) AS total FROM Person WHERE name_ID = :name_ID";
            $query_name_count = $db->prepare($sql_count_name);
            $query_name_count->bindParam(":name_ID", $name_ID);
            $query_name_count->execute();
            $name_used = $query_name_count->fetch(PDO::FETCH_ASSOC)['total'];

            if ($name_used == 0){
                // Safe to delete name
                $this->deleteName($name_ID);
            }

            // STEP 4: Check if the contact info is used by anyone else
            $sql_count_contact = "SELECT COUNT(*) AS total FROM Person WHERE contactinfo_ID = :contactinfo_ID";
            $query_contact_count = $db->prepare($sql_count_contact);
            $query_contact_count->bindParam(":contactinfo_ID", $contactinfo_ID);
            $query_contact_count->execute();
            $contact_used = $query_contact_count->fetch(PDO::FETCH_ASSOC)['total'];

            if ($contact_used == 0){
                // Safely delete full contact info set
                $this->deleteContactInfoSafe($contactinfo_ID);
            }

            $db->commit();
            return true;

        } catch (PDOException $e){
            $db->rollBack();
            error_log("Delete Tourist Error: " . $e->getMessage());
            return false;
        }
    }

    public function viewTourist($person_ID)
{
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

            WHERE p.person_ID = :person_ID AND p.role_ID = 1
        ";

        $query = $db->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function searchTourists($searchTerm){
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
            WHERE p.role_ID = 1
            AND (
                n.name_first LIKE :search
                OR n.name_last LIKE :search
                OR pn.phone_number LIKE :search
                OR c.contactinfo_email LIKE :search
                OR co.country_name LIKE :search
                OR ct.city_name LIKE :search
            )
            ORDER BY full_name ASC
        ";

        $query = $db->prepare($sql);
        $query->bindParam(":search", $searchTerm);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }



}