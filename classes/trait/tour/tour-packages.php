<?php

trait TourPackagesTrait {
    

    public function addTourPackage($guide_ID, $tourpackage_name, $tourpackage_desc, $schedule_days, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount){
        $db = $this->connect();
        $db->beginTransaction();
        try{
            $schedule_ID = $this->addgetSchedule($schedule_days,  $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount,$db);
            if(!$schedule_ID){
                $db->rollBack();
                return false;
            }

            $sql = "INSERT INTO Tour_Package(guide_ID, tourpackage_name, tourpackage_desc, schedule_ID) VALUES(:guide_ID, :tourpackage_name, :tourpackage_desc, :schedule_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':guide_ID',$guide_ID);
            $query->bindParam(':tourpackage_name',$tourpackage_name);
            $query->bindParam(':tourpackage_desc',$tourpackage_desc);
            $query->bindParam(':schedule_ID',$schedule_ID);

            if ($query->execute()){
                $lastInsertId = $db->lastInsertId();
                $db->commit();
                return $lastInsertId;
            } else {   
                $db->rollBack();
                return false;
            }

        }catch (PDOException $e) {
            $db->rollBack();
            error_log("Adding Package Error: " . $e->getMessage()); 
            return false;
        }
    }


    public function viewAllPackages(){
        $sql = "SELECT * FROM Tour_Package";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
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
                $this->linkSpotToPackage($tourpackage_ID, $spots);
            }

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error updating tour package: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTourPackage($tourpackage_ID) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Get current package data to find related records
            $package = $this->getTourPackageById($tourpackage_ID);
            if (!$package) {
                throw new Exception("Package not found");
            }

            // Delete associated spots first
            $sql = "DELETE FROM Tour_Package_Spots WHERE tourpackage_ID = :tourpackage_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            if (!$query->execute()) {
                throw new Exception("Failed to delete package spots");
            }

            // Delete the tour package
            $sql = "DELETE FROM Tour_Package WHERE tourpackage_ID = :tourpackage_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            
            if (!$query->execute()) {
                throw new Exception("Failed to delete tour package");
            }

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error deleting tour package: " . $e->getMessage());
            return false;
        }
    }
}