<?php

trait PricingTrait{

    public function addgetPricing($currency, $basedAmount, $discount,$db){
        $sql = "SELECT * FROM Pricing WHERE currency = :currency AND basedAmount = :basedAmount AND discount = :discount";
        $query = $db->prepare($sql);
        $query->bindParam(":currency", $currency);
        $query->bindParam(":basedAmount", $basedAmount);
        $query->bindParam(":discount", $discount);
        $query->bindParam(":totalAmount", $totalAmount);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if($result){
            return $result["pricing_ID"];
        }

        $sql = "INSERT INTO Pricing(pricing_currency, pricing_based, pricing_discount) 
                VALUES (:currency, :basedAmount, :discount)";
        $query = $db->prepare($sql);
        $query->bindParam(":currency", $currency);
        $query->bindParam(":basedAmount", $basedAmount);
        $query->bindParam(":discount", $discount);
        
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }


    }


}