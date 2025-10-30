<?php
trait CompanionTrait{
    /**
     * Get existing companion OR create a new one
     * @param string $companion_name
     * @param int $category_ID
     * @param PDO $db
     * @return int|false  companion_ID or false
     */
    public function getOrCreateCompanion($companion_name, $category_ID, $db)
    {
        // 1. Try to find existing companion by name + category
        $sql = "SELECT companion_ID 
                FROM Companion 
                WHERE companion_name = :name 
                  AND companion_category_ID = :cat_id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':name'   => $companion_name,
            ':cat_id' => $category_ID
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return (int)$row['companion_ID'];
        }

        // 2. Insert new companion
        $sql = "INSERT INTO Companion (companion_name, companion_category_ID) 
                VALUES (:name, :cat_id)";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            ':name'   => $companion_name,
            ':cat_id' => $category_ID
        ]);

        return $success ? $db->lastInsertId() : false;
    }
}