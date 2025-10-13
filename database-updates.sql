-- Additional tables for Tourismo Zamboanga System
-- Run this after tourguidesystem.sql

-- User Login Credentials Table
CREATE TABLE IF NOT EXISTS User_Login (
    login_ID INT AUTO_INCREMENT PRIMARY KEY,
    person_ID INT NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    last_login DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (person_ID) REFERENCES Person(person_ID) ON DELETE CASCADE
);

-- Password Reset Tokens
CREATE TABLE IF NOT EXISTS Password_Reset (
    reset_ID INT AUTO_INCREMENT PRIMARY KEY,
    person_ID INT NOT NULL,
    reset_token VARCHAR(100) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (person_ID) REFERENCES Person(person_ID) ON DELETE CASCADE
);

-- Activity Log
CREATE TABLE IF NOT EXISTS Activity_Log (
    log_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_ID) REFERENCES Person(person_ID) ON DELETE SET NULL
);

-- Guide Certifications
CREATE TABLE IF NOT EXISTS Guide_Certification (
    certification_ID INT AUTO_INCREMENT PRIMARY KEY,
    guide_ID INT NOT NULL,
    certification_type VARCHAR(100),
    certification_number VARCHAR(100),
    issue_date DATE,
    expiry_date DATE,
    document_path VARCHAR(255),
    status ENUM('pending', 'verified', 'expired', 'rejected') DEFAULT 'pending',
    verified_by INT,
    verified_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES Person(person_ID) ON DELETE SET NULL
);

-- Guide Availability
CREATE TABLE IF NOT EXISTS Guide_Availability (
    availability_ID INT AUTO_INCREMENT PRIMARY KEY,
    guide_ID INT NOT NULL,
    available_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    is_available TINYINT(1) DEFAULT 1,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE
);

-- Tour Package Pricing
CREATE TABLE IF NOT EXISTS Package_Pricing (
    pricing_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_ID INT NOT NULL,
    guide_ID INT,
    base_price DECIMAL(10,2) NOT NULL,
    price_per_person DECIMAL(10,2),
    max_persons INT,
    min_persons INT DEFAULT 1,
    currency VARCHAR(10) DEFAULT 'PHP',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE
);

-- Payment Methods
CREATE TABLE IF NOT EXISTS Payment_Method (
    method_ID INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(50) NOT NULL,
    method_type ENUM('card', 'ewallet', 'bank', 'cash') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    processing_fee DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment Transactions (Extended)
CREATE TABLE IF NOT EXISTS Payment_Transaction (
    transaction_ID INT AUTO_INCREMENT PRIMARY KEY,
    paymentinfo_ID INT NOT NULL,
    method_ID INT,
    transaction_reference VARCHAR(100) UNIQUE,
    transaction_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_gateway VARCHAR(50),
    gateway_response TEXT,
    paid_at DATETIME,
    refunded_at DATETIME,
    refund_amount DECIMAL(10,2),
    refund_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paymentinfo_ID) REFERENCES Payment_Info(paymentinfo_ID) ON DELETE CASCADE,
    FOREIGN KEY (method_ID) REFERENCES Payment_Method(method_ID) ON DELETE SET NULL
);

-- Notifications
CREATE TABLE IF NOT EXISTS Notifications (
    notification_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT NOT NULL,
    notification_type VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    link_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME,
    FOREIGN KEY (user_ID) REFERENCES Person(person_ID) ON DELETE CASCADE
);

-- Reviews (Extended with images)
CREATE TABLE IF NOT EXISTS Review_Images (
    image_ID INT AUTO_INCREMENT PRIMARY KEY,
    rating_ID INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rating_ID) REFERENCES Rating(rating_ID) ON DELETE CASCADE
);

-- Booking Status History
CREATE TABLE IF NOT EXISTS Booking_Status_History (
    history_ID INT AUTO_INCREMENT PRIMARY KEY,
    booking_ID INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_by INT,
    change_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES Person(person_ID) ON DELETE SET NULL
);

-- Favorites/Wishlist
CREATE TABLE IF NOT EXISTS User_Favorites (
    favorite_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT NOT NULL,
    guide_ID INT,
    tourPackage_ID INT,
    spots_ID INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID) ON DELETE CASCADE
);

-- Messages/Chat
CREATE TABLE IF NOT EXISTS Messages (
    message_ID INT AUTO_INCREMENT PRIMARY KEY,
    sender_ID INT NOT NULL,
    receiver_ID INT NOT NULL,
    booking_ID INT,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (receiver_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID) ON DELETE SET NULL
);

