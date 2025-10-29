<?php

trait ScheduleTrait{

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

    public function addgetSchedule($schedule_days, $numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db){
        // First check if a matching schedule exists
        $sql = "SELECT s.schedule_ID
                FROM Schedule s
                JOIN Number_Of_People nop ON s.numberofpeople_ID = nop.numberofpeople_ID
                JOIN Pricing p ON nop.pricing_ID = p.pricing_ID
                WHERE 
                    s.schedule_days = :schedule_days
                    AND nop.numberofpeople_maximum = :max
                    AND nop.numberofpeople_based = :based
                    AND p.pricing_currency = :currency
                    AND p.pricing_based = :basedAmount
                    AND p.pricing_discount = :discount";
                    
        $query = $db->prepare($sql);
        $query->bindParam(':schedule_days', $schedule_days);
        $query->bindParam(':max', $numberofpeople_maximum);
        $query->bindParam(':based', $numberofpeople_based);
        $query->bindParam(':currency', $currency);
        $query->bindParam(':basedAmount', $basedAmount);
        $query->bindParam(':discount', $discount);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result["schedule_ID"];
        }

        // If no matching schedule exists, create a new one
        $numberofpeople_ID = $this->addgetPeople($numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db);
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