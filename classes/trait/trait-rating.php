<?php

trait RatingTrait {
    
    public function createRating($rater_account_role_ID, $rated_type, $rating_value, $rating_description = null, 
                                 $rated_account_role_ID = null, $rated_spot_ID = null, $rated_package_ID = null, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Rating (rater_account_role_ID, rated_type, rating_value, rating_description, 
                                       rated_account_role_ID, rated_spot_ID, rated_package_ID) 
                    VALUES (:rater_account_role_ID, :rated_type, :rating_value, :rating_description, 
                           :rated_account_role_ID, :rated_spot_ID, :rated_package_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':rater_account_role_ID', $rater_account_role_ID);
            $query->bindParam(':rated_type', $rated_type);
            $query->bindParam(':rating_value', $rating_value);
            $query->bindParam(':rating_description', $rating_description);
            $query->bindParam(':rated_account_role_ID', $rated_account_role_ID);
            $query->bindParam(':rated_spot_ID', $rated_spot_ID);
            $query->bindParam(':rated_package_ID', $rated_package_ID);
            
            if ($query->execute()) {
                $rating_ID = $db->lastInsertId();
                
                // Update average rating score if rating a person
                if ($rated_account_role_ID) {
                    $this->updateAverageRating($rated_account_role_ID, $db);
                }
                
                return $rating_ID;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Rating Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    private function updateAverageRating($account_role_ID, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "UPDATE Account_Role 
                    SET role_rating_score = (
                        SELECT AVG(rating_value) 
                        FROM Rating 
                        WHERE rated_account_role_ID = :account_role_ID
                    )
                    WHERE account_role_ID = :account_role_ID";
            $query = $db->prepare($sql);
            $query->bindParam(':account_role_ID', $account_role_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Average Rating Update Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function getRatingsByRatedEntity($rated_type, $rated_ID) {
        $sql = "SELECT r.*, 
                CONCAT(n.name_first, ' ', n.name_last) as rater_name,
                ri.role_name
                FROM Rating r
                LEFT JOIN Account_Role ar ON r.rater_account_role_ID = ar.account_role_ID
                LEFT JOIN User_Login ul ON ar.login_ID = ul.login_ID
                LEFT JOIN Person p ON ul.person_ID = p.person_ID
                LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
                WHERE r.rated_type = :rated_type";
        
        if ($rated_type === 'Tourist' || $rated_type === 'Guide') {
            $sql .= " AND r.rated_account_role_ID = :rated_ID";
        } elseif ($rated_type === 'TouristSpot') {
            $sql .= " AND r.rated_spot_ID = :rated_ID";
        } elseif ($rated_type === 'TouristPackage') {
            $sql .= " AND r.rated_package_ID = :rated_ID";
        }
        
        $sql .= " ORDER BY r.rating_date DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':rated_type', $rated_type);
        $query->bindParam(':rated_ID', $rated_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($rated_type, $rated_ID) {
        $sql = "SELECT AVG(rating_value) as average_rating, COUNT(*) as total_ratings
                FROM Rating
                WHERE rated_type = :rated_type";
        
        if ($rated_type === 'Tourist' || $rated_type === 'Guide') {
            $sql .= " AND rated_account_role_ID = :rated_ID";
        } elseif ($rated_type === 'TouristSpot') {
            $sql .= " AND rated_spot_ID = :rated_ID";
        } elseif ($rated_type === 'TouristPackage') {
            $sql .= " AND rated_package_ID = :rated_ID";
        }
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':rated_type', $rated_type);
        $query->bindParam(':rated_ID', $rated_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function addRatingImage($rating_ID, $image_path, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Review_Images (rating_ID, image_path) VALUES (:rating_ID, :image_path)";
            $query = $db->prepare($sql);
            $query->bindParam(':rating_ID', $rating_ID);
            $query->bindParam(':image_path', $image_path);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Rating Image Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }
}
