<?php 

trait BookingDetails{

    public function getBookingByIDAndTourist(int $booking_ID, int $tourist_ID): array|false
    {
        $db = $this->connect();
        
        $sql = "SELECT 
                b.*,
                tp.tourpackage_name,
                tp.tourpackage_desc,
                s.schedule_days,
                nop.numberofpeople_maximum,
                nop.numberofpeople_based,
                p.pricing_currency,
                p.pricing_foradult,
                p.pricing_discount,
                tp.guide_ID
            FROM booking b
            INNER JOIN tour_package tp ON b.tourpackage_ID = tp.tourpackage_ID
            INNER JOIN schedule s ON s.schedule_ID = tp.schedule_ID
            INNER JOIN number_of_people nop ON nop.numberofpeople_ID = s.numberofpeople_ID
            INNER JOIN pricing p ON p.pricing_ID = nop.pricing_ID
            WHERE 
                b.booking_ID = :booking_ID 
                AND b.tourist_ID = :tourist_ID
            LIMIT 1
        ";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':booking_ID', $booking_ID, PDO::PARAM_INT);
            $stmt->bindValue(':tourist_ID', $tourist_ID, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC); // Returns array or false
        } catch (PDOException $e) {
            error_log("getBookingByIDAndTourist Error: " . $e->getMessage());
            return false;
        }
    }

}

?>