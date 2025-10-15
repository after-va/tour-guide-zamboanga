<?php

require_once "database.php";

    // name_first 
    // name_second 
    // name_middle
    // name_last
    // name_suffix 
class Name_Info extends Database {
    public $name_first = "";
    public $name_second = "";
    public $name_middle = "";
    public $name_last = "";
    public $name_suffix = "";

    public function addNameInfo(){
        $sql = "INSERT INTO name_info (name_first, name_second, name_middle, name_last, name_suffix ) VALUES (:name_first, :name_second, :name_middle, :name_last, :name_suffix) ";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first", $this->name_first);
        $query->bindParam(":name_second", $this->name_second);
        $query->bindParam(":name_middle", $this->name_middle);
        $query->bindParam(":name_last", $this->name_last);
        $query->bindParam(":name_suffix", $this->name_suffix);

        return $query->execute();

    }




}