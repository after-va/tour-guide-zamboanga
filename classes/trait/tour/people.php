<?php

trait PeopleTrait{

    public function addgetPeople($numberofpeople_maximum, $numberofpeople_based, $currency, $basedAmount, $discount, $db){
        try {
            // First check if matching record exists
            $sql = "SELECT numberofpeople_ID 
                   FROM Number_Of_People nop
                   JOIN Pricing p ON nop.pricing_ID = p.pricing_ID
                   WHERE nop.numberofpeople_maximum = :max
                   AND nop.numberofpeople_based = :based
                   AND p.pricing_currency = :currency
                   AND p.pricing_based = :basedAmount
                   AND p.pricing_discount = :discount";
            
            $query = $db->prepare($sql);
            $query->bindParam(':max', $numberofpeople_maximum);
            $query->bindParam(':based', $numberofpeople_based);
            $query->bindParam(':currency', $currency);
            $query->bindParam(':basedAmount', $basedAmount);
            $query->bindParam(':discount', $discount);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result['numberofpeople_ID'];
            }

            // If no matching record exists, create new one
            $pricing_ID = $this->addgetPricing($currency, $basedAmount, $discount, $db);
            if(!$pricing_ID){
                return false;
            }

            $sql = "INSERT INTO Number_Of_People (pricing_ID, numberofpeople_maximum, numberofpeople_based) 
                    VALUES (:pricing_ID, :numberofpeople_maximum, :numberofpeople_based)";
            $query = $db->prepare($sql);
            $query->bindParam(":pricing_ID", $pricing_ID);
            $query->bindParam(":numberofpeople_maximum", $numberofpeople_maximum);
            $query->bindParam(":numberofpeople_based", $numberofpeople_based);

            if ($query->execute()) {
                return $db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Adding People Error: " . $e->getMessage()); 
            return false;
        }
    }

    public function getPeopleByID($peopleID) {
        $db = $this->connect();
        $sql = "SELECT * FROM Number_Of_People WHERE numberofpeople_ID = :peopleID";
        $query = $db->prepare($sql);
        $query->bindParam(':peopleID', $peopleID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

}