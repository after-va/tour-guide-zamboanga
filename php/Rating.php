<?php

require_once "Database.php";

class Rating extends Database {
    
    // Add a rating
    public function addRating($rater_ID, $rated_ID, $rating_value, $rating_description) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Insert rating
            $sql = "INSERT INTO Rating (rater_ID, rated_ID, rating_value, rating_description) 
                    VALUES (:rater_ID, :rated_ID, :rating_value, :rating_description)";
            
            $query = $db->prepare($sql);
            $query->bindParam(":rater_ID", $rater_ID);
            $query->bindParam(":rated_ID", $rated_ID);
            $query->bindParam(":rating_value", $rating_value);
            $query->bindParam(":rating_description", $rating_description);
            
            if ($query->execute()) {
                // Update person's rating score
                $this->updatePersonRatingScore($rated_ID, $db);
                $db->commit();
                return true;
            } else {
                $db->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Rating Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Update person's average rating score
    private function updatePersonRatingScore($person_ID, $db) {
        $sql = "UPDATE Person 
                SET person_RatingScore = (
                    SELECT AVG(rating_value) 
                    FROM Rating 
                    WHERE rated_ID = :person_ID
                ) 
                WHERE person_ID = :person_ID";
        
        $query = $db->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        return $query->execute();
    }
    
    // Get ratings for a person
    public function getRatingsByPerson($person_ID) {
        $sql = "SELECT r.*, 
                       CONCAT(n.name_first, ' ', n.name_last) as rater_name,
                       p.role_ID as rater_role
                FROM Rating r
                INNER JOIN Person p ON r.rater_ID = p.person_ID
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                WHERE r.rated_ID = :person_ID
                ORDER BY r.rating_date DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get average rating for a person
    public function getAverageRating($person_ID) {
        $sql = "SELECT AVG(rating_value) as average_rating, COUNT(*) as total_ratings
                FROM Rating
                WHERE rated_ID = :person_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Check if user has already rated someone
    public function hasRated($rater_ID, $rated_ID) {
        $sql = "SELECT COUNT(*) as count FROM Rating WHERE rater_ID = :rater_ID AND rated_ID = :rated_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":rater_ID", $rater_ID);
        $query->bindParam(":rated_ID", $rated_ID);
        
        if ($query->execute()) {
            $result = $query->fetch();
            return $result['count'] > 0;
        }
        return false;
    }
}
