<?php

require_once "database.php";
//     person_ID
//     role_ID
//     name_ID
//     person_Nationality
//     person_Gender
//     person_CivilStatus
//     person_DateOfBirth
//     rating_ID
//     contactinfo_ID
class Tourists extends Database{
    public $person_ID = "";
    public $person_nationality = "";
    public $person_gender = "";
    public $person_civilstatus = "";
    public $person_dateOfbirth = "";

    // Check if the Person registering is exist Already
    public function isTouristExist($name_first, $name_second, $name_middle, $name_last, $name_suffix, $person_nationality, $person_gender, $person_civilstatus, $person_dateofbirth){
        $sql = "SELECT COUNT(*) AS total FROM Person p INNER JOIN Name_Info n ON p.name_ID=n.name_ID WHERE n.name_first =:name_first AND (n.name_second = :name_second OR (n.name_second IS NULL AND :name_second IS NULL)) AND (n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL)) AND n.name_last = :name_last AND (n.name_suffix = :name_suffix OR (n.name_suffix IS NULL AND :name_suffix IS NULL)) AND p.person_Nationality = :person_nationality AND p.person_Gender = :person_gender AND p.person_CivilStatus = :person_civilstatus AND p.person_DateOfBirth = :person_dateofbirth";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first",$name_first);
        $query->bindParam(":name_second",$name_second);
        $query->bindParam(":name_middle",$name_middle);
        $query->bindParam(":name_last",$name_last);
        $query->bindParam(":name_suffix",$name_suffix);
        $query->bindParam(":person_nationality",$person_nationality);
        $query->bindParam(":person_gender",$person_gender);
        $query->bindParam(":person_civilstatus",$person_civilstatus);
        $query->bindParam(":person_dateofbirth",$person_dateofbirth);

        $record = null;
        if($query->execute()){
            $record = $query->fetch();
        }

        if($record["total"] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix){
        $sql = "SELECT name_ID FROM name_info n WHERE n.name_first = :name_first AND n.name_last = :name_last AND (n.name_second = :name_second OR (n.name_second IS NULL AND :name_second IS NULL)) AND ( n.name_middle = :name_middle OR (n.name_middle IS NULL AND :name_middle IS NULL) ) AND (n.name_suffix = :name_suffix OR (n.name_suffix IS NULL AND :name_suffix IS NULL));";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":name_first",$name_first);
        $query->bindParam(":name_second",$name_second);
        $query->bindParam(":name_middle",$name_middle);
        $query->bindParam(":name_last",$name_last);
        $query->bindParam(":name_suffix",$name_suffix);

        $result = $query->fetch();

        if($result){
            return $result["name_ID"];
        }

        $db = $this->connect();
        $sql = "INSERT INTO name_info (name_first, name_second, name_middle, name_last, name_suffix ) VALUES (:name_first, :name_second, :name_middle, :name_last, :name_suffix)";
        $query->bindParam(":name_first",$name_first);
        $query->bindParam(":name_second",$name_second);
        $query->bindParam(":name_middle",$name_middle);
        $query->bindParam(":name_last",$name_last);
        $query->bindParam(":name_suffix",$name_suffix);
        if ($query->execute()) {
            return $db->lastInsertId(); 
        } else {
            return false;
        }


    }







}