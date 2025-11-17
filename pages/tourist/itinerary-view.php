<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
}

require_once "../../classes/mailer.php";

echo Itinerary::generateHTML($booking, $package, $guide, $spots, $companions, $name);