<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/account/account-login.php";



class Admin extends Database {
    use AccountLoginTrait;

}