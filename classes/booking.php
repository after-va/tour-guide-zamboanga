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
    public function getAllCompanionCategories(){
        $sql = "SELECT * FROM `companion_category`";
        $db = $this->connect();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function viewBookingByTourist($tourist_ID){
        $sql = "SELECT 
            b.booking_ID,
            tp.tourpackage_name,
            tp.tourpackage_desc,
            CONCAT(n.name_first, ' ', n.name_last) AS guide_name,
            b.booking_start_date,
            b.booking_end_date,
            b.booking_status,
            s.schedule_days,
            GROUP_CONCAT(ts.spots_name SEPARATOR ', ') AS tour_spots
        FROM booking b
        JOIN tour_package tp ON b.tourpackage_ID = tp.tourpackage_ID
        LEFT JOIN schedule s ON tp.schedule_ID = s.schedule_ID
        LEFT JOIN guide g ON tp.guide_ID = g.guide_ID
        LEFT JOIN account_info ai ON g.account_ID = ai.account_ID
        LEFT JOIN user_login ul ON ai.user_ID = ul.user_ID
        LEFT JOIN person p ON ul.person_ID = p.person_ID
        LEFT JOIN name_info n ON p.name_ID = n.name_ID
        LEFT JOIN tour_package_spots tps ON tp.tourpackage_ID = tps.tourpackage_ID
        LEFT JOIN tour_spots ts ON tps.spots_ID = ts.spots_ID
        WHERE b.tourist_ID = :tourist_ID
        GROUP BY b.booking_ID
        ORDER BY ABS(DATEDIFF(b.booking_start_date, CURRENT_TIMESTAMP)) ASC
    ";
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
            $sql = "INSERT INTO booking (tourist_ID, tourpackage_ID, booking_start_date, booking_end_date) 
                    VALUES (:tourist_ID, :tourpackage_ID, :booking_start_date, :booking_end_date)";
            $query = $db->prepare($sql);
            $query->bindParam(':tourist_ID', $tourist_ID, PDO::PARAM_INT);
            $query->bindParam(':tourpackage_ID', $tourpackage_ID, PDO::PARAM_INT);
            $query->bindParam(':booking_start_date', $booking_start_date);
            $query->bindParam(':booking_end_date', $booking_end_date);
            $result = $query->execute();

            if ($result) {
                $booking_ID = $db->lastInsertId();
                $db->commit();
                return $booking_ID;
            } else {
                $db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Booking Transaction Error: " . $e->getMessage());
            return false;
        }
    }


    public function cancelBookingIfPendingForApproval($booking_ID, $account_ID){
    $booking_ID = (int)$booking_ID;
    $account_ID = (int)$account_ID;

    if ($booking_ID <= 0 || $account_ID <= 0) {
        return "Invalid ID provided.";
    }

    $db = $this->connect();
    $db->beginTransaction();

    try {
        // Step 1: Check current booking status
        $checkSql = "SELECT booking_status FROM Booking WHERE booking_ID = :booking_ID";
        $checkStmt = $db->prepare($checkSql);
        $checkStmt->bindParam(':booking_ID', $booking_ID, PDO::PARAM_INT);
        $checkStmt->execute();
        $status = $checkStmt->fetchColumn();

        if ($status === false) {
            $db->rollBack();
            return "Booking not found.";
        }

        if ($status !== 'Pending for Approval') {
            $db->rollBack();
            return "Cannot cancel booking. Current status: {$status}";
        }

        // Step 3: Atomic update with status check
        $updateSql = "UPDATE Booking 
                      SET booking_status = 'Cancelled' 
                      WHERE booking_ID = :booking_ID 
                        AND booking_status = 'Pending for Approval'";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->bindParam(':booking_ID', $booking_ID, PDO::PARAM_INT);
        $updateStmt->execute();

        if ($updateStmt->rowCount() === 0) {
            $db->rollBack();
            return "Update failed: Status may have changed or booking not found.";
        }

        // ... rest of action_ID and logging ...

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollBack();
        error_log("Cancel Booking Error: " . $e->getMessage());
        return false;
    }
}



    // public function viewBookingByBookingID($tourist_ID){
    //     $sql = "SELECT * FROM Booking WHERE booking_ID = :booking_ID";
    //     $db = $this->connect();
    //     $query = $db->prepare($sql);
    //     $query->bindParam(':booking_ID', $booking_ID);
    //     $query->execute();
    //     return $query->fetchAll(PDO::FETCH_ASSOC);
    // }
    




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