<?php

require_once "Database.php";

class TourSpot extends Database {
    
    // Create a new tour spot
    public function createTourSpot($spots_Name, $spots_Description, $spots_category, $spots_Address, $spots_GoogleLink) {
        $sql = "INSERT INTO Tour_Spots (spots_Name, spots_Description, spots_category, spots_Address, spots_GoogleLink) 
                VALUES (:spots_Name, :spots_Description, :spots_category, :spots_Address, :spots_GoogleLink)";
        
        try {
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":spots_Name", $spots_Name);
            $query->bindParam(":spots_Description", $spots_Description);
            $query->bindParam(":spots_category", $spots_category);
            $query->bindParam(":spots_Address", $spots_Address);
            $query->bindParam(":spots_GoogleLink", $spots_GoogleLink);
            
            if ($query->execute()) {
                return $this->connect()->lastInsertId();
            }
            // Log error info for debugging
            error_log("TourSpot Insert Error: " . print_r($query->errorInfo(), true));
            return false;
        } catch (PDOException $e) {
            error_log("TourSpot Insert Exception: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all tour spots
    public function getAllTourSpots() {
        $sql = "SELECT * FROM Tour_Spots ORDER BY spots_Name ASC";
        $query = $this->connect()->prepare($sql);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get tour spot by ID
    public function getTourSpotById($spots_ID) {
        $sql = "SELECT * FROM Tour_Spots WHERE spots_ID = :spots_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":spots_ID", $spots_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Get tour spots by category
    public function getTourSpotsByCategory($category) {
        $sql = "SELECT * FROM Tour_Spots WHERE spots_category = :category ORDER BY spots_Name ASC";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":category", $category);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Update tour spot
    public function updateTourSpot($spots_ID, $spots_Name, $spots_Description, $spots_category, $spots_Address, $spots_GoogleLink) {
        $sql = "UPDATE Tour_Spots 
                SET spots_Name = :spots_Name, 
                    spots_Description = :spots_Description, 
                    spots_category = :spots_category, 
                    spots_Address = :spots_Address, 
                    spots_GoogleLink = :spots_GoogleLink 
                WHERE spots_ID = :spots_ID";
        
        try {
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":spots_ID", $spots_ID);
            $query->bindParam(":spots_Name", $spots_Name);
            $query->bindParam(":spots_Description", $spots_Description);
            $query->bindParam(":spots_category", $spots_category);
            $query->bindParam(":spots_Address", $spots_Address);
            $query->bindParam(":spots_GoogleLink", $spots_GoogleLink);
            
            if ($query->execute()) {
                return true;
            }
            error_log("TourSpot Update Error: " . print_r($query->errorInfo(), true));
            return false;
        } catch (PDOException $e) {
            error_log("TourSpot Update Exception: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete tour spot
    public function deleteTourSpot($spots_ID) {
        $sql = "DELETE FROM Tour_Spots WHERE spots_ID = :spots_ID";
        
        try {
            $query = $this->connect()->prepare($sql);
            $query->bindParam(":spots_ID", $spots_ID);
            
            if ($query->execute()) {
                return true;
            }
            error_log("TourSpot Delete Error: " . print_r($query->errorInfo(), true));
            return false;
        } catch (PDOException $e) {
            error_log("TourSpot Delete Exception: " . $e->getMessage());
            return false;
        }
    }
}
