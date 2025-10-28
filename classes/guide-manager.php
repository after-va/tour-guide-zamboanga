<?php
require_once "database.php";
require_once "trait/trait-schedule.php";
require_once "trait/trait-rating.php";
require_once "trait/trait-pricing.php";

class GuideManager extends Database {
    use ScheduleTrait, RatingTrait, PricingTrait;

    public function getGuideOfferings($guide_ID) {
        $sql = "SELECT tp.*, tpo.*, 
                tpo.offering_price as guide_price,
                tpo.price_per_person as guide_price_per_person,
                tpo.min_pax as guide_min_pax
                FROM Tour_Package tp
                LEFT JOIN Tour_Package_Offering tpo ON tp.tourPackage_ID = tpo.tourPackage_ID 
                AND tpo.guide_ID = :guide_ID
                ORDER BY tp.tourPackage_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGuideProfile($person_ID) {
        $sql = "SELECT p.*, n.*, ci.*, ar.role_rating_score,
                CONCAT(n.name_first, ' ', n.name_last) as full_name
                FROM Person p
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
                LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
                LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                WHERE p.person_ID = :person_ID AND ar.role_ID = 2";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':person_ID', $person_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllGuides() {
        $sql = "SELECT p.person_ID, 
                CONCAT(n.name_first, ' ', n.name_last) as full_name,
                ar.role_rating_score,
                COUNT(DISTINCT s.schedule_ID) as total_tours,
                COUNT(DISTINCT b.booking_ID) as total_bookings
                FROM Person p
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                INNER JOIN User_Login ul ON p.person_ID = ul.person_ID
                INNER JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                LEFT JOIN Schedule s ON p.person_ID = s.guide_ID
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID
                WHERE ar.role_ID = 2
                GROUP BY p.person_ID
                ORDER BY ar.role_rating_score DESC, full_name";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGuideAvailability($guide_ID, $date = null) {
        $sql = "SELECT * FROM Guide_Availability WHERE guide_ID = :guide_ID";
        
        if ($date) {
            $sql .= " AND available_date = :date";
        } else {
            $sql .= " AND available_date >= CURDATE()";
        }
        
        $sql .= " ORDER BY available_date, start_time";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        if ($date) {
            $query->bindParam(':date', $date);
        }
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setGuideAvailability($guide_ID, $available_date, $start_time, $end_time, $is_available = 1, $notes = null) {
        try {
            $sql = "INSERT INTO Guide_Availability (guide_ID, available_date, start_time, end_time, is_available, notes) 
                    VALUES (:guide_ID, :available_date, :start_time, :end_time, :is_available, :notes)
                    ON DUPLICATE KEY UPDATE 
                    start_time = :start_time, 
                    end_time = :end_time, 
                    is_available = :is_available, 
                    notes = :notes";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':available_date', $available_date);
            $query->bindParam(':start_time', $start_time);
            $query->bindParam(':end_time', $end_time);
            $query->bindParam(':is_available', $is_available);
            $query->bindParam(':notes', $notes);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Set Guide Availability Error: " . $e->getMessage());
            return false;
        }
    }

    public function getGuidePackageOfferings($guide_ID) {
        $sql = "SELECT gpo.*, tp.tourPackage_Name, tp.tourPackage_Description, tp.tourPackage_Duration
                FROM Guide_Package_Offering gpo
                INNER JOIN Tour_Package tp ON gpo.tourPackage_ID = tp.tourPackage_ID
                WHERE gpo.guide_ID = :guide_ID AND gpo.is_active = 1
                ORDER BY tp.tourPackage_Name";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getGuidePackages($guide_ID) {
        $sql = "SELECT DISTINCT tp.*, 
                CASE WHEN gpa.adoption_ID IS NOT NULL THEN 1 ELSE 0 END as is_adopted
                FROM Tour_Package tp
                LEFT JOIN Guide_Package_Adoption gpa ON tp.tourPackage_ID = gpa.tourPackage_ID AND gpa.guide_ID = :guide_ID AND gpa.is_active = 1
                LEFT JOIN Schedule s ON tp.tourPackage_ID = s.tourPackage_ID AND s.guide_ID = :guide_ID
                WHERE (s.guide_ID = :guide_ID OR gpa.guide_ID = :guide_ID)
                ORDER BY tp.tourPackage_Name";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRecommendedPackagesForGuide($guide_ID) {
        $sql = "SELECT tp.*, pr.recommendation_date
                FROM Tour_Package tp
                INNER JOIN Package_Recommendations pr ON tp.tourPackage_ID = pr.tourPackage_ID
                LEFT JOIN Guide_Package_Adoption gpa ON tp.tourPackage_ID = gpa.tourPackage_ID AND gpa.guide_ID = :guide_ID
                WHERE pr.is_recommended = 1 AND (gpa.guide_ID IS NULL OR gpa.is_active = 0)
                ORDER BY pr.recommendation_date DESC";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function adoptPackage($guide_ID, $tourPackage_ID) {
        try {
            $sql = "INSERT INTO Guide_Package_Adoption (guide_ID, tourPackage_ID, is_active) 
                    VALUES (:guide_ID, :tourPackage_ID, 1)
                    ON DUPLICATE KEY UPDATE is_active = 1";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Adopt Package Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function removePackage($guide_ID, $tourPackage_ID) {
        try {
            $sql = "UPDATE Guide_Package_Adoption 
                    SET is_active = 0 
                    WHERE guide_ID = :guide_ID AND tourPackage_ID = :tourPackage_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Remove Package Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getSchedulesByPackageAndGuide($package_ID, $guide_ID) {
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM Booking b WHERE b.schedule_ID = s.schedule_ID AND b.booking_status != 'cancelled') as booked_count
                FROM Schedule s
                WHERE s.tourPackage_ID = :package_ID AND s.guide_ID = :guide_ID
                ORDER BY s.schedule_date, s.start_time";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':package_ID', $package_ID);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createOrUpdateSchedule($package_ID, $guide_ID, $schedule_date, $start_time, $end_time, $max_tourists, $notes = null) {
        try {
            $sql = "INSERT INTO Schedule (tourPackage_ID, guide_ID, schedule_date, start_time, end_time, max_tourists, schedule_notes)
                    VALUES (:package_ID, :guide_ID, :schedule_date, :start_time, :end_time, :max_tourists, :notes)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':package_ID', $package_ID);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':schedule_date', $schedule_date);
            $query->bindParam(':start_time', $start_time);
            $query->bindParam(':end_time', $end_time);
            $query->bindParam(':max_tourists', $max_tourists);
            $query->bindParam(':notes', $notes);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Create/Update Schedule Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteSchedule($schedule_ID, $guide_ID) {
        try {
            // Check if schedule has bookings
            $checkBookings = $this->connect()->prepare("SELECT COUNT(*) FROM Booking WHERE schedule_ID = :id");
            $checkBookings->bindParam(':id', $schedule_ID);
            $checkBookings->execute();
            
            if ($checkBookings->fetchColumn() > 0) {
                return false; // Cannot delete schedule with bookings
            }
            
            // Check if guide owns this schedule
            $checkOwnership = $this->connect()->prepare("SELECT COUNT(*) FROM Schedule WHERE schedule_ID = :id AND guide_ID = :guide_ID");
            $checkOwnership->bindParam(':id', $schedule_ID);
            $checkOwnership->bindParam(':guide_ID', $guide_ID);
            $checkOwnership->execute();
            
            if ($checkOwnership->fetchColumn() == 0) {
                return false; // Guide does not own this schedule
            }
            
            // Delete the schedule
            $deleteSchedule = $this->connect()->prepare("DELETE FROM Schedule WHERE schedule_ID = :id AND guide_ID = :guide_ID");
            $deleteSchedule->bindParam(':id', $schedule_ID);
            $deleteSchedule->bindParam(':guide_ID', $guide_ID);
            return $deleteSchedule->execute();
        } catch (PDOException $e) {
            error_log("Delete Schedule Error: " . $e->getMessage());
            return false;
        }
    }

    public function createGuideOffering($guide_ID, $tourPackage_ID, $offering_price, $price_per_person = null, 
                                       $min_pax = 1, $max_pax = null, $is_customizable = 1) {
        try {
            $sql = "INSERT INTO Guide_Package_Offering 
                    (guide_ID, tourPackage_ID, offering_price, price_per_person, min_pax, max_pax, is_customizable) 
                    VALUES (:guide_ID, :tourPackage_ID, :offering_price, :price_per_person, :min_pax, :max_pax, :is_customizable)";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':guide_ID', $guide_ID);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            $query->bindParam(':offering_price', $offering_price);
            $query->bindParam(':price_per_person', $price_per_person);
            $query->bindParam(':min_pax', $min_pax);
            $query->bindParam(':max_pax', $max_pax);
            $query->bindParam(':is_customizable', $is_customizable);
            
            if ($query->execute()) {
                return $this->connect()->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Create Guide Offering Error: " . $e->getMessage());
            return false;
        }
    }

    public function getGuideStats($guide_ID) {
        $sql = "SELECT 
                COUNT(DISTINCT s.schedule_ID) as total_schedules,
                COUNT(DISTINCT b.booking_ID) as total_bookings,
                COUNT(DISTINCT CASE WHEN b.booking_Status = 'completed' THEN b.booking_ID END) as completed_bookings,
                AVG(CASE WHEN r.rated_type = 'Guide' THEN r.rating_value END) as avg_rating,
                COUNT(DISTINCT r.rating_ID) as total_reviews
                FROM Person p
                LEFT JOIN Schedule s ON p.person_ID = s.guide_ID
                LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID
                LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
                LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                LEFT JOIN Rating r ON ar.account_role_ID = r.rated_account_role_ID
                WHERE p.person_ID = :guide_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':guide_ID', $guide_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}
