<?php

trait TourPackagesTrait {
    public function getTourPackagesByGuide($guideID) {
        $sql = "SELECT * FROM Tour_Package WHERE guide_ID = :guideID";
        $query = $this->db->prepare($sql);
        $query->bindParam(':guideID', $guideID);
        $query->execute();

        if($query->execute()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }

        $sql = "INSERT INTO "
    }

    public function addTourPackage($guide_ID, $tourpackage_name, $tourpackage_desc, $schedule_days, $numberofpeople_adult, $numberofpeople_children, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db){

        try{

            $schedule_ID = $this->addgetSchedule($schedule_days, $numberofpeople_adult, $numberofpeople_children, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount,$db);
            if(!$schedule_ID){
                return false;
            }

            $sql = "INSERT INTO Tour_Package(guide_ID, tourpackage_name, tourpackage_desc, schedule_ID) VALUES(:guide_ID, :tourpackage_name, :tourpackage_desc, :schedule_ID)";
            $query->bindParam(':guide_ID'$guide_ID);
            $query->bindParam(':tourpackage_name',$tourpackage_name);
            $query->bindParam(':tourpackage_desc',$tourpackage_desc);
            $query->bindParam(':schedule_ID',$schedule_ID);

            if ($query->execute()){
                return $db->lastInsertId();
            } else {   
                return false;
            }



        }catch (PDOException $e) {
            $db->rollBack();
            error_log("Adding Package Error: " . $e->getMessage()); 
            return false;
        }

    }




}