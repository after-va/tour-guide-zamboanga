<?php

trait TourPackagesTrait {
    

    public function addTourPackage($guide_ID, $tourpackage_name, $tourpackage_desc, $schedule_days, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount){
        $db = $this->connect();
        $db->beginTransaction();
        try{
            $schedule_ID = $this->addgetSchedule($schedule_days,  $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount,$db);
            if(!$schedule_ID){
                $db->rollBack();
                return false;
            }

            $sql = "INSERT INTO Tour_Package(guide_ID, tourpackage_name, tourpackage_desc, schedule_ID) VALUES(:guide_ID, :tourpackage_name, :tourpackage_desc, :schedule_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':guide_ID',$guide_ID);
            $query->bindParam(':tourpackage_name',$tourpackage_name);
            $query->bindParam(':tourpackage_desc',$tourpackage_desc);
            $query->bindParam(':schedule_ID',$schedule_ID);

            if ($query->execute()){
                $lastInsertId = $db->lastInsertId();
                $db->commit();
                return $lastInsertId;
            } else {   
                $db->rollBack();
                return false;
            }

        }catch (PDOException $e) {
            $db->rollBack();
            error_log("Adding Package Error: " . $e->getMessage()); 
            return false;
        }
    }




}