<?php

trait PaymentTrait {
    
    public function createPayment($booking_ID, $paymentinfo_Amount, $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $paymentinfo_Date = date('Y-m-d');
            $sql = "INSERT INTO Payment_Info (booking_ID, paymentinfo_Amount, paymentinfo_Date) 
                    VALUES (:booking_ID, :paymentinfo_Amount, :paymentinfo_Date)";
            $query = $db->prepare($sql);
            $query->bindParam(':booking_ID', $booking_ID);
            $query->bindParam(':paymentinfo_Amount', $paymentinfo_Amount);
            $query->bindParam(':paymentinfo_Date', $paymentinfo_Date);
            
            if ($query->execute()) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Payment Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function createPaymentTransaction($paymentinfo_ID, $method_ID, $transaction_reference, $transaction_status = 'pending', $db = null) {
        $closeConnection = false;
        if ($db === null) {
            $db = $this->connect();
            $closeConnection = true;
        }

        try {
            $sql = "INSERT INTO Payment_Transaction (paymentinfo_ID, method_ID, transaction_reference, transaction_status) 
                    VALUES (:paymentinfo_ID, :method_ID, :transaction_reference, :transaction_status)";
            $query = $db->prepare($sql);
            $query->bindParam(':paymentinfo_ID', $paymentinfo_ID);
            $query->bindParam(':method_ID', $method_ID);
            $query->bindParam(':transaction_reference', $transaction_reference);
            $query->bindParam(':transaction_status', $transaction_status);
            
            if ($query->execute()) {
                return $db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Payment Transaction Creation Error: " . $e->getMessage());
            return false;
        } finally {
            if ($closeConnection) {
                $db = null;
            }
        }
    }

    public function updatePaymentTransactionStatus($transaction_ID, $transaction_status, $gateway_response = null) {
        try {
            $sql = "UPDATE Payment_Transaction 
                    SET transaction_status = :transaction_status, 
                        gateway_response = :gateway_response,
                        updated_at = NOW()";
            
            if ($transaction_status === 'completed') {
                $sql .= ", paid_at = NOW()";
            }
            
            $sql .= " WHERE transaction_ID = :transaction_ID";
            
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':transaction_ID', $transaction_ID);
            $query->bindParam(':transaction_status', $transaction_status);
            $query->bindParam(':gateway_response', $gateway_response);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Payment Transaction Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function getPaymentByBooking($booking_ID) {
        $sql = "SELECT pi.*, pt.transaction_status, pt.transaction_reference, pm.method_name 
                FROM Payment_Info pi
                LEFT JOIN Payment_Transaction pt ON pi.paymentinfo_ID = pt.paymentinfo_ID
                LEFT JOIN Payment_Method pm ON pt.method_ID = pm.method_ID
                WHERE pi.booking_ID = :booking_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':booking_ID', $booking_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPaymentMethods() {
        $sql = "SELECT * FROM Payment_Method WHERE is_active = 1 ORDER BY method_name";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentMethodById($method_ID) {
        $sql = "SELECT * FROM Payment_Method WHERE method_ID = :method_ID";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':method_ID', $method_ID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function processRefund($transaction_ID, $refund_amount, $refund_reason) {
        try {
            $sql = "UPDATE Payment_Transaction 
                    SET transaction_status = 'refunded', 
                        refund_amount = :refund_amount, 
                        refund_reason = :refund_reason, 
                        refunded_at = NOW() 
                    WHERE transaction_ID = :transaction_ID";
            $query = $this->connect()->prepare($sql);
            $query->bindParam(':transaction_ID', $transaction_ID);
            $query->bindParam(':refund_amount', $refund_amount);
            $query->bindParam(':refund_reason', $refund_reason);
            
            return $query->execute();
        } catch (PDOException $e) {
            error_log("Refund Processing Error: " . $e->getMessage());
            return false;
        }
    }
}
