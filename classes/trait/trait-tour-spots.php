<?php

trait TourSpotsTrait {
    
    public function createTourSpot($spots_Name, $spots_Description, $spots_category, $spots_Address, $spots_GoogleLink = null, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Tour_Spots (spots_Name, spots_Description, spots_category, spots_Address, spots_GoogleLink) 
                    VALUES (:spots_Name, :spots_Description, :spots_category, :spots_Address, :spots_GoogleLink)";
            $query = $db->prepare($sql);
            $query->bindParam(':spots_Name', $spots_Name);
            $query->bindParam(':spots_Description', $spots_Description);
            $query->bindParam(':spots_category', $spots_category);
            $query->bindParam(':spots_Address', $spots_Address);
            $query->bindParam(':spots_GoogleLink', $spots_GoogleLink);
            
            if ($query->execute()) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Tour Spot Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function getAllTourSpots() {
        $sql = "SELECT * FROM Tour_Spots ORDER BY spots_Name";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTourSpotById($spots_ID) {
        $sql = "SELECT * FROM Tour_Spots WHERE spots_ID = :spots_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':spots_ID', $spots_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getTourSpotsByCategory($category) {
        $sql = "SELECT * FROM Tour_Spots WHERE spots_category = :category ORDER BY spots_Name";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':category', $category);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchTourSpots($search_term) {
        $search = "%{$search_term}%";
        $sql = "SELECT * FROM Tour_Spots 
                WHERE spots_Name LIKE :search 
                OR spots_Description LIKE :search 
                OR spots_category LIKE :search 
                OR spots_Address LIKE :search
                ORDER BY spots_Name";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':search', $search);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTourSpot($spots_ID, $spots_Name, $spots_Description, $spots_category, $spots_Address, $spots_GoogleLink = null) {
        try {
            $sql = "UPDATE Tour_Spots 
                    SET spots_Name = :spots_Name, 
                        spots_Description = :spots_Description, 
                        spots_category = :spots_category, 
                        spots_Address = :spots_Address, 
                        spots_GoogleLink = :spots_GoogleLink 
                    WHERE spots_ID = :spots_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':spots_ID', $spots_ID);
            $query->bindParam(':spots_Name', $spots_Name);
            $query->bindParam(':spots_Description', $spots_Description);
            $query->bindParam(':spots_category', $spots_category);
            $query->bindParam(':spots_Address', $spots_Address);
            $query->bindParam(':spots_GoogleLink', $spots_GoogleLink);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Tour Spot Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTourSpot($spots_ID) {
        try {
            $sql = "DELETE FROM Tour_Spots WHERE spots_ID = :spots_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':spots_ID', $spots_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Tour Spot Delete Error: " . $e->getMessage());
            return false;
        }
    }

    public function getSpotCategories() {
        $sql = "SELECT DISTINCT spots_category FROM Tour_Spots WHERE spots_category IS NOT NULL ORDER BY spots_category";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
}
