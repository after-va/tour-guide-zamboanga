<?php
require_once "database.php";
require_once "trait/trait-booking.php";
require_once "trait/trait-payment.php";

class BookingManager extends Database {
    use BookingTrait, PaymentTrait;

    public function createBookingWithPayment($customer_ID, $schedule_ID, $tourPackage_ID, $booking_PAX, $payment_amount, $method_ID) {
        $db = $this->connect();
        $db->beginTransaction();

        try {
            // Create booking
            $booking_ID = $this->createBooking($customer_ID, $schedule_ID, $tourPackage_ID, $booking_PAX, $db);
            
            if (!$booking_ID) {
                $db->rollBack();
                return false;
            }

            // Create payment
            $paymentinfo_ID = $this->createPayment($booking_ID, $payment_amount, $db);
            
            if (!$paymentinfo_ID) {
                $db->rollBack();
                return false;
            }

            // Create payment transaction
            $transaction_reference = 'TXN-' . time() . '-' . $booking_ID;
            $transaction_ID = $this->createPaymentTransaction($paymentinfo_ID, $method_ID, $transaction_reference, 'pending', $db);
            
            if (!$transaction_ID) {
                $db->rollBack();
                return false;
            }

            $db->commit();
            return [
                'booking_ID' => $booking_ID,
                'paymentinfo_ID' => $paymentinfo_ID,
                'transaction_ID' => $transaction_ID,
                'transaction_reference' => $transaction_reference
            ];
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Booking with Payment Error: " . $e->getMessage());
            return false;
        }
    }

    public function addCompanionToBooking($booking_ID, $companion_name, $companioncategory_ID) {
        $db = $this->connect();
        $db->beginTransaction();

        try {
            // Create companion
            $sql = "INSERT INTO Companion_Info (companion_name, companioncategory_ID) 
                    VALUES (:companion_name, :companioncategory_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':companion_name', $companion_name);
            $query->bindParam(':companioncategory_ID', $companioncategory_ID);
            $query->execute();
            $companion_ID = $db->lastInsertId();

            // Link to booking
            $sql = "INSERT INTO Booking_Bundle (companion_ID, booking_ID) 
                    VALUES (:companion_ID, :booking_ID)";
            $query = $db->prepare($sql);
            $query->bindParam(':companion_ID', $companion_ID);
            $query->bindParam(':booking_ID', $booking_ID);
            $query->execute();

            $db->commit();
            return $companion_ID;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Add Companion Error: " . $e->getMessage());
            return false;
        }
    }

    public function getBookingCompanions($booking_ID) {
        $sql = "SELECT ci.*, cc.companioncategory_name 
                FROM Booking_Bundle bb
                INNER JOIN Companion_Info ci ON bb.companion_ID = ci.companion_ID
                INNER JOIN Companion_Category cc ON ci.companioncategory_ID = cc.companioncategory_ID
                WHERE bb.booking_ID = :booking_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':booking_ID', $booking_ID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompanionCategories() {
        $sql = "SELECT * FROM Companion_Category ORDER BY companioncategory_name";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