-- System Settings
CREATE TABLE IF NOT EXISTS System_Settings (
    setting_ID INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(50),
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES Person(person_ID) ON DELETE SET NULL
);

-- Insert default roles
INSERT INTO Role_Info (role_name) VALUES 
('Admin'),
('Tour Guide'),
('Tourist')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- Insert default payment methods
INSERT INTO Payment_Method (method_name, method_type, processing_fee) VALUES
('Credit Card', 'card', 2.50),
('Debit Card', 'card', 2.50),
('GCash', 'ewallet', 1.00),
('PayMaya', 'ewallet', 1.00),
('Bank Transfer', 'bank', 0.00),
('Cash', 'cash', 0.00)
ON DUPLICATE KEY UPDATE method_name = VALUES(method_name);

-- Insert default system settings
INSERT INTO System_Settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Tourismo Zamboanga', 'text', 'Website name'),
('site_email', 'info@tourismozamboanga.com', 'email', 'Contact email'),
('booking_fee', '200', 'number', 'Service fee per booking in PHP'),
('cancellation_hours', '24', 'number', 'Hours before tour to allow cancellation'),
('max_booking_days', '90', 'number', 'Maximum days in advance for booking'),
('min_booking_hours', '24', 'number', 'Minimum hours in advance for booking')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Create indexes for better performance
CREATE INDEX idx_booking_status ON Booking(booking_Status);
CREATE INDEX idx_booking_customer ON Booking(customer_ID);
CREATE INDEX idx_schedule_guide ON Schedule(guide_ID);
CREATE INDEX idx_schedule_date ON Schedule(schedule_StartDateTime);
CREATE INDEX idx_payment_booking ON Payment_Info(booking_ID);
CREATE INDEX idx_rating_rated ON Rating(rated_ID);
CREATE INDEX idx_user_login_username ON User_Login(username);
CREATE INDEX idx_activity_log_user ON Activity_Log(user_ID);
CREATE INDEX idx_notifications_user ON Notifications(user_ID, is_read);

-- Create views for common queries
CREATE OR REPLACE VIEW v_user_details AS
SELECT 
    p.person_ID,
    p.role_ID,
    r.role_name,
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    n.name_first,
    n.name_last,
    ci.contactinfo_email as email,
    ph.phone_number,
    p.person_RatingScore as rating,
    ul.username,
    ul.last_login,
    ul.is_active
FROM Person p
LEFT JOIN Role_Info r ON p.role_ID = r.role_ID
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID;

CREATE OR REPLACE VIEW v_booking_details AS
SELECT 
    b.booking_ID,
    b.booking_Status,
    b.booking_PAX,
    CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
    CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
    tp.tourPackage_Name,
    ts.spots_Name,
    s.schedule_StartDateTime,
    s.schedule_EndDateTime,
    s.schedule_MeetingSpot,
    pi.paymentinfo_Amount,
    pi.paymentinfo_Date,
    pt.transaction_status as payment_status
FROM Booking b
LEFT JOIN Person t ON b.customer_ID = t.person_ID
LEFT JOIN Name_Info tn ON t.name_ID = tn.name_ID
LEFT JOIN Schedule s ON b.schedule_ID = s.schedule_ID
LEFT JOIN Person g ON s.guide_ID = g.person_ID
LEFT JOIN Name_Info gn ON g.name_ID = gn.name_ID
LEFT JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
LEFT JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
LEFT JOIN Payment_Info pi ON b.booking_ID = pi.booking_ID
LEFT JOIN Payment_Transaction pt ON pi.paymentinfo_ID = pt.paymentinfo_ID;

-- Sample admin user (password: admin123)
-- Note: This is a hashed password, you should change it in production
INSERT INTO Name_Info (name_first, name_last) VALUES ('System', 'Administrator');
SET @name_id = LAST_INSERT_ID();

INSERT INTO Contact_Info (contactinfo_email) VALUES ('admin@tourismozamboanga.com');
SET @contact_id = LAST_INSERT_ID();

INSERT INTO Person (role_ID, name_ID, contactinfo_ID, person_Nationality) 
VALUES (1, @name_id, @contact_id, 'Filipino');
SET @person_id = LAST_INSERT_ID();

INSERT INTO User_Login (person_ID, username, password_hash) 
VALUES (@person_id, 'admin@tourismozamboanga.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- End of database updates
