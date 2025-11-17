<?php

trait UpdateBookings{




    // Inside your Booking class
    public function updateBookings($timedate = null) {
        $now = $timedate ? new DateTime($timedate, new DateTimeZone('Asia/Manila')) 
                        : new DateTime('now', new DateTimeZone('Asia/Manila'));
        $nowStr = $now->format('Y-m-d H:i:s');

        $sql = "UPDATE booking
                SET booking_status = CASE
                    WHEN booking_status = 'Pending for Payment'
                        AND booking_start_date <= DATE_ADD(?, INTERVAL 1 DAY)
                        THEN 'Booking Expired — Payment Not Completed'

                    WHEN booking_status = 'Pending for Approval'
                        AND booking_start_date <= ?
                        THEN 'Booking Expired — Guide Did Not Confirm in Time'

                    WHEN booking_status IN ('Approved', 'In Progress')
                        AND booking_end_date <= ?
                        THEN 'Completed'

                    ELSE booking_status
                END
                WHERE booking_status IN ('Pending for Payment', 'Pending for Approval', 'Approved', 'In Progress')
                AND (booking_start_date <= DATE_ADD(?, INTERVAL 1 DAY) OR booking_end_date <= ?)";

        try {
            $db = $this->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute([$nowStr, $nowStr, $nowStr, $nowStr, $nowStr]);

            $updated = $stmt->rowCount();

            // LOG THE SYSTEM ACTIVITY AFTER SUCCESS
            if ($updated > 0) { // Make sure Activity class is included
                $this->activity->systemUpdateBooking();
            }

            error_log("[AUTO-UPDATE SUCCESS] $nowStr (PH) | Updated: $updated bookings");

            return [
                'success' => true,
                'updated' => $updated,
                'message' => "$updated booking(s) auto-updated"
            ];

        } catch (Exception $e) {
            error_log("BOOKING AUTO-UPDATE FAILED: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }





}


?>