<?php

trait MethodTrait{

    public function viewAllPaymentMethodCategory(){
        $sql = "SELECT * FROM Method_Category";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

}


?>