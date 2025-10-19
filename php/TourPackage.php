<?php

require_once "Database.php";

class TourPackage extends Database {
    
    // Create a new tour package with multiple spots
    public function createTourPackage($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $spots_IDs = []) {
        try {
            $conn = $this->connect();
            $conn->beginTransaction();
            
            // Insert tour package
            $sql = "INSERT INTO Tour_Package (tourPackage_Name, tourPackage_Description, tourPackage_Capacity, tourPackage_Duration) 
                    VALUES (:tourPackage_Name, :tourPackage_Description, :tourPackage_Capacity, :tourPackage_Duration)";
            
            $query = $conn->prepare($sql);
            $query->bindParam(":tourPackage_Name", $tourPackage_Name);
            $query->bindParam(":tourPackage_Description", $tourPackage_Description);
            $query->bindParam(":tourPackage_Capacity", $tourPackage_Capacity);
            $query->bindParam(":tourPackage_Duration", $tourPackage_Duration);
            
            if (!$query->execute()) {
                $conn->rollBack();
                return false;
            }
            
            $packageId = $conn->lastInsertId();
            
            // Insert package spots if provided
            if (!empty($spots_IDs) && is_array($spots_IDs)) {
                $spotSql = "INSERT INTO Package_Spots (tourPackage_ID, spots_ID, spot_order) VALUES (:packageId, :spotId, :order)";
                $spotQuery = $conn->prepare($spotSql);
                
                foreach ($spots_IDs as $index => $spotId) {
                    $spotQuery->bindParam(":packageId", $packageId);
                    $spotQuery->bindParam(":spotId", $spotId);
                    $order = $index + 1;
                    $spotQuery->bindParam(":order", $order);
                    
                    if (!$spotQuery->execute()) {
                        $conn->rollBack();
                        return false;
                    }
                }
            }
            
            $conn->commit();
            return $packageId;
        } catch (PDOException $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            error_log("TourPackage Create Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all tour packages with their spots
    public function getAllTourPackages() {
        $sql = "SELECT tp.*
                FROM Tour_Package tp
                ORDER BY tp.tourPackage_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            $packages = $query->fetchAll();
            
            // Get spots for each package
            foreach ($packages as &$package) {
                $package['spots'] = $this->getPackageSpots($package['tourPackage_ID']);
                // For backward compatibility, set first spot name
                $package['spots_Name'] = !empty($package['spots']) ? $package['spots'][0]['spots_Name'] : 'No spots';
            }
            
            return $packages;
        }
        return [];
    }
    
    // Get tour package by ID with all spots
    public function getTourPackageById($tourPackage_ID) {
        $sql = "SELECT tp.*
                FROM Tour_Package tp
                WHERE tp.tourPackage_ID = :tourPackage_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        
        if ($query->execute()) {
            $package = $query->fetch();
            if ($package) {
                $package['spots'] = $this->getPackageSpots($tourPackage_ID);
            }
            return $package;
        }
        return null;
    }
    
    // Update tour package with multiple spots
    public function updateTourPackage($tourPackage_ID, $tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $spots_IDs = []) {
        try {
            $conn = $this->connect();
            $conn->beginTransaction();
            
            // Update tour package
            $sql = "UPDATE Tour_Package 
                    SET tourPackage_Name = :tourPackage_Name, 
                        tourPackage_Description = :tourPackage_Description, 
                        tourPackage_Capacity = :tourPackage_Capacity, 
                        tourPackage_Duration = :tourPackage_Duration
                    WHERE tourPackage_ID = :tourPackage_ID";
            
            $query = $conn->prepare($sql);
            $query->bindParam(":tourPackage_ID", $tourPackage_ID);
            $query->bindParam(":tourPackage_Name", $tourPackage_Name);
            $query->bindParam(":tourPackage_Description", $tourPackage_Description);
            $query->bindParam(":tourPackage_Capacity", $tourPackage_Capacity);
            $query->bindParam(":tourPackage_Duration", $tourPackage_Duration);
            
            if (!$query->execute()) {
                $conn->rollBack();
                return false;
            }
            
            // Delete existing package spots
            $deleteSql = "DELETE FROM Package_Spots WHERE tourPackage_ID = :tourPackage_ID";
            $deleteQuery = $conn->prepare($deleteSql);
            $deleteQuery->bindParam(":tourPackage_ID", $tourPackage_ID);
            $deleteQuery->execute();
            
            // Insert new package spots
            if (!empty($spots_IDs) && is_array($spots_IDs)) {
                $spotSql = "INSERT INTO Package_Spots (tourPackage_ID, spots_ID, spot_order) VALUES (:packageId, :spotId, :order)";
                $spotQuery = $conn->prepare($spotSql);
                
                foreach ($spots_IDs as $index => $spotId) {
                    $spotQuery->bindParam(":packageId", $tourPackage_ID);
                    $spotQuery->bindParam(":spotId", $spotId);
                    $order = $index + 1;
                    $spotQuery->bindParam(":order", $order);
                    
                    if (!$spotQuery->execute()) {
                        $conn->rollBack();
                        return false;
                    }
                }
            }
            
            $conn->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            error_log("TourPackage Update Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete tour package
    public function deleteTourPackage($tourPackage_ID) {
        $sql = "DELETE FROM Tour_Package WHERE tourPackage_ID = :tourPackage_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        return $query->execute();
    }
    
    // Get all spots for a specific package
    public function getPackageSpots($tourPackage_ID) {
        $sql = "SELECT ts.*, ps.spot_order
                FROM Package_Spots ps
                INNER JOIN Tour_Spots ts ON ps.spots_ID = ts.spots_ID
                WHERE ps.tourPackage_ID = :tourPackage_ID
                ORDER BY ps.spot_order ASC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get available schedules for a package
    public function getPackageSchedules($tourPackage_ID) {
        $sql = "SELECT s.*, 
                       CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                       COUNT(b.booking_ID) as total_bookings,
                       SUM(b.booking_PAX) as total_pax
                FROM Schedule s
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID
                WHERE s.tourPackage_ID = :tourPackage_ID
                GROUP BY s.schedule_ID
                ORDER BY s.schedule_StartDateTime ASC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
}
