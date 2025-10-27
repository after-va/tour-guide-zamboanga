<?php

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/trait-name-core.php';
require_once __DIR__ . '/trait-phone-core.php';
require_once __DIR__ . '/trait-address-core.php';
require_once __DIR__ . '/trait-emergency-core.php';
require_once __DIR__ . '/trait-contact-core.php';

trait PersonCoreTrait {
    use NameCoreTrait, PhoneCoreTrait, AddressCoreTrait, EmergencyCoreTrait, ContactCoreTrait;

    // Creates a Person and returns person_ID on success, false on failure
    protected function addPersonRecord( $name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $address_houseno, $address_street, $barangay_ID,
        $phone_country_ID, $phone_number,
        $em_name, $em_country_ID, $em_phone, $em_relationship,
        $contact_email,
        $person_nationality, $person_gender, $person_dateofbirth
    ){
        $db = $this->connect();
        $db->beginTransaction();
        try {
            $name_ID = $this->addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix, $db);
            $address_ID = $this->addgetAddressInfo($address_houseno, $address_street, $barangay_ID, $db);
            $phone_ID = $this->addgetPhone($phone_country_ID, $phone_number, $db);
            $emergency_ID = $this->addgetEmergency($em_country_ID, $em_phone, $em_name, $em_relationship, $db);
                
                if (!$name_ID || !$address_ID || !$phone_ID || !$emergency_ID){
                    $db->rollBack();
                    return false;
                }
                
                $contactinfo_ID = $this->addgetContactInfo($address_ID, $phone_ID, $emergency_ID, $contact_email, $db);
                
                if (!$contactinfo_ID){
                    $db->rollBack();
                    return false;
                }

            $sql = "INSERT INTO Person (name_ID, person_Nationality, person_Gender, person_DateOfBirth, contactinfo_ID) VALUES (:n, :nat, :gen, :dob, :ci)";
            $q = $db->prepare($sql);
            $q->bindParam(":n", $name_ID, PDO::PARAM_INT);
            $q->bindParam(":nat", $person_nationality);
            $q->bindParam(":gen", $person_gender);
            $q->bindParam(":dob", $person_dateofbirth);
            $q->bindParam(":ci", $contactinfo_ID, PDO::PARAM_INT);
            if (!$q->execute()){
                $db->rollBack();
                return false;
            }
            $person_ID = (int)$db->lastInsertId();
            $db->commit();
            return $person_ID;
        } catch (Throwable $e){
            $db->rollBack();
            return false;
        }
    }
}
