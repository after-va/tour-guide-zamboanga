<?php

trait ScheduleTrait {
    
    public function createSchedule($tourPackage_ID, $guide_ID, $schedule_StartDateTime, $schedule_EndDateTime, $schedule_Capacity, $schedule_MeetingSpot, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Schedule (tourPackage_ID, guide_ID, schedule_StartDateTime, schedule_EndDateTime, schedule_Capacity, schedule_MeetingSpot) 
                    VALUES (:tourPackage_ID, :guide_ID, :schedule_StartDateTime, :schedule_EndDateTime, :schedule_Capacity, :schedule_MeetingSpot)";
            $query = $db->prepare($sql);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':schedule_StartDateTime', $schedule_StartDateTime);
            $query->bindParam(':schedule_EndDateTime', $schedule_EndDateTime);
            $query->bindParam(':schedule_Capacity', $schedule_Capacity);
            $query->bindParam(':schedule_MeetingSpot', $schedule_MeetingSpot);
            
            if ($query->execute()) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Schedule Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function getScheduleById($schedule_ID) {
        $sql = "SELECT s.*, tp.tourPackage_Name, tp.tourPackage_Description, 
                CONCAT(n.name_first, ' ', n.name_last) as guide_name
                FROM Schedule s
                LEFT JOIN Tour_Package tp ON s.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                WHERE s.schedule_ID = :schedule_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':schedule_ID', $schedule_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getSchedulesByPackage($tourPackage_ID) {
        $sql = "SELECT s.*, 
                CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                (SELECT COUNT(*) FROM Booking WHERE schedule_ID = s.schedule_ID AND booking_Status != 'cancelled') as booked_count
                FROM Schedule s
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                WHERE s.tourPackage_ID = :tourPackage_ID
                ORDER BY s.schedule_StartDateTime";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':tourPackage_ID', $tourPackage_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchedulesByGuide($guide_ID) {
        $sql = "SELECT s.*, tp.tourPackage_Name, tp.tourPackage_Description,
                (SELECT COUNT(*) FROM Booking WHERE schedule_ID = s.schedule_ID AND booking_Status != 'cancelled') as booked_count
                FROM Schedule s
                LEFT JOIN Tour_Package tp ON s.tourPackage_ID = tp.tourPackage_ID
                WHERE s.guide_ID = :guide_ID
                ORDER BY s.schedule_StartDateTime";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableSchedules($tourPackage_ID = null) {
        $sql = "SELECT s.*, tp.tourPackage_Name, tp.tourPackage_Description,
                CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                (SELECT COUNT(*) FROM Booking WHERE schedule_ID = s.schedule_ID AND booking_Status != 'cancelled') as booked_count,
                (s.schedule_Capacity - (SELECT COALESCE(SUM(booking_PAX), 0) FROM Booking WHERE schedule_ID = s.schedule_ID AND booking_Status != 'cancelled')) as available_slots
                FROM Schedule s
                LEFT JOIN Tour_Package tp ON s.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Person p ON s.guide_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                WHERE s.schedule_StartDateTime > NOW()";
        
        if ($tourPackage_ID !== null) {
            $sql .= " AND s.tourPackage_ID = :tourPackage_ID";
        }
        
        $sql .= " HAVING available_slots > 0 ORDER BY s.schedule_StartDateTime";
        
        $query = $this->connect()->prepare($sql);
        if ($tourPackage_ID !== null) {
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
        }
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSchedule($schedule_ID, $schedule_StartDateTime, $schedule_EndDateTime, $schedule_Capacity, $schedule_MeetingSpot) {
        try {
            $sql = "UPDATE Schedule 
                    SET schedule_StartDateTime = :schedule_StartDateTime, 
                        schedule_EndDateTime = :schedule_EndDateTime, 
                        schedule_Capacity = :schedule_Capacity, 
                        schedule_MeetingSpot = :schedule_MeetingSpot 
                    WHERE schedule_ID = :schedule_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':schedule_ID', $schedule_ID);
            $query->bindParam(':schedule_StartDateTime', $schedule_StartDateTime);
            $query->bindParam(':schedule_EndDateTime', $schedule_EndDateTime);
            $query->bindParam(':schedule_Capacity', $schedule_Capacity);
            $query->bindParam(':schedule_MeetingSpot', $schedule_MeetingSpot);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Schedule Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteSchedule($schedule_ID) {
        try {
            $sql = "DELETE FROM Schedule WHERE schedule_ID = :schedule_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':schedule_ID', $schedule_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Schedule Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
