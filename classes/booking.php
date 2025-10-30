<?php 

require_once __DIR__ . "/../config/database.php";
require_once "trait/booking/booking_bundle.php";
require_once "trait/booking/companion.php";

class Booking extends Database{
    use BookingBundleTrait, CompanionTrait;
    // tourist_ID	booking_status	booking_created_at	tourpackage_ID	booking_start_date	booking_end_date	
    // 'Pending for Payment',
    // 'Pending for Approval',
    // 'Approved',
    // 'In Progress',
    // 'Completed',
    // 'Cancelled',
    // 'Refunded',
    // 'Failed'
    public function viewBookingForTourist($tourist_ID){
        $sql = "SELECT * FROM Booking WHERE tourist_ID = :tourist_ID";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->bindParam(':tourist_ID', $tourist_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addBookingForTourist($tourist_ID, $tourpackage_ID, $booking_start_date, $booking_end_date){
        $db = $this->connect();
        $db->beginTransaction();

        try {
            $sql = "INSERT INTO Booking (tourist_ID, tourpackage_ID	
            , booking_start_date, booking_end_date) VALUES (:tourist_ID, :tourpackage_ID, :booking_start_date, :booking_end_date) ";
            $query = $db->prepare($sql);
            $query->bindParam(':tourist_ID', $tourist_ID);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID);
            $query->bindParam(':booking_start_date', $booking_start_date);
            $query->bindParam(':booking_end_date', $booking_end_date);
            $result = $query->execute();

            if (!$result) {
                    $db->rollBack();
                    return false;
                } else {
                    $db->commit();
                    return true;
                }
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Booking Transaction Error: " . $e->getMessage()); 
            return false;
        }
        

    }

    




    // public function addCompanionToBooking($booking_ID, $companion_name, $category_ID){
    //     $db = $this->connect();
    //     $db->beginTransaction();

    //     try{
    //             $bookingbundle_ID = $this->addCompanionToBooking($booking_ID, $companion_name, $category_ID, $db);

    //             if (!$bookingbundle_ID) {
    //                 $db->rollBack();
    //                 return false;
    //             } else {
    //                 $db->commit();
    //                 return true;
    //             }

    //         } catch (PDOException $e) {
    //             $db->rollBack();
    //             error_log("Booking Bundle: " . $e->getMessage()); 
    //             return false;
    //         }

    // }






}

?>