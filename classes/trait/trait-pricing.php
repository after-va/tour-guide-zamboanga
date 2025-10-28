<?php
trait PricingTrait {
    
    public function updateGuideOffering($offering_ID, $guide_ID, $offering_price, $price_per_person = null, 
                                      $min_pax = 1, $max_pax = null, $is_customizable = 1, $is_active = 1) {
        try {
            $sql = "UPDATE Guide_Package_Offering 
                    SET offering_price = :offering_price,
                        price_per_person = :price_per_person,
                        min_pax = :min_pax,
                        max_pax = :max_pax,
                        is_customizable = :is_customizable,
                        is_active = :is_active
                    WHERE offering_ID = :offering_ID AND guide_ID = :guide_ID";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':offering_ID', $offering_ID);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':offering_price', $offering_price);
            $query->bindParam(':price_per_person', $price_per_person);
            $query->bindParam(':min_pax', $min_pax);
            $query->bindParam(':max_pax', $max_pax);
            $query->bindParam(':is_customizable', $is_customizable);
            $query->bindParam(':is_active', $is_active);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Update Guide Offering Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getGuideOffering($offering_ID) {
        $sql = "SELECT gpo.*, tp.tourPackage_Name, tp.tourPackage_Description, tp.tourPackage_Duration
                FROM Guide_Package_Offering gpo
                INNER JOIN Tour_Package tp ON gpo.tourPackage_ID = tp.tourPackage_ID
                WHERE gpo.offering_ID = :offering_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':offering_ID', $offering_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deleteGuideOffering($offering_ID, $guide_ID) {
        try {
            // Check if offering has any active bookings
            $checkBookings = "SELECT COUNT(*) FROM Booking b
                            INNER JOIN Schedule s ON b.schedule_ID = s.schedule_ID
                            INNER JOIN Guide_Package_Offering gpo ON s.tourPackage_ID = gpo.tourPackage_ID
                            WHERE gpo.offering_ID = :offering_ID 
                            AND b.booking_Status NOT IN ('cancelled', 'completed')";
            
            $query = $this->connect()->prepare($checkBookings);
            $query->bindParam(':offering_ID', $offering_ID);
            $query->execute();
            
            if ($query->fetchColumn() > 0) {
                return false; // Cannot delete offering with active bookings
            }
            
            // Delete the offering
            $sql = "UPDATE Guide_Package_Offering 
                    SET is_active = 0 
                    WHERE offering_ID = :offering_ID AND guide_ID = :guide_ID";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':offering_ID', $offering_ID);
            $query->bindParam(':guide_ID', $guide_ID);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Delete Guide Offering Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function calculateTotalPrice($offering_ID, $total_pax) {
        $offering = $this->getGuideOffering($offering_ID);
        if (!$offering) {
            return false;
        }
        
        $base_price = $offering['offering_price'];
        $price_per_person = $offering['price_per_person'];
        
        if ($price_per_person) {
            return $base_price + ($price_per_person * $total_pax);
        }
        
        return $base_price;
    }
}