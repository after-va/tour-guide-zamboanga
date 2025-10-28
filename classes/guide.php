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
        $username, $password,
        $license_number, $license_type = null, $issue_date = null, $expiry_date = null, $issuing_authority = null) {
    
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
                // Get the person_ID from the login_ID
                $sql = "SELECT person_ID FROM User_Login WHERE login_ID = :login_ID";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':login_ID', $login_ID);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && isset($result['person_ID'])) {
                    $person_ID = $result['person_ID'];
                    
                    // Add the guide license (use the same DB connection to avoid locking issues)
                    if ($this->addGuideLicense($person_ID, $license_number, $license_type, $issue_date, $expiry_date, $issuing_authority, $db)) {
                        $db->commit();
                        error_log("Guide registration successful for user: " . $username);
                        return true;
                    } else {
                        $error = $this->getLastError() ?: "Failed to add guide license";
                        error_log("Failed to add guide license: " . $error);
                        $db->rollBack();
                        $this->setLastError($error);
                        return false;
                    }
                } else {
                    $error = "Failed to get person_ID for login_ID: " . $login_ID;
                    error_log($error);
                    $db->rollBack();
                    $this->setLastError($error);
                    return false;
                }
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

    /**
     * Add a guide license. Accepts an optional PDO connection to participate in an existing transaction.
     * If $db is provided, it will be used (no new transaction started here). If null, a new connection is used.
     */
    private function addGuideLicense($guide_ID, $license_number, $license_type = null, $issue_date = null, $expiry_date = null, $issuing_authority = null, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            $sql = "INSERT INTO Guide_License (
                guide_ID, license_number, license_type, issue_date, 
                expiry_date, issuing_authority, status, verification_status
            ) VALUES (
                :guide_ID, :license_number, :license_type, :issue_date,
                :expiry_date, :issuing_authority, 'pending', 'pending'
            )";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':guide_ID', $guide_ID);
            $stmt->bindParam(':license_number', $license_number);
            $stmt->bindParam(':license_type', $license_type);
            $stmt->bindParam(':issue_date', $issue_date);
            $stmt->bindParam(':expiry_date', $expiry_date);
            $stmt->bindParam(':issuing_authority', $issuing_authority);

            $res = $stmt->execute();
            if ($closeConnection) {
                $db = null;
            }
            return $res;
        } catch (PDOException $e) {
            $this->setLastError("Failed to add guide license: " . $e->getMessage());
            error_log("Failed to add guide license: " . $e->getMessage());
            if ($closeConnection) {
                $db = null;
            }
            return false;
        }
    }

    /**
     * Public wrapper to add a license for an existing guide
     */
    public function addLicense($guide_ID, $license_number, $license_type = null, $issue_date = null, $expiry_date = null, $issuing_authority = null) {
        return $this->addGuideLicense($guide_ID, $license_number, $license_type, $issue_date, $expiry_date, $issuing_authority);
    }

    /**
     * Generate a random, unique license number.
     * Format: PREFIX-XXXX-XXXX where X are uppercase hex chars.
     * Returns license string on success, false on failure.
     */
    public function generateLicenseNumber($prefix = 'TG', $parts = [4,4], $maxAttempts = 10) {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        for ($i = 0; $i < $maxAttempts; $i++) {
            $neededChars = array_sum($parts);
            try {
                $rand = strtoupper(bin2hex(random_bytes(ceil($neededChars/2))));
            } catch (Exception $e) {
                // Fallback to uniqid
                $rand = strtoupper(substr(sha1(uniqid('', true)), 0, $neededChars));
            }
            $rand = substr($rand, 0, $neededChars);
            $chunks = [];
            $pos = 0;
            foreach ($parts as $len) {
                $chunks[] = substr($rand, $pos, $len);
                $pos += $len;
            }
            $license = $prefix . '-' . implode('-', $chunks);

            // Check uniqueness
            $sql = "SELECT license_ID FROM Guide_License WHERE license_number = :license_number LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':license_number', $license);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return $license;
            }
            // else try again
        }

        $this->setLastError('Unable to generate unique license number after attempts');
        return false;
    }

    public function getGuideLicense($guide_ID) {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            $sql = "SELECT * FROM Guide_License WHERE guide_ID = :guide_ID";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':guide_ID', $guide_ID);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->setLastError("Failed to get guide license: " . $e->getMessage());
            error_log("Failed to get guide license: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all licenses or filter by guide_ID
     * @param int|null $guide_ID
     * @return array|false
     */
    public function getAllLicenses($guide_ID = null) {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            if ($guide_ID) {
                $sql = "SELECT * FROM Guide_License WHERE guide_ID = :guide_ID ORDER BY created_at DESC";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':guide_ID', $guide_ID, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $sql = "SELECT * FROM Guide_License ORDER BY created_at DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->setLastError("Failed to get licenses: " . $e->getMessage());
            error_log("Failed to get licenses: " . $e->getMessage());
            return false;
        }
    }

    /**
     * List pending licenses (verification_status = 'pending')
     * @return array|false
     */
    public function listPendingLicenses() {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            $sql = "SELECT gl.*, ni.name_first, ni.name_last, ci.contactinfo_email
                    FROM Guide_License gl
                    LEFT JOIN Person p ON gl.guide_ID = p.person_ID
                    LEFT JOIN Name_Info ni ON p.name_ID = ni.name_ID
                    LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
                    WHERE gl.verification_status = 'pending' ORDER BY gl.created_at ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->setLastError("Failed to list pending licenses: " . $e->getMessage());
            error_log("Failed to list pending licenses: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify or reject a license (sets verification_status and status)
     * @param int $license_ID
     * @param int $verifier_person_ID
     * @param string $verification_status ('verified'|'rejected')
     * @param string|null $status ('active'|'revoked' etc.) optional override
     * @return bool
     */
    public function verifyLicense($license_ID, $verifier_person_ID, $verification_status = 'verified', $status = null) {
        $allowed = ['verified', 'rejected'];
        if (!in_array($verification_status, $allowed)) {
            $this->setLastError("Invalid verification_status");
            return false;
        }

        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            $db->beginTransaction();

            $sql = "UPDATE Guide_License SET verification_status = :verification_status, verified_by = :verified_by, verified_at = :verified_at";
            if ($status !== null) {
                $sql .= ", status = :status";
            }
            $sql .= " WHERE license_ID = :license_ID";

            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->bindParam(':verification_status', $verification_status);
            $stmt->bindParam(':verified_by', $verifier_person_ID, PDO::PARAM_INT);
            $stmt->bindParam(':verified_at', $now);
            if ($status !== null) $stmt->bindParam(':status', $status);
            $stmt->bindParam(':license_ID', $license_ID, PDO::PARAM_INT);

            $res = $stmt->execute();
            if (!$res) {
                $errorInfo = $stmt->errorInfo();
                $this->setLastError("Failed to update license: " . ($errorInfo[2] ?? 'unknown'));
                $db->rollBack();
                return false;
            }

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            $this->setLastError("Failed to verify license: " . $e->getMessage());
            error_log("Failed to verify license: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark a license as expired
     * @param int $license_ID
     * @return bool
     */
    public function expireLicense($license_ID) {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            $sql = "UPDATE Guide_License SET status = 'expired' WHERE license_ID = :license_ID";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':license_ID', $license_ID, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->setLastError("Failed to expire license: " . $e->getMessage());
            error_log("Failed to expire license: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Expire licenses whose expiry_date is reached or passed.
     * Returns number of licenses updated or false on error.
     */
    public function expireDueLicenses() {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            return false;
        }

        try {
            $sql = "UPDATE Guide_License SET status = 'expired' WHERE expiry_date IS NOT NULL AND expiry_date <= CURDATE() AND status != 'expired'";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $count = $stmt->rowCount();
            return $count;
        } catch (PDOException $e) {
            $this->setLastError("Failed to expire due licenses: " . $e->getMessage());
            error_log("Failed to expire due licenses: " . $e->getMessage());
            return false;
        }
    }
}
