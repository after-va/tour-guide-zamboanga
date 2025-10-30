<?php
trait BookingBundleTrait
{
    use CompanionTrait; // Reuse companion logic

    public function addCompanionToBooking($booking_ID, $companion_name, $category_ID, $db){
        $companion_ID = $this->getOrCreateCompanion($companion_name, $category_ID, $db);

        if (!$companion_ID) {
            return false;
        }

        // Step 2: Insert into Booking_Bundle
        $sql = "INSERT INTO Booking_Bundle (booking_ID, companion_ID) 
                VALUES (:booking_id, :companion_id)";
        $query = $db->prepare($sql);
        $query->bindParam('booking_id', $booking_id);
        return $query->execute('companion_id', $companion_id);
    }
}