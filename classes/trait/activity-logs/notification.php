<?php 

trait NotificationTrait{

    public function touristNotification(int $tourist_ID): array {
        $db = $this->connect();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql = "SELECT 
                    al.activity_ID,
                    al.account_ID,
                    al.action_ID,
                    al.activity_description,
                    al.activity_timestamp,
                    a.action_name,
                    COALESCE(av.activity_isViewed, 0) AS is_viewed 
                FROM 
                    `activity_log` al 
                INNER JOIN 
                    `action` a ON al.action_ID = a.action_ID
                LEFT JOIN
                    `Activity_View` av 
                    ON al.activity_ID = av.activity_ID AND al.account_ID = av.account_ID
                WHERE 
                    al.account_ID = :touristID 
                    AND a.action_name NOT IN ('Logout', 'Login', 'Change Account Into Tourist')
                ORDER BY 
                    al.activity_timestamp DESC";

        try {
            $query = $db->prepare($sql);
            
            // Correctly bind the parameter with the expected name
            $query->bindParam(':touristID', $tourist_ID, PDO::PARAM_INT);

            $query->execute();
            
            // Fetch all results as an associative array
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("FATAL touristNotification error: Failed to fetch notifications for ID {$tourist_ID}. Details: " . $e->getMessage());
            return []; // Return an empty array on failure
        }
    }

    // In your ActivityLogs class
    public function markTouristNotificationsAsViewed(int $tourist_ID): bool {
        $db = $this->connect();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO Activity_View (activity_ID, account_ID, activity_isViewed)
                SELECT  al.activity_ID, al.account_ID, 1 AS activity_isViewed
                FROM  activity_log al 
                INNER JOIN  `action` a ON al.action_ID = a.action_ID
                WHERE   al.account_ID = :touristID  AND a.action_name NOT IN ('Logout', 'Login', 'Change Account Into Tourist')
                ON DUPLICATE KEY UPDATE activity_isViewed = 1";

        try {
            $query = $db->prepare($sql);
            $query->bindParam(':touristID', $tourist_ID, PDO::PARAM_INT);
            $query->execute();
            return true;
        } catch (Exception $e) {
            error_log("FATAL markTouristNotificationsAsViewed error: " . $e->getMessage());
            return false;
        }
    }



}


?>