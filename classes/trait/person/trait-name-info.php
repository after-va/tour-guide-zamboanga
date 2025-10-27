<?php

trait NameInfoTrait{

    // $name_first, $name_second, $name_middle, $name_last, $name_suffix,
    public function addgetNameInfo($name_first, $name_second, $name_middle, $name_last, $name_suffix, $db) {
        $sql_check = "
            SELECT name_ID 
            FROM Name_Info
            WHERE first_Name = :firstname
            AND (second_Name = :secondname OR (second_Name IS NULL AND :secondname IS NULL))
            AND (middle_Name = :middlename OR (middle_Name IS NULL AND :middlename IS NULL))
            AND last_Name = :lastname
            AND (name_Suffix = :suffix OR (name_Suffix IS NULL AND :suffix IS NULL))
        ";

        $q_check = $db->prepare($sql_check);
        $q_check->bindParam(":firstname", $name_first);
        $q_check->bindParam(":secondname", $name_second);
        $q_check->bindParam(":middlename", $name_middle);
        $q_check->bindParam(":lastname", $name_last);
        $q_check->bindParam(":suffix", $name_suffix);
        $q_check->execute();

        $existing = $q_check->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            return $existing["name_ID"];
        }

        // Insert new name record
        $sql_insert = "
            INSERT INTO Name_Info (first_Name, second_Name, middle_Name, last_Name, name_Suffix)
            VALUES (:firstname, :secondname, :middlename, :lastname, :suffix)
        ";

        $q_insert = $db->prepare($sql_insert);
        $q_insert->bindParam(":firstname", $name_first);
        $q_insert->bindParam(":secondname", $name_second);
        $q_insert->bindParam(":middlename", $name_middle);
        $q_insert->bindParam(":lastname", $name_last);
        $q_insert->bindParam(":suffix", $name_suffix);

        if ($q_insert->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }


    public function renameNameSmart($person_ID, $firstname, $middlename, $lastname, $suffix){
        $db = $this->connect();

        // 1. Get current name_ID of this person
        $sql = "SELECT name_ID FROM Person WHERE person_ID = :person_ID";
        $q = $db->prepare($sql);
        $q->bindParam(":person_ID", $person_ID);
        $q->execute();
        $current = $q->fetch(PDO::FETCH_ASSOC);

        if(!$current){
            return false;
        }

        $current_name_ID = $current['name_ID'];

        // 2. Count how many people use this name_ID
        $sql_count = "SELECT COUNT(*) AS total FROM Person WHERE name_ID = :name_ID";
        $q_count = $db->prepare($sql_count);
        $q_count->bindParam(":name_ID", $current_name_ID);
        $q_count->execute();
        $count = $q_count->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Check if the NEW NAME already exists
        $sql_check = "SELECT name_ID FROM name_info
                    WHERE first_Name = :firstname AND middle_Name = :middlename 
                    AND last_Name = :lastname AND name_Suffix = :suffix";
        $q_check = $db->prepare($sql_check);
        $q_check->bindParam(":firstname", $firstname);
        $q_check->bindParam(":middlename", $middlename);
        $q_check->bindParam(":lastname", $lastname);
        $q_check->bindParam(":suffix", $suffix);
        $q_check->execute();
        $existing = $q_check->fetch(PDO::FETCH_ASSOC);

        // ---- CASE A: New name already exists → just link to it
        if($existing){
            $new_name_ID = $existing["name_ID"];

            $sql_update = "UPDATE Person SET name_ID = :new_name_ID WHERE person_ID = :person_ID";
            $q_update = $db->prepare($sql_update);
            $q_update->bindParam(":new_name_ID", $new_name_ID);
            $q_update->bindParam(":person_ID", $person_ID);
            $q_update->execute();

            return $new_name_ID;
        }

        // ---- CASE B: Only this person uses the current name → safe to rename
        if($count == 1){
            $sql_update_name = "UPDATE name_info 
                                SET first_Name = :firstname, middle_Name = :middlename, last_Name = :lastname, name_Suffix = :suffix
                                WHERE name_ID = :name_ID";
            $q_update_name = $db->prepare($sql_update_name);
            $q_update_name->bindParam(":firstname", $firstname);
            $q_update_name->bindParam(":middlename", $middlename);
            $q_update_name->bindParam(":lastname", $lastname);
            $q_update_name->bindParam(":suffix", $suffix);
            $q_update_name->bindParam(":name_ID", $current_name_ID);
            $q_update_name->execute();

            return $current_name_ID;
        }

        // ---- CASE C: More than 1 person uses the name → create new one
        $sql_insert = "INSERT INTO name_info (first_Name, middle_Name, last_Name, name_Suffix)
                    VALUES (:firstname, :middlename, :lastname, :suffix)";
        $q_insert = $db->prepare($sql_insert);
        $q_insert->bindParam(":firstname", $firstname);
        $q_insert->bindParam(":middlename", $middlename);
        $q_insert->bindParam(":lastname", $lastname);
        $q_insert->bindParam(":suffix", $suffix);
        $q_insert->execute();

        $new_name_ID = $db->lastInsertId();

        $sql_update_person = "UPDATE Person SET name_ID = :new_name_ID WHERE person_ID = :person_ID";
        $q_update_person = $db->prepare($sql_update_person);
        $q_update_person->bindParam(":new_name_ID", $new_name_ID);
        $q_update_person->bindParam(":person_ID", $person_ID);
        $q_update_person->execute();

        return $new_name_ID;
    }


    public function deleteName($name_ID){
        $sql = "DELETE FROM name_info WHERE name_ID = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $name_ID);

        return $query->execute();
    }

}