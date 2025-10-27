<?php
require_once __DIR__ . '/database.php';

class Guide extends Database {
    public function listGuides(){
        $db = $this->connect();
        $sql = "SELECT person_ID, full_name, email, phone_number, rating, role_name FROM v_user_details WHERE role_name = 'Tour Guide' AND role_is_active = 1 GROUP BY person_ID, full_name, email, phone_number, rating, role_name ORDER BY full_name";
        $q = $db->prepare($sql);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
