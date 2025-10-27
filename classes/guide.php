<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/trait/person/trait-name-info.php';
require_once __DIR__ . '/trait/person/trait-address.php';
require_once __DIR__ . '/trait/person/trait-phone.php';
require_once __DIR__ . '/trait/person/trait-emergency.php';
require_once __DIR__ . '/trait/person/trait-contact-info.php';
require_once __DIR__ . '/trait/person/trait-person.php';
require_once __DIR__ . '/trait/person/trait-user.php';

class Guide extends Database {
    use PersonTrait, UserTrait, NameInfoTrait, AddressTrait, PhoneTrait, EmergencyTrait, ContactInfoTrait;
    
    private $lastError = "";

    public function addGuide($name_first, $name_second, $name_middle, $name_last, $name_suffix,
        $houseno, $street, $barangay,
        $country_ID, $phone_number,
        $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship,
        $contactinfo_email,
        $person_nationality, $person_gender, $person_dateofbirth, 
        $username, $password) {
    
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            error_log("Database connection failed in addGuide");
            return false;
        }

        $db->beginTransaction();

        try {
            error_log("Calling addUser from addGuide");
            $login_ID = $this->addUser(
                $name_first, 
                $name_second, 
                $name_middle, 
                $name_last, 
                $name_suffix,
                $houseno, 
                $street, 
                $barangay,
                $country_ID, 
                $phone_number,
                $emergency_name, 
                $emergency_country_ID, 
                $emergency_phonenumber, 
                $emergency_relationship,
                $contactinfo_email,
                $person_nationality, 
                $person_gender, 
                $person_dateofbirth, 
                $username, 
                $password,
                $db
            );

            error_log("addUser returned login_ID: " . ($login_ID ?: 'false'));

            if (!$login_ID) {
                $error = $this->getLastError() ?: "Failed to create user account";
                error_log("addUser failed: " . $error);
                $db->rollBack();
                $this->setLastError($error);
                return false;
            }

            $role_ID = 2; // Tour Guide role_ID is 2
            $created_at = date('Y-m-d H:i:s');
            $is_approved = 0; // Guide registration requires admin approval

            $sql = "INSERT INTO Account_Role (login_ID, role_ID, created_at, is_approved) VALUES (:login_ID, :role_ID, :created_at, :is_approved)";
            $query = $db->prepare($sql);
            $query->bindParam(":login_ID", $login_ID, PDO::PARAM_INT);
            $query->bindParam(":role_ID", $role_ID, PDO::PARAM_INT);
            $query->bindParam(":created_at", $created_at);
            $query->bindParam(":is_approved", $is_approved, PDO::PARAM_INT);

            $result = $query->execute();
            
            if ($result) {
                $db->commit();
                error_log("Guide registration successful for user: " . $username);
                return true; 
            } else {
                $errorInfo = $query->errorInfo();
                $error = "Database error: " . ($errorInfo[2] ?? 'Unknown error');
                error_log("Failed to add role: " . $error);
                $db->rollBack();
                $this->setLastError($error);
                return false;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $this->setLastError($e->getMessage());
            error_log("Guide Registration Error: " . $e->getMessage()); 
            return false;
        }
    }

    public function requestGuideRole($login_ID, $db) {
        try {
            // Check if user already has a guide role
            $sql_check = "SELECT account_role_ID FROM Account_Role WHERE login_ID = :login_ID AND role_ID = 2";
            $query_check = $db->prepare($sql_check);
            $query_check->bindParam(":login_ID", $login_ID, PDO::PARAM_INT);
            $query_check->execute();
            
            if ($query_check->rowCount() > 0) {
                $this->setLastError("You already have a guide role request or account.");
                return false;
            }

            // Create pending guide role
            $sql = "INSERT INTO Account_Role (login_ID, role_ID, created_at, is_approved) VALUES (:login_ID, :role_ID, :created_at, :is_approved)";
            $query = $db->prepare($sql);
            $query->bindParam(":login_ID", $login_ID, PDO::PARAM_INT);
            $role_ID = 2;
            $is_approved = 0;
            $created_at = date('Y-m-d H:i:s');
            $query->bindParam(":role_ID", $role_ID, PDO::PARAM_INT);
            $query->bindParam(":created_at", $created_at);
            $query->bindParam(":is_approved", $is_approved, PDO::PARAM_INT);
            
            if ($query->execute()) {
                error_log("Guide role request created for login_ID: " . $login_ID);
                return true;
            } else {
                $this->setLastError("Failed to create guide role request.");
                return false;
            }
        } catch (PDOException $e) {
            $this->setLastError("Error requesting guide role: " . $e->getMessage());
            error_log("Error requesting guide role: " . $e->getMessage());
            return false;
        }
    }

