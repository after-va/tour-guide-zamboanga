<?php

require_once __DIR__ . "/../config/database.php";
require_once "trait/tour/tour-packages.php";
require_once "trait/tour/tour-spots.php";
require_once "trait/tour/tour-packagespots.php";
require_once "trait/tour/schedule.php";
require_once "trait/tour/pricing.php";
require_once "trait/tour/people.php";

class TourManager extends Database {
    use TourPackagesTrait, PeopleTrait, PricingTrait, ScheduleTrait;
    use TourSpotsTrait, TourPackageSpot;

    // spots_ID, packagespots_activityname, packagespots_starttime, packagespots_endtime, packagespot_day
   public function addTourPackagesAndItsSpots($tour_spots, $packagespots_activityname, $packagespots_starttime, $packagespots_endtime, $packagespot_day, $guide_ID, $name, $desc, $days, $numberofpeople_maximum, $numberofpeople_based, $currency, $forAdult, $forChild, $forYoungAdult, $forSenior, $forPWD, $includeMeal, $mealFee, $transportFee, $discount){
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $tourpackage_ID = $this->addTourPackage(
                $guide_ID, $name, $desc, $days, $numberofpeople_maximum,
                $numberofpeople_based, $currency, $forAdult, $forChild,
                $forYoungAdult, $forSenior, $forPWD, $includeMeal,
                $mealFee, $transportFee, $discount, $db
            );

            if (!$tourpackage_ID) {
                throw new Exception('Failed to create tour package');
            }

            $sql = "INSERT INTO Tour_Package_Spots
                    (tourpackage_ID, spots_ID, packagespot_activityname,
                    packagespot_starttime, packagespot_endtime, packagespot_day)
                    VALUES
                    (:tourpackage_ID, :spots_ID, :activity, :start, :end, :day)";

            $stmt = $db->prepare($sql);

            $count = count($tour_spots);
            for ($i = 0; $i < $count; $i++) {
                $stmt->execute([
                    ':tourpackage_ID' => $tourpackage_ID,
                    ':spots_ID'       => $tour_spots[$i] ?? null,
                    ':activity'       => $packagespots_activityname[$i] ?? null,
                    ':start'          => $packagespots_starttime[$i] ?? null,
                    ':end'            => $packagespots_endtime[$i] ?? null,
                    ':day'            => $packagespot_day[$i] ?? null,
                ]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("addTourPackagesAndItsSpots error: " . $e->getMessage());
            return false;
        }
    }


    //updateTourPackage($tourpackage_ID, $guide_ID, $name, $desc, $schedule_ID, $days, $numberofpeople_ID, $numberofpeople_maximum, $numberofpeople_based, $pricing_ID, $currency, $forAdult, $forChild, $forYoungAdult, $forSenior, $forPWD, $includeMeal, $mealFee, $transportFee, $discount, $db)
    public function updateTourPackagesAndItsSpots($packagespot_ID, $tour_spots, $packagespots_activityname, $packagespots_starttime, $packagespots_endtime, $packagespot_day, $tourpackage_ID, $guide_ID, $name, $desc, $schedule_ID, $days, $numberofpeople_ID, $numberofpeople_maximum, $numberofpeople_based, $pricing_ID, $currency, $forAdult, $forChild, $forYoungAdult, $forSenior, $forPWD, $includeMeal, $mealFee, $transportFee, $discount){
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $result = $this->updateTourPackages($tourpackage_ID, $guide_ID, $name, $desc, $schedule_ID, $days, $numberofpeople_ID, $numberofpeople_maximum, $numberofpeople_based, $pricing_ID, $currency, $forAdult, $forChild, $forYoungAdult, $forSenior, $forPWD, $includeMeal, $mealFee, $transportFee, $discount, $db);

            if (!$result) {
                throw new Exception('Failed to create tour package');
            }

            $sql = "UPDATE Tour_Package_Spots SET
                    tourpackage_ID = :tourpackage_ID, spots_ID = :spots_ID, packagespot_activityname = :activity,
                    packagespot_starttime = :start_time, packagespot_endtime = :end_time, packagespot_day = :packagespot_day WHERE packagespots_ID = :packagespots_ID";

            $stmt = $db->prepare($sql);

            $count = count($tour_spots);
            for ($i = 0; $i < $count; $i++) {
                $stmt->execute([ ':packagespots_ID' => $packagespots_ID, 
                ':tourpackage_ID' => $tourpackage_ID, 
                ':spots_ID'       => $tour_spots[$i] ?? null, 
                ':activity'       => $packagespots_activityname[$i] ?? null, 
                ':start_time'          => $packagespots_starttime[$i] ?? null, 
                ':end_time'            => $packagespots_endtime[$i] ?? null, 
                ':packagespot_day'=> $packagespot_day[$i] ?? null ]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("UpdateTourPackagesAndItsSpots error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTourPackage($spots, $tourpackage_ID, $schedule_ID, $numberofpeople_ID, $pricing_ID){
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $pricingDelete = $this->deletePricingByID($pricing_ID,$db);
            $numberofpeopleDelete = $this->deletePeopleByID($numberofpeople_ID, $db);
            $scheduleDelete = $this->deleteScheduleByID($schedule_ID, $db);
            $count = count($spots);
            for ($i = 0; $i < $count; $i++){
                $tourpackage_spots = $this->deleteTourPackageSpotsByTourPackageID($tourpackage_ID, $db);
            }

            $sql = "DELETE FROM Tour_Package WHERE tourpackage_ID = :tourpackage_ID";
            $query = $db->prepare($sql);        
            $query->bindParam(":tourpackage_ID", $tourpackage_ID);
            $query->execute();

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("delete Tour Package error: " . $e->getMessage());
            return false;
        }
    }
    // Additional methods specific to TourManager can be added here





}