<?php

require_once "Database.php";

class Booking extends Database {
    
    // Create a new booking
    public function createBooking($customer_ID, $schedule_ID, $tourPackage_ID, $booking_PAX, $companions = []) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Insert booking
            $sql = "INSERT INTO Booking (customer_ID, schedule_ID, tourPackage_ID, booking_Status, booking_PAX) 
                    VALUES (:customer_ID, :schedule_ID, :tourPackage_ID, 'Pending', :booking_PAX)";
            
            $query = $db->prepare($sql);
            $query->bindParam(":customer_ID", $customer_ID);
            $query->bindParam(":schedule_ID", $schedule_ID);
            $query->bindParam(":tourPackage_ID", $tourPackage_ID);
            $query->bindParam(":booking_PAX", $booking_PAX);
            
            if ($query->execute()) {
                $booking_ID = $db->lastInsertId();
                
                // Add companions if any
                if (!empty($companions)) {
                    foreach ($companions as $companion_ID) {
                        $sql_companion = "INSERT INTO Booking_Bundle (companion_ID, booking_ID) VALUES (:companion_ID, :booking_ID)";
                        $query_companion = $db->prepare($sql_companion);
                        $query_companion->bindParam(":companion_ID", $companion_ID);
                        $query_companion->bindParam(":booking_ID", $booking_ID);
                        $query_companion->execute();
                    }
                }
                
                $db->commit();
                return $booking_ID;
            } else {
                $db->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Booking Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all bookings
    public function getAllBookings() {
        $sql = "SELECT b.*, 
                       CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
                       CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
                       tp.tourPackage_Name,
                       ts.spots_Name,
                       s.schedule_StartDateTime,
                       s.schedule_EndDateTime
                FROM Booking b
                INNER JOIN Person t ON b.customer_ID = t.person_ID
                INNER JOIN Name_Info tn ON t.name_ID = tn.name_ID
                INNER JOIN Schedule s ON b.schedule_ID = s.schedule_ID
                LEFT JOIN Person g ON s.guide_ID = g.person_ID
                LEFT JOIN Name_Info gn ON g.name_ID = gn.name_ID
                INNER JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                ORDER BY b.booking_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get bookings by customer
    public function getBookingsByCustomer($customer_ID) {
        $sql = "SELECT b.*, 
                       CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
                       tp.tourPackage_Name,
                       ts.spots_Name,
                       s.schedule_StartDateTime,
                       s.schedule_EndDateTime,
                       s.schedule_MeetingSpot,
                       pi.paymentinfo_Amount,
                       pi.paymentinfo_Date
                FROM Booking b
                INNER JOIN Schedule s ON b.schedule_ID = s.schedule_ID
                LEFT JOIN Person g ON s.guide_ID = g.person_ID
                LEFT JOIN Name_Info gn ON g.name_ID = gn.name_ID
                INNER JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                LEFT JOIN Payment_Info pi ON b.booking_ID = pi.booking_ID
                WHERE b.customer_ID = :customer_ID
                ORDER BY b.booking_ID DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":customer_ID", $customer_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get bookings by guide
    public function getBookingsByGuide($guide_ID) {
        $sql = "SELECT b.*, 
                       CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
                       tci.contactinfo_email as tourist_email,
                       tph.phone_number as tourist_phone,
                       tp.tourPackage_Name,
                       ts.spots_Name,
                       s.schedule_StartDateTime,
                       s.schedule_EndDateTime,
                       s.schedule_MeetingSpot
                FROM Booking b
                INNER JOIN Person t ON b.customer_ID = t.person_ID
                INNER JOIN Name_Info tn ON t.name_ID = tn.name_ID
                INNER JOIN Contact_Info tci ON t.contactinfo_ID = tci.contactinfo_ID
                LEFT JOIN Phone_Number tph ON tci.phone_ID = tph.phone_ID
                INNER JOIN Schedule s ON b.schedule_ID = s.schedule_ID
                INNER JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                WHERE s.guide_ID = :guide_ID
                ORDER BY s.schedule_StartDateTime ASC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":guide_ID", $guide_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get booking by ID
    public function getBookingById($booking_ID) {
        $sql = "SELECT b.*, 
                       CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
                       CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
                       tp.tourPackage_Name,
                       tp.tourPackage_Description,
                       ts.spots_Name,
                       s.schedule_StartDateTime,
                       s.schedule_EndDateTime,
                       s.schedule_MeetingSpot,
                       pi.paymentinfo_Amount,
                       pi.paymentinfo_Date
                FROM Booking b
                INNER JOIN Person t ON b.customer_ID = t.person_ID
                INNER JOIN Name_Info tn ON t.name_ID = tn.name_ID
                INNER JOIN Schedule s ON b.schedule_ID = s.schedule_ID
                LEFT JOIN Person g ON s.guide_ID = g.person_ID
                LEFT JOIN Name_Info gn ON g.name_ID = gn.name_ID
                INNER JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                LEFT JOIN Payment_Info pi ON b.booking_ID = pi.booking_ID
                WHERE b.booking_ID = :booking_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":booking_ID", $booking_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Update booking status
    public function updateBookingStatus($booking_ID, $new_status, $changed_by, $reason = null) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Get old status
            $sql_old = "SELECT booking_Status FROM Booking WHERE booking_ID = :booking_ID";
            $query_old = $db->prepare($sql_old);
            $query_old->bindParam(":booking_ID", $booking_ID);
            $query_old->execute();
            $old_status = $query_old->fetchColumn();
            
            // Update booking
            $sql = "UPDATE Booking SET booking_Status = :new_status WHERE booking_ID = :booking_ID";
            $query = $db->prepare($sql);
            $query->bindParam(":new_status", $new_status);
            $query->bindParam(":booking_ID", $booking_ID);
            
            if ($query->execute()) {
                // Log status change
                $sql_history = "INSERT INTO Booking_Status_History (booking_ID, old_status, new_status, changed_by, change_reason) 
                                VALUES (:booking_ID, :old_status, :new_status, :changed_by, :change_reason)";
                $query_history = $db->prepare($sql_history);
                $query_history->bindParam(":booking_ID", $booking_ID);
                $query_history->bindParam(":old_status", $old_status);
                $query_history->bindParam(":new_status", $new_status);
                $query_history->bindParam(":changed_by", $changed_by);
                $query_history->bindParam(":change_reason", $reason);
                $query_history->execute();
                
                $db->commit();
                return true;
            } else {
                $db->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Update Booking Status Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Cancel booking
    public function cancelBooking($booking_ID, $cancelled_by, $reason) {
        return $this->updateBookingStatus($booking_ID, 'Cancelled', $cancelled_by, $reason);
    }
    
    // Confirm booking
    public function confirmBooking($booking_ID, $confirmed_by) {
        return $this->updateBookingStatus($booking_ID, 'Confirmed', $confirmed_by, 'Booking confirmed');
    }
}
