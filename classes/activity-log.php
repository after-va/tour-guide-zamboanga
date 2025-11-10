<?php 

require_once __DIR__ . "/../config/database.php";
require_once "trait/account/activity-logs.php";

class ActivityLogs extends Database {

    use AccountActivity;

    public function addgetActionID($action_name,$db){
        
        $sql = "SELECT action_ID FROM Action WHERE action_name = :action_name";
        $query = $db->prepare($sql);
        $query->bindParam(':action_name', $action_name);
        $result = $query->execute();

        if ($result){
            return $result['action_ID'];
        }

        $sql = "INSERT INTO Action (action_name) VALUES (:action_name)";
        $query = $db->prepare($sql);
        $query->bindParam(':action_name', $action_name);

        if($query->execute()){
            return $db->lastInsertId();
        } else {
            return false;
        }


    }




}

?>