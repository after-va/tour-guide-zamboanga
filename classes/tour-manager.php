<?php
require_once "database.php";
require_once "trait/trait-tour-package.php";
require_once "trait/trait-tour-spots.php";
require_once "trait/trait-schedule.php";
require_once "trait/trait-rating.php";

class TourManager extends Database {
    use TourPackageTrait, TourSpotsTrait, ScheduleTrait, RatingTrait;

    public function createPackageWithSpots($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, 
                                          $tourPackage_Duration, $spot_IDs = []) {
        $db = $this->connect();
        $db->beginTransaction();

        try {
            // Create package
            $tourPackage_ID = $this->createTourPackage($tourPackage_Name, $tourPackage_Description, 
                                                       $tourPackage_Capacity, $tourPackage_Duration, null, $db);
            
            if (!$tourPackage_ID) {
                $db->rollBack();
                return false;
            }

            // Add spots to package
            if (!empty($spot_IDs)) {
                foreach ($spot_IDs as $index => $spot_ID) {
                    $this->addSpotToPackage($tourPackage_ID, $spot_ID, $index + 1, $db);
                }
            }

            $db->commit();
            return $tourPackage_ID;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Create Package with Spots Error: " . $e->getMessage());
            return false;
        }
    }

    public function getPackageWithDetails($tourPackage_ID) {
        $package = $this->getTourPackageById($tourPackage_ID);
        
        if ($package) {
            $package['spots'] = $this->getPackageSpots($tourPackage_ID);
            $package['schedules'] = $this->getSchedulesByPackage($tourPackage_ID);
            $package['rating'] = $this->getAverageRating('TouristPackage', $tourPackage_ID);
            $package['reviews'] = $this->getRatingsByRatedEntity('TouristPackage', $tourPackage_ID);
        }
        
        return $package;
    }

    public function searchPackages($search_term = null, $category = null) {
        $sql = "SELECT DISTINCT tp.*, 
                (SELECT AVG(rating_value) FROM Rating WHERE rated_package_ID = tp.tourPackage_ID) as avg_rating,
                (SELECT COUNT(*) FROM Rating WHERE rated_package_ID = tp.tourPackage_ID) as total_reviews
                FROM Tour_Package tp
                LEFT JOIN Package_Spots ps ON tp.tourPackage_ID = ps.tourPackage_ID
                LEFT JOIN Tour_Spots ts ON ps.spots_ID = ts.spots_ID
                WHERE 1=1";
        
        if ($search_term) {
            $sql .= " AND (tp.tourPackage_Name LIKE :search 
                      OR tp.tourPackage_Description LIKE :search 
                      OR ts.spots_Name LIKE :search)";
        }
        
        if ($category) {
            $sql .= " AND ts.spots_category = :category";
        }
        
        $sql .= " ORDER BY tp.tourPackage_Name";
        
        $query = $this->connect()->prepare($sql);
        
        if ($search_term) {
            $search = "%{$search_term}%";
            $query->bindParam(':search', $search);
        }
        
        if ($category) {
            $query->bindParam(':category', $category);
        }
        
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopularPackages($limit = 10) {
        $sql = "SELECT tp.*, 
                COUNT(DISTINCT b.booking_ID) as booking_count,
                AVG(r.rating_value) as avg_rating,
                COUNT(DISTINCT r.rating_ID) as total_reviews
                FROM Tour_Package tp
                LEFT JOIN Booking b ON tp.tourPackage_ID = b.tourPackage_ID
                LEFT JOIN Rating r ON tp.tourPackage_ID = r.rated_package_ID
                GROUP BY tp.tourPackage_ID
                ORDER BY booking_count DESC, avg_rating DESC
                LIMIT :limit";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
                
    public function deleteTourPackage($packageId) {
        try {
            $db = $this->connect();
            $db->beginTransaction();
            
            // Check if package has active bookings
            $checkBookings = $db->prepare("SELECT COUNT(*) FROM Booking WHERE tourPackage_ID = :id AND booking_status != 'cancelled'");
            $checkBookings->bindParam(':id', $packageId);
            $checkBookings->execute();
            
            if ($checkBookings->fetchColumn() > 0) {
                $db->rollBack();
                return false; // Cannot delete package with active bookings
            }
            
            // Delete package spots associations
            $deletePackageSpots = $db->prepare("DELETE FROM Package_Spots WHERE tourPackage_ID = :id");
            $deletePackageSpots->bindParam(':id', $packageId);
            $deletePackageSpots->execute();
            
            // Delete package schedules
            $deleteSchedules = $db->prepare("DELETE FROM Schedule WHERE tourPackage_ID = :id");
            $deleteSchedules->bindParam(':id', $packageId);
            $deleteSchedules->execute();
            
            // Delete package ratings
            $deleteRatings = $db->prepare("DELETE FROM Rating WHERE rated_package_ID = :id");
            $deleteRatings->bindParam(':id', $packageId);
            $deleteRatings->execute();
            
            // Delete the package
            $deletePackage = $db->prepare("DELETE FROM Tour_Package WHERE tourPackage_ID = :id");
            $deletePackage->bindParam(':id', $packageId);
            $deletePackage->execute();
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            if ($db) $db->rollBack();
            error_log("Delete Tour Package Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteTourSpot($spotId) {
        try {
            $db = $this->connect();
            $db->beginTransaction();
            
            // Check if spot is used in any package
            $checkPackages = $db->prepare("SELECT COUNT(*) FROM Package_Spots WHERE spots_ID = :id");
            $checkPackages->bindParam(':id', $spotId);
            $checkPackages->execute();
            
            if ($checkPackages->fetchColumn() > 0) {
                $db->rollBack();
                return false; // Cannot delete spot used in packages
            }
            
            // Delete the spot
            $deleteSpot = $db->prepare("DELETE FROM Tour_Spots WHERE spots_ID = :id");
            $deleteSpot->bindParam(':id', $spotId);
            $deleteSpot->execute();
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            if ($db) $db->rollBack();
            error_log("Delete Tour Spot Error: " . $e->getMessage());
            return false;
        }
    }

    public function getSpotWithDetails($spots_ID) {
        $spot = $this->getTourSpotById($spots_ID);
        
        if ($spot) {
            $spot['rating'] = $this->getAverageRating('TouristSpot', $spots_ID);
            $spot['reviews'] = $this->getRatingsByRatedEntity('TouristSpot', $spots_ID);
            
            // Get packages that include this spot
            $sql = "SELECT DISTINCT tp.* 
                    FROM Tour_Package tp
                    INNER JOIN Package_Spots ps ON tp.tourPackage_ID = ps.tourPackage_ID
                    WHERE ps.spots_ID = :spots_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':spots_ID', $spots_ID);
            $query->execute();
            $spot['packages'] = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $spot;
    }
}
