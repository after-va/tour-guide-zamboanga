<?php
trait CompanionTrait{
    /**
     * Get existing companion OR create a new one
     * @param string $companion_name
     * @param int $category_ID
     * @param PDO $db
     * @return int|false  companion_ID or false
     */
    public function getOrCreateCompanion($companion_name, $category_ID, $db){
        // 1. Try to find existing companion
        $sql = "SELECT companion_ID 
                FROM companion 
                WHERE companion_name = :companion_name 
                AND companion_category_ID = :category_ID";
        $query = $db->prepare($sql);
        $query->bindParam(':companion_name', $companion_name);
        $query->bindParam(':category_ID', $category_ID, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return (int)$row['companion_ID'];
        }

        // 2. Insert new companion
        $sql = "INSERT INTO companion (companion_name, companion_category_ID) 
                VALUES (:companion_name, :category_ID)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':companion_name', $companion_name);
        $stmt->bindParam(':category_ID', $category_ID, PDO::PARAM_INT);
        $success = $stmt->execute();

        return $success ? $db->lastInsertId() : false;
    }

    public function getCompanionsByBooking($booking_ID) {
        $sql = "SELECT c.companion_name, c.companion_category_ID 
                FROM Booking_Bundle b
                JOIN Companion c ON b.companion_ID = c.companion_ID
                WHERE b.booking_ID = :id";
        $db = $this->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $booking_ID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}