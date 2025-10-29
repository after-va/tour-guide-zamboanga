<?php

trait Schedule{

    public function getScheduleByID($scheduleID) {
        $sql = "SELECT * FROM Schedule WHERE schedule_ID = :scheduleID";
        $query = $this->db->prepare($sql);
        $query->bindParam(':scheduleID', $scheduleID);
        $query->execute();

        if($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function addgetSchedule($schedule_days,  $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount,$db){
        $sql = "SELECT 
                    s.schedule_ID,
                    s.schedule_days,
                    nop.numberofpeople_adult,
                    nop.numberofpeople_children,
                    nop.numberofpeople_maximum,
                    nop.numberofpeople_based,
                    p.pricing_currency,
                    p.pricing_based AS base_price,
                    p.pricing_discount,
                    p.pricing_total,
                    tp.tourpackage_name,
                GROUP_CONCAT(ts.spots_name SEPARATOR ', ') AS spots_list
                FROM Schedule s
                JOIN Number_Of_People nop ON s.numberofpeople_ID = nop.numberofpeople_ID
                JOIN Pricing p ON nop.pricing_ID = p.pricing_ID
                LEFT JOIN Tour_Package tp ON s.schedule_ID = tp.schedule_ID
                LEFT JOIN Tour_Package_Spots tps ON tp.tourpackage_ID = tps.tourpackage_ID
                LEFT JOIN Tour_Spots ts ON tps.spots_ID = ts.spots_ID
                WHERE 
                    nop.numberofpeople_adult = :adult
                    AND nop.numberofpeople_children = :children
                    AND nop.numberofpeople_maximum = :max
                    AND nop.numberofpeople_based = :based
                GROUP BY s.schedule_ID";
        $query = $db->prepare($sql);
        $query->bindParam(':adult', $numberofpeople_adult);
        $query->bindParam(':children', $numberofpeople_children );
        $query->bindParam(':max', $numberofpeople_maximum);
        $query->bindParam(':based', $numberofpeople_based);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["address_ID"];
        }

        $numberofpeople_ID = $this->addgetPeople( $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount,$db);

        if(!$numberofpeople_ID){
            return false;
        }
        $sql = "INSERT INTO Schedule(numberofpeople_ID, schedule_days) VALUES (:numberofpeople_ID, :schedule_days)";
        $query = $db->prepare($sql);
        $query->bindParam(':numberofpeople_ID', $numberofpeople_ID);
        $query->bindParam(':schedule_days', $schedule_days);



        if ($query->execute()){
                return $db->lastInsertId();
        } else {
                
                return false;
        }
        
    }


}