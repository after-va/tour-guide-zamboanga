<?php

require_once "Database.php";

class Payment extends Database {
    
    // Create payment record
    public function createPayment($booking_ID, $paymentinfo_Amount, $paymentinfo_Date) {
        $sql = "INSERT INTO Payment_Info (booking_ID, paymentinfo_Amount, paymentinfo_Date) 
                VALUES (:booking_ID, :paymentinfo_Amount, :paymentinfo_Date)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":booking_ID", $booking_ID);
        $query->bindParam(":paymentinfo_Amount", $paymentinfo_Amount);
        $query->bindParam(":paymentinfo_Date", $paymentinfo_Date);
        
        if ($query->execute()) {
            return $this->connect()->lastInsertId();
        }
        return false;
    }
    
    // Get payment by booking ID
    public function getPaymentByBooking($booking_ID) {
        $sql = "SELECT pi.*, pt.transaction_status, pt.transaction_reference, pm.method_name
                FROM Payment_Info pi
                LEFT JOIN Payment_Transaction pt ON pi.paymentinfo_ID = pt.paymentinfo_ID
                LEFT JOIN Payment_Method pm ON pt.method_ID = pm.method_ID
                WHERE pi.booking_ID = :booking_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":booking_ID", $booking_ID);
        
        if ($query->execute()) {
            return $query->fetch();
        }
        return null;
    }
    
    // Create payment transaction
    public function createTransaction($paymentinfo_ID, $method_ID, $transaction_reference, $transaction_status) {
        $sql = "INSERT INTO Payment_Transaction (paymentinfo_ID, method_ID, transaction_reference, transaction_status) 
                VALUES (:paymentinfo_ID, :method_ID, :transaction_reference, :transaction_status)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":paymentinfo_ID", $paymentinfo_ID);
        $query->bindParam(":method_ID", $method_ID);
        $query->bindParam(":transaction_reference", $transaction_reference);
        $query->bindParam(":transaction_status", $transaction_status);
        
        return $query->execute();
    }
    
    // Update transaction status
    public function updateTransactionStatus($transaction_ID, $transaction_status) {
        $sql = "UPDATE Payment_Transaction SET transaction_status = :transaction_status WHERE transaction_ID = :transaction_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":transaction_status", $transaction_status);
        $query->bindParam(":transaction_ID", $transaction_ID);
        return $query->execute();
    }
    
    // Get all payment methods
    public function getAllPaymentMethods() {
        $sql = "SELECT * FROM Payment_Method WHERE is_active = 1 ORDER BY method_name ASC";
        $query = $this->connect()->prepare($sql);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    // Get all payments
    public function getAllPayments() {
        $sql = "SELECT pi.*, 
                       b.booking_ID,
                       CONCAT(n.name_first, ' ', n.name_last) as customer_name,
                       tp.tourPackage_Name,
                       pt.transaction_status,
                       pm.method_name
                FROM Payment_Info pi
                INNER JOIN Booking b ON pi.booking_ID = b.booking_ID
                INNER JOIN Person p ON b.customer_ID = p.person_ID
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                INNER JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Payment_Transaction pt ON pi.paymentinfo_ID = pt.paymentinfo_ID
                LEFT JOIN Payment_Method pm ON pt.method_ID = pm.method_ID
                ORDER BY pi.paymentinfo_Date DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
}