    public function listGuides(){
        $db = $this->connect();
        $sql = "SELECT person_ID, full_name, email, phone_number, rating, role_name FROM v_user_details WHERE role_name = 'Tour Guide' AND role_is_active = 1 GROUP BY person_ID, full_name, email, phone_number, rating, role_name ORDER BY full_name";
        $q = $db->prepare($sql);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLastError() {
        return $this->lastError;
    }
    
    public function setLastError($error) {
        $this->lastError = $error;
        return $this;
    }
    
    public function fetchCountryCode(){
        $sql = "SELECT * FROM country";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function fetchCountry(){
        $sql = "SELECT * FROM Country";
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function fetchRegion($country_ID = null){
        if ($country_ID === null) {
            $sql = "SELECT * FROM Region";
            $query = $this->connect()->prepare($sql);
        } else {
            $sql = "SELECT * FROM Region WHERE country_ID = :country_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":country_ID", $country_ID);
        }
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function fetchProvince($region_ID = null){
        if ($region_ID === null || $region_ID === "") {
            $sql = "SELECT * FROM Province";
            $query = $this->connect()->prepare($sql);
        } else {
            $sql = "SELECT * FROM Province WHERE region_ID = :region_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":region_ID", $region_ID);
        }
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function fetchCity($province_ID = null){
        if ($province_ID === null || $province_ID === "") {
            $sql = "SELECT * FROM City";
            $query = $this->connect()->prepare($sql);
        } else {
            $sql = "SELECT * FROM City WHERE province_ID = :province_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":province_ID", $province_ID);
        }
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function fetchBarangay($city_ID = null){
        if ($city_ID === null || $city_ID === "") {
            $sql = "SELECT * FROM Barangay";
            $query = $this->connect()->prepare($sql);
        } else {
            $sql = "SELECT * FROM Barangay WHERE city_ID = :city_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":city_ID", $city_ID);
        }
        if ($query->execute()) {
            return $query->fetchAll();
        } else {
            return null;
        }
    }

    public function addgetRegion($region_name, $country_ID, $db){
        $sql_select = "SELECT region_ID 
                       FROM Region 
                       WHERE region_name = :region_name 
                       AND country_ID = :country_ID";
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":region_name", $region_name);
        $query_select->bindParam(":country_ID", $country_ID);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["region_ID"];
        }

        $sql_insert = "INSERT INTO region (region_name, country_ID) 
                       VALUES (:region_name, :country_ID)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":region_name", $region_name);
        $query_insert->bindParam(":country_ID", $country_ID);
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function addgetProvince($province_name, $region_ID, $db){
        $sql_select = "SELECT province_ID 
                       FROM Province 
                       WHERE province_name = :province_name 
                       AND region_ID = :region_ID";
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":province_name", $province_name);
        $query_select->bindParam(":region_ID", $region_ID);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["province_ID"];
        }

        $sql_insert = "INSERT INTO province (province_name, region_ID) 
                       VALUES (:province_name, :region_ID)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":province_name", $province_name);
        $query_insert->bindParam(":region_ID", $region_ID);
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function addgetCity($city_name,$province_ID, $db){
        $sql_select = "SELECT city_ID 
                       FROM City 
                       WHERE city_name = :city_name 
                       AND province_ID = :province_ID";
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":city_name", $city_name);
        $query_select->bindParam(":province_ID", $province_ID);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["city_ID"];
        }

        $sql_insert = "INSERT INTO city_municipality (city_name, province_ID) 
                       VALUES (:city_name, :province_ID)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":city_name", $city_name);
        $query_insert->bindParam(":province_ID", $province_ID);
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }

    }

    public function addgetBarangay($barangay_name, $city_ID, $db){
        $sql_select = "SELECT barangay_ID 
                       FROM Barangay 
                       WHERE barangay_name = :barangay_name 
                       AND city_ID = :city_ID";
        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":barangay_name", $barangay_name);
        $query_select->bindParam(":city_ID", $city_ID);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["barangay_ID"];
        }

        $sql_insert = "INSERT INTO barangay (barangay_name, city_ID) 
                       VALUES (:barangay_name, :city_ID)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":barangay_name", $barangay_name);
        $query_insert->bindParam(":city_ID", $city_ID);
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }
}
