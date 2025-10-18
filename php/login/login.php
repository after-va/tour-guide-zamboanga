<?php


require_once '../database/database.php';



class Login extends Database{
    public function checkLogin($username, $password){
        $sql = "SELECT * FROM User_Account WHERE account_Username = :username AND account_Password = :password";
        $query = $this->connect()->prepare($sql);
        $query->execute(['username' => $username, 'password' => $password]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        return $user;
    }
}