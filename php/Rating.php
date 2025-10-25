<?php

require_once "Database.php";

class Rating extends Database {
    
    // Add a rating
    public function addRating($rater_account_role_ID, $rated_account_role_ID, $rating_value, $rating_description) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Insert rating
            $sql = "INSERT INTO Rating (rater_account_role_ID, rated_account_role_ID, rating_value, rating_description) 
                    VALUES (:rater_account_role_ID, :rated_account_role_ID, :rating_value, :rating_description)";
            
            $query = $db->prepare($sql);
            $query->bindParam(":rater_account_role_ID", $rater_account_role_ID);
            $query->bindParam(":rated_account_role_ID", $rated_account_role_ID);
            $query->bindParam(":rating_value", $rating_value);
            $query->bindParam(":rating_description", $rating_description);
            
            if ($query->execute()) {
                // Update account role's rating score
                $this->updateAccountRoleRatingScore($rated_account_role_ID, $db);
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
    
    // Update account role's average rating score
    private function updateAccountRoleRatingScore($account_role_ID, $db) {
        $sql = "UPDATE Account_Role 
                SET role_rating_score = (
                    SELECT AVG(rating_value) 
                    FROM Rating 
                    WHERE rated_account_role_ID = :account_role_ID
                ) 
                WHERE account_role_ID = :account_role_ID";
        
        $query = $db->prepare($sql);
        $query->bindParam(":account_role_ID", $account_role_ID);
        return $query->execute();
    }
    
    // Get ratings for an account role
    public function getRatingsByAccountRole($account_role_ID) {
        $sql = "SELECT r.*, 
                       CONCAT(n.name_first, ' ', n.name_last) as rater_name,
                       ar_rater.role_ID as rater_role,
                       ri.role_name as rater_role_name
                FROM Rating r
                INNER JOIN Account_Role ar_rater ON r.rater_account_role_ID = ar_rater.account_role_ID
                INNER JOIN User_Login ul ON ar_rater.login_ID = ul.login_ID
                INNER JOIN Person p ON ul.person_ID = p.person_ID
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN Role_Info ri ON ar_rater.role_ID = ri.role_ID
                WHERE r.rated_account_role_ID = :account_role_ID
                ORDER BY r.rating_date DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":account_role_ID", $account_role_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get average rating for an account role
    public function getAverageRating($account_role_ID) {
        $sql = "SELECT AVG(rating_value) as average_rating, COUNT(*) as total_ratings
                FROM Rating
                WHERE rated_account_role_ID = :account_role_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":account_role_ID", $account_role_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Check if user has already rated someone
    public function hasRated($rater_account_role_ID, $rated_account_role_ID) {
        $sql = "SELECT COUNT(*) as count FROM Rating 
                WHERE rater_account_role_ID = :rater_account_role_ID 
                AND rated_account_role_ID = :rated_account_role_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":rater_account_role_ID", $rater_account_role_ID);
        $query->bindParam(":rated_account_role_ID", $rated_account_role_ID);
        
        if ($query->execute()) {
            $result = $query->fetch();
            return $result['count'] > 0;
        }
        return false;
    }
    
    // Get account role ID by person ID and role ID
    public function getAccountRoleID($person_ID, $role_ID) {
        $sql = "SELECT ar.account_role_ID 
                FROM Account_Role ar
                INNER JOIN User_Login ul ON ar.login_ID = ul.login_ID
                WHERE ul.person_ID = :person_ID AND ar.role_ID = :role_ID AND ar.is_active = 1";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":person_ID", $person_ID);
        $query->bindParam(":role_ID", $role_ID);
        
        if ($query->execute()) {
            $result = $query->fetch();
            return $result ? $result['account_role_ID'] : null;
        }
        return null;
    }
}
