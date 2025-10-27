<?php

trait TourPackageTrait {
    
    public function createTourPackage($tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration, $spots_ID = null, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Tour_Package (tourPackage_Name, tourPackage_Description, tourPackage_Capacity, tourPackage_Duration, spots_ID) 
                    VALUES (:tourPackage_Name, :tourPackage_Description, :tourPackage_Capacity, :tourPackage_Duration, :spots_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':tourPackage_Name', $tourPackage_Name);
            $query->bindParam(':tourPackage_Description', $tourPackage_Description);
            $query->bindParam(':tourPackage_Capacity', $tourPackage_Capacity);
            $query->bindParam(':tourPackage_Duration', $tourPackage_Duration);
            $query->bindParam(':spots_ID', $spots_ID);
            
            if ($query->execute()) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Tour Package Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function getAllTourPackages() {
        $sql = "SELECT tp.*, ts.spots_Name, ts.spots_Description, ts.spots_Address 
                FROM Tour_Package tp
                LEFT JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                ORDER BY tp.tourPackage_Name";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTourPackageById($tourPackage_ID) {
        $sql = "SELECT tp.*, ts.spots_Name, ts.spots_Description, ts.spots_Address, ts.spots_GoogleLink 
                FROM Tour_Package tp
                LEFT JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
                WHERE tp.tourPackage_ID = :tourPackage_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':tourPackage_ID', $tourPackage_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getPackageSpots($tourPackage_ID) {
        $sql = "SELECT ps.*, ts.spots_Name, ts.spots_Description, ts.spots_Address, ts.spots_GoogleLink 
                FROM Package_Spots ps
                INNER JOIN Tour_Spots ts ON ps.spots_ID = ts.spots_ID
                WHERE ps.tourPackage_ID = :tourPackage_ID
                ORDER BY ps.spot_order";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':tourPackage_ID', $tourPackage_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSpotToPackage($tourPackage_ID, $spots_ID, $spot_order = 0, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Package_Spots (tourPackage_ID, spots_ID, spot_order) 
                    VALUES (:tourPackage_ID, :spots_ID, :spot_order)";
            $query = $db->prepare($sql);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            $query->bindParam(':spots_ID', $spots_ID);
            $query->bindParam(':spot_order', $spot_order);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Add Spot to Package Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function updateTourPackage($tourPackage_ID, $tourPackage_Name, $tourPackage_Description, $tourPackage_Capacity, $tourPackage_Duration) {
        try {
            $sql = "UPDATE Tour_Package 
                    SET tourPackage_Name = :tourPackage_Name, 
                        tourPackage_Description = :tourPackage_Description, 
                        tourPackage_Capacity = :tourPackage_Capacity, 
                        tourPackage_Duration = :tourPackage_Duration 
                    WHERE tourPackage_ID = :tourPackage_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            $query->bindParam(':tourPackage_Name', $tourPackage_Name);
            $query->bindParam(':tourPackage_Description', $tourPackage_Description);
            $query->bindParam(':tourPackage_Capacity', $tourPackage_Capacity);
            $query->bindParam(':tourPackage_Duration', $tourPackage_Duration);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Tour Package Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTourPackage($tourPackage_ID) {
        try {
            $sql = "DELETE FROM Tour_Package WHERE tourPackage_ID = :tourPackage_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':tourPackage_ID', $tourPackage_ID);
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Tour Package Delete Error: " . $e->getMessage());
            return false;
        }
    }
}
