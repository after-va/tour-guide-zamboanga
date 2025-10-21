<?php

require_once "Database.php";

class TourPackage extends Database {
    
    // Create a new tour package with detailed itinerary
    public function createTourPackage($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $tourPackage_TotalDays, $itinerary_items = []) {
        try {
            $conn = $this->connect();
            $conn->beginTransaction();
            
            // Insert tour package
            $sql = "INSERT INTO Tour_Package (tourPackage_Name, tourPackage_Description, tourPackage_Capacity, tourPackage_Duration, tourPackage_TotalDays) 
                    VALUES (:tourPackage_Name, :tourPackage_Description, :tourPackage_Capacity, :tourPackage_Duration, :tourPackage_TotalDays)";
            
            $query = $conn->prepare($sql);
            $query->bindParam(":tourPackage_Name", $tourPackage_Name);
            $query->bindParam(":tourPackage_Description", $tourPackage_Description);
            $query->bindParam(":tourPackage_Capacity", $tourPackage_Capacity);
            $query->bindParam(":tourPackage_Duration", $tourPackage_Duration);
            $query->bindParam(":tourPackage_TotalDays", $tourPackage_TotalDays);
            
            if (!$query->execute()) {
                $conn->rollBack();
                return false;
            }
            
            $packageId = $conn->lastInsertId();
            
            // Insert itinerary items if provided
            if (!empty($itinerary_items) && is_array($itinerary_items)) {
                $itinerarySql = "INSERT INTO Tour_Package_Itinerary 
                                (tourPackage_ID, spots_ID, day_number, sequence_order, start_time, end_time, activity_description, notes) 
                                VALUES (:packageId, :spotId, :dayNumber, :sequenceOrder, :startTime, :endTime, :activityDescription, :notes)";
                $itineraryQuery = $conn->prepare($itinerarySql);
                
                foreach ($itinerary_items as $item) {
                    $itineraryQuery->bindParam(":packageId", $packageId);
                    // Allow NULL for spots_ID (for break times like Lunch, Break, Sleep)
                    $spotId = !empty($item['spots_ID']) ? $item['spots_ID'] : null;
                    $itineraryQuery->bindParam(":spotId", $spotId, PDO::PARAM_INT);
                    $itineraryQuery->bindParam(":dayNumber", $item['day_number']);
                    $itineraryQuery->bindParam(":sequenceOrder", $item['sequence_order']);
                    $itineraryQuery->bindParam(":startTime", $item['start_time']);
                    $itineraryQuery->bindParam(":endTime", $item['end_time']);
                    $itineraryQuery->bindParam(":activityDescription", $item['activity_description']);
                    $itineraryQuery->bindParam(":notes", $item['notes']);
                    
                    if (!$itineraryQuery->execute()) {
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
    
    // Get all tour packages with their itinerary
    public function getAllTourPackages() {
        $sql = "SELECT tp.*
                FROM Tour_Package tp
                ORDER BY tp.tourPackage_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            $packages = $query->fetchAll();
            
            // Get itinerary for each package
            foreach ($packages as &$package) {
                $package['itinerary'] = $this->getPackageItinerary($package['tourPackage_ID']);
                $package['total_spots'] = count($package['itinerary']);
            }
            
            return $packages;
        }
        return [];
    }
    
    // Get tour package by ID with full itinerary
    public function getTourPackageById($tourPackage_ID) {
        $sql = "SELECT tp.*
                FROM Tour_Package tp
                WHERE tp.tourPackage_ID = :tourPackage_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        
        if ($query->execute()) {
            $package = $query->fetch();
            if ($package) {
                $package['itinerary'] = $this->getPackageItinerary($tourPackage_ID);
            }
            return $package;
        }
        return null;
    }
    
    // Update tour package with detailed itinerary
    public function updateTourPackage($tourPackage_ID, $tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $tourPackage_TotalDays, $itinerary_items = []) {
        try {
            $conn = $this->connect();
            $conn->beginTransaction();
            
            // Update tour package
            $sql = "UPDATE Tour_Package 
                    SET tourPackage_Name = :tourPackage_Name, 
                        tourPackage_Description = :tourPackage_Description, 
                        tourPackage_Capacity = :tourPackage_Capacity, 
                        tourPackage_Duration = :tourPackage_Duration,
                        tourPackage_TotalDays = :tourPackage_TotalDays
                    WHERE tourPackage_ID = :tourPackage_ID";
            
            $query = $conn->prepare($sql);
            $query->bindParam(":tourPackage_ID", $tourPackage_ID);
            $query->bindParam(":tourPackage_Name", $tourPackage_Name);
            $query->bindParam(":tourPackage_Description", $tourPackage_Description);
            $query->bindParam(":tourPackage_Capacity", $tourPackage_Capacity);
            $query->bindParam(":tourPackage_Duration", $tourPackage_Duration);
            $query->bindParam(":tourPackage_TotalDays", $tourPackage_TotalDays);
            
            if (!$query->execute()) {
                $conn->rollBack();
                return false;
            }
            
            // Delete existing itinerary items
            $deleteSql = "DELETE FROM Tour_Package_Itinerary WHERE tourPackage_ID = :tourPackage_ID";
            $deleteQuery = $conn->prepare($deleteSql);
            $deleteQuery->bindParam(":tourPackage_ID", $tourPackage_ID);
            $deleteQuery->execute();
            
            // Insert new itinerary items
            if (!empty($itinerary_items) && is_array($itinerary_items)) {
                $itinerarySql = "INSERT INTO Tour_Package_Itinerary 
                                (tourPackage_ID, spots_ID, day_number, sequence_order, start_time, end_time, activity_description, notes) 
                                VALUES (:packageId, :spotId, :dayNumber, :sequenceOrder, :startTime, :endTime, :activityDescription, :notes)";
                $itineraryQuery = $conn->prepare($itinerarySql);
                
                foreach ($itinerary_items as $item) {
                    $itineraryQuery->bindParam(":packageId", $tourPackage_ID);
                    // Allow NULL for spots_ID (for break times like Lunch, Break, Sleep)
                    $spotId = !empty($item['spots_ID']) ? $item['spots_ID'] : null;
                    $itineraryQuery->bindParam(":spotId", $spotId, PDO::PARAM_INT);
                    $itineraryQuery->bindParam(":dayNumber", $item['day_number']);
                    $itineraryQuery->bindParam(":sequenceOrder", $item['sequence_order']);
                    $itineraryQuery->bindParam(":startTime", $item['start_time']);
                    $itineraryQuery->bindParam(":endTime", $item['end_time']);
                    $itineraryQuery->bindParam(":activityDescription", $item['activity_description']);
                    $itineraryQuery->bindParam(":notes", $item['notes']);
                    
                    if (!$itineraryQuery->execute()) {
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
    
    // Get full itinerary for a specific package
    public function getPackageItinerary($tourPackage_ID) {
        $sql = "SELECT tpi.*, ts.spots_Name, ts.spots_Description, ts.spots_category, ts.spots_Address, ts.spots_GoogleLink,
                       TIME_FORMAT(tpi.start_time, '%h:%i %p') as start_time_formatted,
                       TIME_FORMAT(tpi.end_time, '%h:%i %p') as end_time_formatted
                FROM Tour_Package_Itinerary tpi
                INNER JOIN Tour_Spots ts ON tpi.spots_ID = ts.spots_ID
                WHERE tpi.tourPackage_ID = :tourPackage_ID
                ORDER BY tpi.day_number ASC, tpi.sequence_order ASC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get itinerary grouped by day
    public function getPackageItineraryByDay($tourPackage_ID) {
        $itinerary = $this->getPackageItinerary($tourPackage_ID);
        $grouped = [];
        
        foreach ($itinerary as $item) {
            $day = $item['day_number'];
            if (!isset($grouped[$day])) {
                $grouped[$day] = [];
            }
            $grouped[$day][] = $item;
        }
        
        return $grouped;
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
