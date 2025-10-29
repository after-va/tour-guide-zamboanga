<?php

trait TourPackageSpot {


    public function addTourPackagespots($tourspots_ID, $guide_ID, $tourpackage_name, $tourpackage_desc, $schedule_days, $numberofpeople_adult, $numberofpeople_children, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount){
        $db = $this->connect();
        $db->beginTransaction();

        try{
            $tourpackage_ID = $this->addTourPackage($guide_ID, $tourpackage_name, $tourpackage_desc, $schedule_days, $numberofpeople_adult, $numberofpeople_children, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db);

            if(!$tourpackage_ID){
                return false;
            }

            $sql = "INSERT INTO Tour_Package_Spots(tourpackage_ID,spots_ID) VALUES (:tourpackage_ID, :spots_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(":tourpackage_ID",$tourpackage_ID);
            $query->bindParam(":spots_ID",$spots_ID);

            if ($query_insert->execute()) {
                return $db->lastInsertId();
            } else {
                return false;
            }

        }catch (PDOException $e) {
            $db->rollBack();
            error_log("Error adding tour spot: " . $e->getMessage());
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