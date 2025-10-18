<?php

require_once "Database.php";

class TourPackage extends Database {
    
    // Create a new tour package
    public function createTourPackage($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $spots_ID) {
        $sql = "INSERT INTO Tour_Package (tourPackage_Name, tourPackage_Description, tourPackage_Capacity, tourPackage_Duration, spots_ID) 
                VALUES (:tourPackage_Name, :tourPackage_Description, :tourPackage_Capacity, :tourPackage_Duration, :spots_ID)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_Name", $tourPackage_Name);
        $query->bindParam(":tourPackage_Description", $tourPackage_Description);
        $query->bindParam(":tourPackage_Capacity", $tourPackage_Capacity);
        $query->bindParam(":tourPackage_Duration", $tourPackage_Duration);
        $query->bindParam(":spots_ID", $spots_ID);
        
        if ($query->execute()) {
            return $this->connect()->lastInsertId();
        }
        return false;
    }
    
    // Get all tour packages
    public function getAllTourPackages() {
        $sql = "SELECT tp.*, ts.spots_Name, ts.spots_Description, ts.spots_Address, ts.spots_GoogleLink
                FROM Tour_Package tp
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                ORDER BY tp.tourPackage_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get tour package by ID
    public function getTourPackageById($tourPackage_ID) {
        $sql = "SELECT tp.*, ts.spots_Name, ts.spots_Description, ts.spots_Address, ts.spots_GoogleLink, ts.spots_category
                FROM Tour_Package tp
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                WHERE tp.tourPackage_ID = :tourPackage_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Update tour package
    public function updateTourPackage($tourPackage_ID, $tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $spots_ID) {
        $sql = "UPDATE Tour_Package 
                SET tourPackage_Name = :tourPackage_Name, 
                    tourPackage_Description = :tourPackage_Description, 
                    tourPackage_Capacity = :tourPackage_Capacity, 
                    tourPackage_Duration = :tourPackage_Duration, 
                    spots_ID = :spots_ID 
                WHERE tourPackage_ID = :tourPackage_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        $query->bindParam(":tourPackage_Name", $tourPackage_Name);
        $query->bindParam(":tourPackage_Description", $tourPackage_Description);
        $query->bindParam(":tourPackage_Capacity", $tourPackage_Capacity);
        $query->bindParam(":tourPackage_Duration", $tourPackage_Duration);
        $query->bindParam(":spots_ID", $spots_ID);
        
        return $query->execute();
    }
    
    // Delete tour package
    public function deleteTourPackage($tourPackage_ID) {
        $sql = "DELETE FROM Tour_Package WHERE tourPackage_ID = :tourPackage_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        return $query->execute();
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
