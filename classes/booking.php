<?php 

require_once __DIR__ . "/../config/database.php";
require_once "trait/booking/booking_bundle.php";
require_once "trait/booking/companion.php";

class Booking extends Database{
    use BookingBundleTrait, CompanionTrait;

    public function addCompanionToBooking($booking_ID, $companion_name, $category_ID){
        $db = $this->connect();
        $db->beginTransaction();

        try{
                $bookingbundle_ID = $this->addCompanionToBooking($booking_ID, $companion_name, $category_ID, $db);

                if (!$bookingbundle_ID) {
                    $db->rollBack();
                    return false;
                } else {
                    $db->commit();
                    return true;
                }

            } catch (PDOException $e) {
                $db->rollBack();
                error_log("Booking Bundle: " . $e->getMessage()); 
                return false;
            }

    }



}

?>