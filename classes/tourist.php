<?php
require_once "database.php";
require_once "auth.php";
require_once "trait/person/trait-person.php";


class Tourist extends Database {
    use PersonTrait;

    public function registerTourist(
        $name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $address_houseno, $address_street, $barangay_ID,
        $phone_country_ID, $phone_number,
        $em_name, $em_country_ID, $em_phone, $em_relationship,
        $contact_email,
        $person_nationality, $person_gender, $person_dateofbirth,
        $username, $password
    ){
        $person_ID = $this->addPersonRecord(
            $name_first, $name_second, $name_middle, $name_last, $name_suffix,
            $address_houseno, $address_street, $barangay_ID,
            $phone_country_ID, $phone_number,
            $em_name, $em_country_ID, $em_phone, $em_relationship,
            $contact_email,
            $person_nationality, $person_gender, $person_dateofbirth
        );
        if (!$person_ID) return false;
        $auth = new Auth();
        $created = $auth->createUserLogin($person_ID, $username, $password, 'Tourist');
        return $created === 'username_exists' ? 'username_exists' : (bool)$created;
    }

    public function fetchCountries(){
        $sql = "SELECT country_ID, country_name, country_codenumber FROM Country ORDER BY country_name";
        $q = $this->connect()->prepare($sql);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
