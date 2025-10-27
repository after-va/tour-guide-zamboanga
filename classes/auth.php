<?php
require_once __DIR__ . '/database.php';

class Auth extends Database {
    public function login($username, $password){
        $db = $this->connect();
        $sql = "SELECT login_ID, person_ID, username, password_hash, is_active FROM User_Login WHERE username = :u LIMIT 1";
        $q = $db->prepare($sql);
        $q->bindParam(":u", $username);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        if ((int)$row['is_active'] !== 1) return false;
        if (!password_verify($password, $row['password_hash'])) return false;
        // Update last_login
        $upd = $db->prepare("UPDATE User_Login SET last_login = NOW() WHERE login_ID = :id");
        $upd->bindParam(":id", $row['login_ID'], PDO::PARAM_INT);
        $upd->execute();
        // Get roles
        $roles = [];
        $qr = $db->prepare("SELECT ar.role_ID, r.role_name FROM Account_Role ar JOIN Role_Info r ON r.role_ID = ar.role_ID WHERE ar.login_ID = :lid AND ar.is_active = 1");
        $qr->bindParam(":lid", $row['login_ID'], PDO::PARAM_INT);
        $qr->execute();
        foreach ($qr->fetchAll(PDO::FETCH_ASSOC) as $r){ $roles[] = $r['role_name']; }
        return [
            'login_ID' => (int)$row['login_ID'],
            'person_ID' => (int)$row['person_ID'],
            'username' => $row['username'],
            'roles' => $roles,
        ];
    }

    public function createUserLogin($person_ID, $username, $password, $role_name){
        $db = $this->connect();
        $db->beginTransaction();
        try{
            // ensure username unique
            $chk = $db->prepare("SELECT 1 FROM User_Login WHERE username = :u");
            $chk->bindParam(":u", $username);
            $chk->execute();
            if ($chk->fetch()) { $db->rollBack(); return 'username_exists'; }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins = $db->prepare("INSERT INTO User_Login (person_ID, username, password_hash) VALUES (:pid, :u, :ph)");
            $ins->bindParam(":pid", $person_ID, PDO::PARAM_INT);
            $ins->bindParam(":u", $username);
            $ins->bindParam(":ph", $hash);
            if (!$ins->execute()) { $db->rollBack(); return false; }
            $login_ID = (int)$db->lastInsertId();

            // Fetch role_ID
            $qr = $db->prepare("SELECT role_ID FROM Role_Info WHERE role_name = :rn LIMIT 1");
            $qr->bindParam(":rn", $role_name);
            $qr->execute();
            $role = $qr->fetch(PDO::FETCH_ASSOC);
            if (!$role){ $db->rollBack(); return 'role_missing'; }

            $ar = $db->prepare("INSERT INTO Account_Role (login_ID, role_ID, is_active) VALUES (:lid, :rid, 1)");
            $ar->bindParam(":lid", $login_ID, PDO::PARAM_INT);
            $ar->bindParam(":rid", $role['role_ID'], PDO::PARAM_INT);
            if (!$ar->execute()) { $db->rollBack(); return false; }

            $db->commit();
            return $login_ID;
        } catch (Throwable $e){
            $db->rollBack();
            return false;
        }
    }
}
