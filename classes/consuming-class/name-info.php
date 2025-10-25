<?php

require_once "../database.php";

class NameInfo extends Database{

    public function addgetName($firstname, $middlename, $lastname, $suffix, $db){
        // Check if name already exists
        $sql_select = "SELECT name_ID 
                       FROM name_info 
                       WHERE first_Name = :firstname 
                       AND middle_Name = :middlename 
                       AND last_Name = :lastname 
                       AND name_Suffix = :suffix";

        $query_select = $db->prepare($sql_select);
        $query_select->bindParam(":firstname", $firstname);
        $query_select->bindParam(":middlename", $middlename);
        $query_select->bindParam(":lastname", $lastname);
        $query_select->bindParam(":suffix", $suffix);
        $query_select->execute();
        $result = $query_select->fetch();

        if($result){
            return $result["name_ID"];
        }

        // Insert if not found
        $sql_insert = "INSERT INTO name_info (first_Name, middle_Name, last_Name, name_Suffix) 
                       VALUES (:firstname, :middlename, :lastname, :suffix)";
        $query_insert = $db->prepare($sql_insert);
        $query_insert->bindParam(":firstname", $firstname);
        $query_insert->bindParam(":middlename", $middlename);
        $query_insert->bindParam(":lastname", $lastname);
        $query_insert->bindParam(":suffix", $suffix);

        if ($query_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public function deleteName($name_ID){
        $sql = "DELETE FROM name_info WHERE name_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $name_ID);

        return $query->execute();
    }
}