<?php

require_once "Database.php";

class Schedule extends Database {
    
    // Create a new schedule
    public function createSchedule($tourPackage_ID, $guide_ID, $schedule_StartDateTime, $schedule_EndDateTime, $schedule_Capacity, $schedule_MeetingSpot) {
        $sql = "INSERT INTO Schedule (tourPackage_ID, guide_ID, schedule_StartDateTime, schedule_EndDateTime, schedule_Capacity, schedule_MeetingSpot) 
                VALUES (:tourPackage_ID, :guide_ID, :schedule_StartDateTime, :schedule_EndDateTime, :schedule_Capacity, :schedule_MeetingSpot)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        $query->bindParam(":guide_ID", $guide_ID);
        $query->bindParam(":schedule_StartDateTime", $schedule_StartDateTime);
        $query->bindParam(":schedule_EndDateTime", $schedule_EndDateTime);
        $query->bindParam(":schedule_Capacity", $schedule_Capacity);
        $query->bindParam(":schedule_MeetingSpot", $schedule_MeetingSpot);
        
        if ($query->execute()) {
            return $this->connect()->lastInsertId();
        }
        return false;
    }
    
    // Get all schedules
    public function getAllSchedules() {
        $sql = "SELECT s.*, 
                       tp.tourPackage_Name,
                       ts.spots_Name,
                       CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                       COUNT(b.booking_ID) as total_bookings,
                       SUM(b.booking_PAX) as total_pax
                FROM Schedule s
                INNER JOIN Tour_Package tp ON s.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID
                GROUP BY s.schedule_ID
                ORDER BY s.schedule_StartDateTime DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get schedule by ID
    public function getScheduleById($schedule_ID) {
        $sql = "SELECT s.*, 
                       tp.tourPackage_Name,
                       tp.tourPackage_Description,
                       ts.spots_Name,
                       ts.spots_Address,
                       CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                       COUNT(b.booking_ID) as total_bookings,
                       SUM(b.booking_PAX) as total_pax
                FROM Schedule s
                INNER JOIN Tour_Package tp ON s.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID
                WHERE s.schedule_ID = :schedule_ID
                GROUP BY s.schedule_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":schedule_ID", $schedule_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Get available schedules (future schedules with available slots)
    public function getAvailableSchedules() {
        $sql = "SELECT s.*, 
                       tp.tourPackage_Name,
                       tp.tourPackage_Description,
                       ts.spots_Name,
                       ts.spots_Address,
                       CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                       COUNT(b.booking_ID) as total_bookings,
                       COALESCE(SUM(b.booking_PAX), 0) as total_pax,
                       (s.schedule_Capacity - COALESCE(SUM(b.booking_PAX), 0)) as available_slots
                FROM Schedule s
                INNER JOIN Tour_Package tp ON s.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID AND b.booking_Status != 'Cancelled'
                WHERE s.schedule_StartDateTime > NOW()
                GROUP BY s.schedule_ID
                HAVING available_slots > 0
                ORDER BY s.schedule_StartDateTime ASC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Update schedule
    public function updateSchedule($schedule_ID, $tourPackage_ID, $guide_ID, $schedule_StartDateTime, $schedule_EndDateTime, $schedule_Capacity, $schedule_MeetingSpot) {
        $sql = "UPDATE Schedule 
                SET tourPackage_ID = :tourPackage_ID, 
                    guide_ID = :guide_ID, 
                    schedule_StartDateTime = :schedule_StartDateTime, 
                    schedule_EndDateTime = :schedule_EndDateTime, 
                    schedule_Capacity = :schedule_Capacity, 
                    schedule_MeetingSpot = :schedule_MeetingSpot 
                WHERE schedule_ID = :schedule_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":schedule_ID", $schedule_ID);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        $query->bindParam(":guide_ID", $guide_ID);
        $query->bindParam(":schedule_StartDateTime", $schedule_StartDateTime);
        $query->bindParam(":schedule_EndDateTime", $schedule_EndDateTime);
        $query->bindParam(":schedule_Capacity", $schedule_Capacity);
        $query->bindParam(":schedule_MeetingSpot", $schedule_MeetingSpot);
        
        return $query->execute();
    }
    
    // Delete schedule
    public function deleteSchedule($schedule_ID) {
        $sql = "DELETE FROM Schedule WHERE schedule_ID = :schedule_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":schedule_ID", $schedule_ID);
        return $query->execute();
    }
    
    // Check if schedule has capacity for booking
    public function hasCapacity($schedule_ID, $requested_pax) {
        $sql = "SELECT s.schedule_Capacity, COALESCE(SUM(b.booking_PAX), 0) as total_pax
                FROM Schedule s
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID AND b.booking_Status != 'Cancelled'
                WHERE s.schedule_ID = :schedule_ID
                GROUP BY s.schedule_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":schedule_ID", $schedule_ID);
        
        if ($query->execute()) {
            $result = $query->fetch();
            $available = $result['schedule_Capacity'] - $result['total_pax'];
            return $available >= $requested_pax;
        }
        return false;
    }
}
