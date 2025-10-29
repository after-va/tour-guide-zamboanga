<?php

require_once "trait-user-login.php";
trait AccountLoginTrait {

    public function addgetAccountLogin($user_ID, $role_ID, $db) {
        $sql = "SELECT accountlogin_ID FROM Account_Login 
                WHERE user_ID = :user_ID AND role_ID = :role_ID";
        $query = $db->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        $query->bindParam(":role_ID", $role_ID);
        $query->execute();
        $result = $query->fetch();

        if ($result) {
            return $result["accountlogin_ID"];
        }
        
        $sql = "INSERT INTO Account_Login (user_ID, role_ID) 
                VALUES (:user_ID, :role_ID)";
        $query = $db->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        $query->bindParam(":role_ID", $role_ID);
        
        if ($query->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    




}