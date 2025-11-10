<?php

trait AccountActivity {

    public function loginActivity($account_ID) {
        
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $action_name = 'Login';
            $action_ID = $this->addgetActionID($action_name,$db);

            if (!$action_ID){
                error_log("Action ID is unknown: " . $e->getMessage());
            }

            $sql= "INSERT INTO Activity_Logs (account_ID, action_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':account_ID', $account_ID);
            $query->bindParam(':action_ID', $action_ID);

            $query->execute();

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("delete Tour Package error: " . $e->getMessage());
            return false;
        }

        








    }




}



?>