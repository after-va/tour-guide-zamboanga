<?php

trait BookingTrait {
    
    public function createBooking($customer_ID, $schedule_ID, $tourPackage_ID, $booking_PAX, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $booking_Status = 'pending';
            $sql = "INSERT INTO Booking (customer_ID, schedule_ID, tourPackage_ID, booking_Status, booking_PAX) 
                    VALUES (:customer_ID, :schedule_ID, :tourPackage_ID, :booking_Status, :booking_PAX)";
            $query = $db->prepare($sql);
            $query->bindParam(':customer_ID', $customer_ID);
            $query->bindParam(':schedule_ID', $schedule_ID);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            $query->bindParam(':booking_Status', $booking_Status);
            $query->bindParam(':booking_PAX', $booking_PAX);
            
            if ($query->execute()) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Booking Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function getBookingById($booking_ID) {
        $sql = "SELECT * FROM Booking WHERE booking_ID = :booking_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':booking_ID', $booking_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getBookingsByCustomer($customer_ID) {
        $sql = "SELECT b.*, tp.tourPackage_Name, s.schedule_StartDateTime, s.schedule_EndDateTime 
                FROM Booking b
                LEFT JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Schedule s ON b.schedule_ID = s.schedule_ID
                WHERE b.customer_ID = :customer_ID
                ORDER BY s.schedule_StartDateTime DESC";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':customer_ID', $customer_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBookingStatus($booking_ID, $new_status, $changed_by = null, $change_reason = null) {
        $db = $this->connect();
        $db->beginTransaction();

        try {
            // Get old status
            $old_booking = $this->getBookingById($booking_ID);
            $old_status = $old_booking['booking_Status'];

            // Update booking
            $sql = "UPDATE Booking SET booking_Status = :new_status WHERE booking_ID = :booking_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':new_status', $new_status);
            $query->bindParam(':booking_ID', $booking_ID);
            $query->execute();

            // Log status change
            $sql = "INSERT INTO Booking_Status_History (booking_ID, old_status, new_status, changed_by, change_reason) 
                    VALUES (:booking_ID, :old_status, :new_status, :changed_by, :change_reason)";
            $query = $db->prepare($sql);
            $query->bindParam(':booking_ID', $booking_ID);
            $query->bindParam(':old_status', $old_status);
            $query->bindParam(':new_status', $new_status);
            $query->bindParam(':changed_by', $changed_by);
            $query->bindParam(':change_reason', $change_reason);
            $query->execute();

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Booking Status Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function cancelBooking($booking_ID, $person_ID, $reason) {
        return $this->updateBookingStatus($booking_ID, 'cancelled', $person_ID, $reason);
    }

    public function getBookingDetails($booking_ID) {
        $sql = "SELECT * FROM v_booking_details WHERE booking_ID = :booking_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':booking_ID', $booking_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}
