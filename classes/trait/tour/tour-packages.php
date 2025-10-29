<?php

trait TourPackagesTrait {

    public function getAllTourPackages(){
        $sql = "SELECT * FROM tour_packages";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTourPackages($tour_ID) {
        $db = $this->connect();
        if (!$db) {
            $this->setLastError("Database connection failed");
            error_log("Database connection failed in getTourPackages");
            return false;
        }

        $sql = "SELECT tp.* 
                FROM tour_packages tp
                INNER JOIN tour_tour_packages ttp 
                    ON tp.tour_package_ID = ttp.tour_package_ID
                WHERE ttp.tour_ID = :tour_ID";

        try {
            $query = $db->prepare($sql);
            $query->bindParam(':tour_ID', $tour_ID, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching tour packages for tour_ID $tour_ID: " . $e->getMessage());
            return false;
        }
    }

    public function addTourPackages($package_name, $package_description, $guide_ID) {
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $sql = "INSERT INTO tour_packages (tourpackage_name, tourpackage_description, guide_ID)
                    VALUES (:package_name, :package_description, :guide_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':package_name', $package_name);
            $query->bindParam(':package_description', $package_description);
            $query->bindParam(':guide_ID', $guide_ID);

            if ($query->execute()) {
                $db->commit();
                return true;
            } else {
                $db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error adding tour package: " . $e->getMessage());
            return false;
        }
    }
}
