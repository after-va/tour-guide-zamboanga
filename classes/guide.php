<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/account/account-login.php";

class Guide extends Database {
    use AccountLoginTrait;

    public function addgetGuide($name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay, $country_ID, $phone_number, $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email, $person_nationality, $person_gender, $person_dateofbirth, $user_username, $user_password){
        
            $db = $this->connect();
            if (!$db) {
                $this->setLastError("Database connection failed");
                error_log("Database connection failed in addTourist");
                return false;
            }

            $db->beginTransaction();

            try{
                $person_ID = $this->addgetPerson($name_first, $name_second, $name_middle, $name_last, $name_suffix, $houseno, $street, $barangay, $country_ID, $phone_number, $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship, $contactinfo_email, $person_nationality, $person_gender, $person_dateofbirth, $db);
                $user_ID = $this->addgetUserLogin($person_ID, $user_username, $user_password, $db);
                $role_ID = 2;

                if (!$person_ID || !$user_ID) {
                    $db->rollBack();
                    return false;
                }

                $accountlogin_ID = $this->addgetAccountLogin($user_ID, $role_ID, $db);

                if (!$accountlogin_ID) {
                    $db->rollBack();
                    return false;
                } else {
                    $db->commit();
                    return true;
                }

            } catch (PDOException $e) {
            $db->rollBack();
            error_log("Tourist Registration Error: " . $e->getMessage()); 
            return false;
        }


    }

    public function viewAllGuide(){
        $sql = "SELECT 
                    g.guide_ID,
                    CONCAT(
                        n.name_first, 
                        IF(n.name_middle IS NOT NULL, CONCAT(' ', n.name_middle), ''),
                        ' ', 
                        n.name_last,
                        IF(n.name_suffix IS NOT NULL, CONCAT(' ', n.name_suffix), '')
                    ) AS guide_name
                FROM Guide g
                JOIN Account_Info ai ON g.account_ID = ai.account_ID
                JOIN User_Login ul ON ai.user_ID = ul.user_ID
                JOIN Person p ON ul.person_ID = p.person_ID
                JOIN Name_Info n ON p.name_ID = n.name_ID
                ORDER BY n.name_last, n.name_first";
        $db = $this->connect();
        $query = $db->prepare($sql);

        if ($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }
}
