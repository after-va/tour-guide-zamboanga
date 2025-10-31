<?php

trait MethodTrait{

    public function viewAllPaymentMethod(){
         $sql = "SELECT * FROM Method";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

}


?>