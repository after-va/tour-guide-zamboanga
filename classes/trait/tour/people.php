<?php

trait PeopleTrait{

    public function addgetPeople($numberofpeople_adult, $numberofpeople_children, $numberofpeople_maximum,	$numberofpeople_based, $currency, $basedAmount, $discount,$db){
        try{
            $pricing_ID = $this->addgetPricing($currency, $basedAmount, $discount,$db);

            if(!$pricing_ID){
                return false;
            }

            $sql = "INSERT INTO number_of_people (pricing_ID, numberofpeople_adult, numberofpeople_children, numberofpeople_maximum,numberofpeople_based) VALUES (:pricing_ID, :numberofpeople_adult, :numberofpeople_children, :numberofpeople_maximum, :numberofpeople_based)";
            $query->bindParam("pricing_ID",$pricing_ID);
            $query->bindParam(":numberofpeople_adult",$numberofpeople_adult);
            $query->bindParam(":numberofpeople_children",$numberofpeople_children);
            $query->bindParam(":numberofpeople_maximum",$numberofpeople_maximum);
            $query->bindParam(":numberofpeople_based", $numberofpeople_based);

            if ($query_insert->execute()) {
            return $db->lastInsertId();
            } else {
                return false;
            }
        }


    }



}