<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/account/account-login.php";
require_once "trait/tour/tour-packages.php";
require_once "trait/tour/tour-spots.php";
require_once "trait/tour/tour-packagespots.php";
require_once "trait/tour/schedule.php";
require_once "trait/tour/pricing.php";
require_once "trait/tour/people.php";


class Guide extends Database {
    use AccountLoginTrait;
    use TourPackagesTrait, PeopleTrait, PricingTrait, ScheduleTrait;
    use TourSpotsTrait;
    use TourPackageSpot;

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
                $user_ID = $this->addUser($name_first, $name_second, $name_middle, $name_last, $name_suffix,
                $houseno, $street, $barangay, 
                $country_ID, $phone_number, 
                $emergency_name, $emergency_country_ID, $emergency_phonenumber, $emergency_relationship, 
                $contactinfo_email, $person_nationality, $person_gender, $person_dateofbirth, 
                $username, $password, $db);


                if (!$user_ID) {
                    $db->rollBack();
                    return false;
                }

                $account_ID = $this->addAccountGuide($user_ID, $db);

                if (!$account_ID) {
                    $db->rollBack();
                    return false;
                }

                $guide_ID = $this->addGuide_ID($account_ID, $db);

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

    public function addGuide_ID($account_ID, $db){

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


    public function viewPackageByGuideID($guide_ID){
        $sql = "SELECT * FROM Tour_Package WHERE guide_ID = :guide_ID";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    

    }

    public function getGuide_ID($account_ID){
        $sql = "SELECT g.guide_ID FROM Guide AS g WHERE g.account_ID = :account_ID";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->bindParam(":account_ID", $account_ID);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['guide_ID'] : null;
    }

    public function getScheduleByID($scheduleID) {
        $db = $this->connect();
        $sql = "SELECT * FROM Schedule WHERE schedule_ID = :scheduleID";
        $query = $db->prepare($sql);
        $query->bindParam(':scheduleID', $scheduleID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    

    public function getPricingByID($pricingID) {
        $db = $this->connect();
        $sql = "SELECT * FROM Pricing WHERE pricing_ID = :pricingID";
        $query = $db->prepare($sql);
        $query->bindParam(':pricingID', $pricingID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getPeopleByID($peopleID) {
        $db = $this->connect();
        $sql = "SELECT * FROM Number_Of_People WHERE numberofpeople_ID = :peopleID";
        $query = $db->prepare($sql);
        $query->bindParam(':peopleID', $peopleID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getSpotsByPackage($packageID) {
        $sql = "SELECT ts.* 
                FROM Tour_Package_Spots tps
                JOIN Tour_Spots ts ON tps.spots_ID = ts.spots_ID
                WHERE tps.tourpackage_ID = ?";
        $query = $this->conn->prepare($sql);
        $query->execute([$packageID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTourPackage($tourpackage_ID, $guide_ID, $tourpackage_name, $tourpackage_desc, 
                                    $schedule_days, $numberofpeople_maximum, $numberofpeople_based, 
                                    $currency, $basedAmount, $discount, $spots) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Get current package data to find related records
            $currentPackage = $this->getTourPackageById($tourpackage_ID);
            if (!$currentPackage) {
                throw new Exception("Package not found");
            }

            // Update schedule and related information
            $schedule_ID = $this->addgetSchedule($schedule_days, $numberofpeople_maximum, $numberofpeople_based, 
                                               $currency, $basedAmount, $discount, $db);
            if (!$schedule_ID) {
                throw new Exception("Failed to update schedule");
            }

            // Update tour package
            $sql = "UPDATE Tour_Package SET 
                    guide_ID = :guide_ID,
                    tourpackage_name = :tourpackage_name,
                    tourpackage_desc = :tourpackage_desc,
                    schedule_ID = :schedule_ID
                    WHERE tourpackage_ID = :tourpackage_ID";
            
            $query = $db->prepare($sql);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':tourpackage_name', $tourpackage_name);
            $query->bindParam(':tourpackage_desc', $tourpackage_desc);
            $query->bindParam(':schedule_ID', $schedule_ID);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            
            if (!$query->execute()) {
                throw new Exception("Failed to update tour package");
            }

            // Delete existing spots
            $sql = "DELETE FROM Tour_Package_Spots WHERE tourpackage_ID = :tourpackage_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            $query->execute();

            // Add new spots
            if (!empty($spots)) {
                foreach ($spots as $spot_ID) {
                    $sql = "INSERT INTO Tour_Package_Spots (tourpackage_ID, spots_ID) VALUES (:tourpackage_ID, :spots_ID)";
                    $query = $db->prepare($sql);
                    $query->bindParam(':tourpackage_ID', $tourpackage_ID);
                    $query->bindParam(':spots_ID', $spot_ID);
                    $query->execute();
                }
            }

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error updating tour package: " . $e->getMessage());
            return false;
        }
    }

     public function getTourPackageById($tourpackage_ID) {
        $db = $this->connect();
        try {
            // Get tour package information
            $sql = "SELECT tp.*, s.schedule_days, nop.numberofpeople_maximum, nop.numberofpeople_based,
                    p.pricing_currency, p.pricing_based, p.pricing_discount
                    FROM Tour_Package tp
                    JOIN Schedule s ON tp.schedule_ID = s.schedule_ID
                    JOIN Number_Of_People nop ON s.numberofpeople_ID = nop.numberofpeople_ID
                    JOIN Pricing p ON nop.pricing_ID = p.pricing_ID
                    WHERE tp.tourpackage_ID = :tourpackage_ID";
            
            $query = $db->prepare($sql);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            $query->execute();
            
            $package = $query->fetch(PDO::FETCH_ASSOC);
            if (!$package) {
                return null;
            }

            // Get associated spots
            $sql = "SELECT spots_ID FROM Tour_Package_Spots WHERE tourpackage_ID = :tourpackage_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            $query->execute();
            $package['spots'] = array_column($query->fetchAll(PDO::FETCH_ASSOC), 'spots_ID');

            return $package;
        } catch (PDOException $e) {
            error_log("Error getting tour package: " . $e->getMessage());
            return null;
        }
    }

    public function getAllSpots(){
        $sql = "SELECT * FROM tour_spots";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addgetSchedule($schedule_days, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db){
        // First check if a matching schedule exists
        $sql = "SELECT s.schedule_ID
                FROM Schedule s
                JOIN Number_Of_People nop ON s.numberofpeople_ID = nop.numberofpeople_ID
                JOIN Pricing p ON nop.pricing_ID = p.pricing_ID
                WHERE 
                    s.schedule_days = :schedule_days
                    AND nop.numberofpeople_maximum = :max
                    AND nop.numberofpeople_based = :based
                    AND p.pricing_currency = :currency
                    AND p.pricing_based = :basedAmount
                    AND p.pricing_discount = :discount";
                    
        $query = $db->prepare($sql);
        $query->bindParam(':schedule_days', $schedule_days);
        $query->bindParam(':max', $numberofpeople_maximum);
        $query->bindParam(':based', $numberofpeople_based);
        $query->bindParam(':currency', $currency);
        $query->bindParam(':basedAmount', $basedAmount);
        $query->bindParam(':discount', $discount);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result["schedule_ID"];
        }

        // If no matching schedule exists, create a new one
        $numberofpeople_ID = $this->addgetPeople($numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db);
        if(!$numberofpeople_ID){
            return false;
        }

        $sql = "INSERT INTO Schedule(numberofpeople_ID, schedule_days) VALUES (:numberofpeople_ID, :schedule_days)";
        $query = $db->prepare($sql);
        $query->bindParam(':numberofpeople_ID', $numberofpeople_ID);
        $query->bindParam(':schedule_days', $schedule_days);

        if ($query->execute()){
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    
    public function addgetTouristByUserID($user_ID){
        $sql = "SELECT account_ID FROM Account_Info WHERE user_ID = :user_ID AND role_ID = 3";
        $db = $this->connect();
        $query_select = $db->prepare($sql);
        $query_select->bindParam(":user_ID", $user_ID);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["account_ID"];
        }

        $sql = "INSERT INTO Account_Info(user_ID, role_ID) VALUES (:user_ID, 3)";
        $query_insert = $db->prepare($sql);
        $query_insert->bindParam(":user_ID", $user_ID);

        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function changeAccountToTourist($user_ID){
        $db = $this->connect();
        $db->beginTransaction();

        try {
           $sql = "SELECT account_ID FROM Account_Info WHERE user_ID = :user_ID AND role_ID = 3";
            $db = $this->connect();
            $query_select = $db->prepare($sql);
            $query_select->bindParam(":user_ID", $user_ID);
            $query_select->execute();
            $result = $query_select->fetch();

            if($result){
                return $result["account_ID"];
            }

            $sql = "INSERT INTO Account_Info(user_ID, role_ID, account_status) VALUES (:user_ID, 3, Active)";
            $query_insert = $db->prepare($sql);
            $query_insert->bindParam(":user_ID", $user_ID);

            if ($query_insert->execute()) {
                return $db->lastInsertId();
            } else {
                return false;
            }
           
        } catch (PDOException $e) {
            $db->rollBack();
            $this->setLastError($e->getMessage());
            error_log("Change Account Error: " . $e->getMessage()); 
            return false;
        }
    }

}
