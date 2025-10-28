-- Add missing payment tables for better payment tracking

-- Payment Method table
CREATE TABLE IF NOT EXISTS Payment_Method (
    method_ID INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(50) NOT NULL,
    method_type ENUM('card', 'ewallet', 'bank', 'cash') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    processing_fee DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment Transaction table for tracking individual transactions
CREATE TABLE IF NOT EXISTS Payment_Transaction (
    transaction_ID INT AUTO_INCREMENT PRIMARY KEY,
    paymentinfo_ID INT NOT NULL,
    method_ID INT NOT NULL,
    transaction_reference VARCHAR(100) UNIQUE NOT NULL,
    transaction_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_gateway VARCHAR(50),
    gateway_response TEXT,
    paid_at DATETIME,
    refunded_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paymentinfo_ID) REFERENCES Payment_Info(paymentinfo_ID) ON DELETE CASCADE,
    FOREIGN KEY (method_ID) REFERENCES Payment_Method(method_ID)
);

-- Add default payment methods
INSERT INTO Payment_Method (method_name, method_type, processing_fee) VALUES
('Credit Card', 'card', 2.50),
('Debit Card', 'card', 2.50),
('GCash', 'ewallet', 1.00),
('PayMaya', 'ewallet', 1.00),
('Bank Transfer', 'bank', 0.00),
('Cash', 'cash', 0.00)
ON DUPLICATE KEY UPDATE method_name = VALUES(method_name);

-- Add indexes for better performance
ALTER TABLE Payment_Transaction ADD INDEX idx_payment_status (transaction_status);
ALTER TABLE Payment_Transaction ADD INDEX idx_payment_reference (transaction_reference);