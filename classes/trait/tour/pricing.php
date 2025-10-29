<?php

trait PricingTrait{

    public function addgetPricing($currency, $basedAmount, $discount, $db) {
        $sql = "SELECT pricing_ID 
                FROM Pricing 
                WHERE pricing_currency = :currency 
                AND pricing_based = :basedAmount 
                AND pricing_discount = :discount";
        $query = $db->prepare($sql);
        $query->bindParam(":currency", $currency);
        $query->bindParam(":basedAmount", $basedAmount);
        $query->bindParam(":discount", $discount);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result["pricing_ID"];
        }

        $sql = "INSERT INTO Pricing(pricing_currency, pricing_based, pricing_discount)
                VALUES (:currency, :basedAmount, :discount)";
        $query_insert = $db->prepare($sql);
        $query_insert->bindParam(":currency", $currency);
        $query_insert->bindParam(":basedAmount", $basedAmount);
        $query_insert->bindParam(":discount", $discount);
        
        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }


}