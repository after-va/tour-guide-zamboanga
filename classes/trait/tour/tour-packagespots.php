<?php

trait TourPackageSpot {

    public function linkSpotToPackage($tourpackage_ID, $tour_spots){
        $db = $this->connect();
        $db->beginTransaction();
        try{
            $sql = "INSERT INTO tour_package_spots (tourpackage_ID, spot_ID) 
                    VALUES (:tourpackage_ID, :tour_spot_ID)";
            $query = $db->prepare($sql);
            foreach($tour_spots as $spot_ID){
                $query->bindParam(':tourpackage_ID', $tourpackage_ID, PDO::PARAM_INT);
                $query->bindParam(':tour_spot_ID', $spot_ID, PDO::PARAM_INT);
                $query->execute();
            }
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error linking spots to package: " . $e->getMessage());
            return false;
        }
    }

     public function getSpotsByPackage($packageID) {
        $sql = "SELECT ts.* 
                FROM Tour_Package_Spots tps
                JOIN Tour_Spots ts ON tps.spots_ID = ts.spots_ID
                WHERE tps.tourpackage_ID = ?";
        $query = $this->conn->prepare($sql);
        $query->execute([$packageID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPackagesBySpot($spotID) {
        $sql = "SELECT tp.* 
                FROM Tour_Package_Spots tps
                JOIN Tour_Package tp ON tps.tourpackage_ID = tp.tourpackage_ID
                WHERE tps.spots_ID = ?";
        $query = $this->conn->prepare($sql);
        $query->execute([$spotID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getViewAll() {
        $sql = "SELECT * FROM View_TourSpots_With_Packages";
        $query = $this->conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


}