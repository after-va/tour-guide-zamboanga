<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/tour/tour-packages.php";
require_once "trait/tour/tour-spots.php";
require_once "trait/tour/tour-packagespots.php";

class TourManager extends Database {
    use TourPackagesTrait;
    use TourSpotsTrait;
    use TourPackageSpot;

    // Additional methods specific to TourManager can be added here
}