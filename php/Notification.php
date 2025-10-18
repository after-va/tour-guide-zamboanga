<?php

require_once "Database.php";

class Notification extends Database {
    
    // Create notification
    public function createNotification($user_ID, $notification_type, $title, $message, $link_url = null) {
        $sql = "INSERT INTO Notifications (user_ID, notification_type, title, message, link_url) 
                VALUES (:user_ID, :notification_type, :title, :message, :link_url)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        $query->bindParam(":notification_type", $notification_type);
        $query->bindParam(":title", $title);
        $query->bindParam(":message", $message);
        $query->bindParam(":link_url", $link_url);
        
        return $query->execute();
    }
    
    // Get notifications for user
    public function getNotificationsByUser($user_ID, $limit = 50) {
        $sql = "SELECT * FROM Notifications 
                WHERE user_ID = :user_ID 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        $query->bindParam(":limit", $limit, PDO::PARAM_INT);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get unread notifications
    public function getUnreadNotifications($user_ID) {
        $sql = "SELECT * FROM Notifications 
                WHERE user_ID = :user_ID AND is_read = 0 
                ORDER BY created_at DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Mark notification as read
    public function markAsRead($notification_ID) {
        $sql = "UPDATE Notifications SET is_read = 1, read_at = NOW() WHERE notification_ID = :notification_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":notification_ID", $notification_ID);
        return $query->execute();
    }
    
    // Mark all notifications as read for user
    public function markAllAsRead($user_ID) {
        $sql = "UPDATE Notifications SET is_read = 1, read_at = NOW() WHERE user_ID = :user_ID AND is_read = 0";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        return $query->execute();
    }
    
    // Get unread count
    public function getUnreadCount($user_ID) {
        $sql = "SELECT COUNT(*) as count FROM Notifications WHERE user_ID = :user_ID AND is_read = 0";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_ID", $user_ID);
        
        if ($query->execute()) {
            $result = $query->fetch();
            return $result['count'];
        }
        return 0;
    }
}
