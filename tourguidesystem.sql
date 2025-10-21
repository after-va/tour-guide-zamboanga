-- Country Code
CREATE TABLE Country_Code (
    countrycode_ID INT AUTO_INCREMENT PRIMARY KEY,
    countrycode_name VARCHAR(225),
    countrycode_number VARCHAR(10)
);

-- Phone Number
CREATE TABLE Phone_Number (
    phone_ID INT AUTO_INCREMENT PRIMARY KEY,
    countrycode_ID INT,
    phone_number VARCHAR(15) NOT NULL ,
    FOREIGN KEY (countrycode_ID) REFERENCES Country_Code(countrycode_ID),
    UNIQUE KEY unique_phone_per_country (countrycode_ID, phone_number)
);

-- Address
CREATE TABLE Address_Info (
    address_ID INT AUTO_INCREMENT PRIMARY KEY,
    address_houseno VARCHAR(50) NOT NULL,
    address_street VARCHAR(50) NOT NULL,
    address_barangay VARCHAR(100) NOT NULL,
    address_city VARCHAR(100) NOT NULL,
    address_province VARCHAR(100) NOT NULL,
    address_country VARCHAR(100) NOT NULL
    ,
    UNIQUE KEY unique_full_address (
        address_houseno,
        address_street,
        address_barangay,
        address_city,
        address_province,
        address_country
    )
);

-- Emergency Contact Info
CREATE TABLE Emergency_Info (
    emergency_ID INT AUTO_INCREMENT PRIMARY KEY,
    emergency_Name VARCHAR(225) NOT NULL,
    emergency_Relationship VARCHAR(225) NOT NULL,
    phone_ID INT,
    FOREIGN KEY (phone_ID) REFERENCES Phone_Number(phone_ID)
);

-- Contact Info
CREATE TABLE Contact_Info (
    contactinfo_ID INT AUTO_INCREMENT PRIMARY KEY,
    address_ID INT,
    phone_ID INT,
    contactinfo_email VARCHAR(100) NOT NULL,
    emergency_ID INT,
    FOREIGN KEY (address_ID) REFERENCES Address_Info(address_ID),
    FOREIGN KEY (phone_ID) REFERENCES Phone_Number(phone_ID),
    FOREIGN KEY (emergency_ID) REFERENCES Emergency_Info(emergency_ID)
);

-- Name Info
CREATE TABLE Name_Info (
    name_ID INT AUTO_INCREMENT PRIMARY KEY,
    name_first VARCHAR(100) NOT NULL,
    name_second VARCHAR(225),
    name_middle VARCHAR(225),
    name_last VARCHAR(225) NOT NULL,
    name_suffix VARCHAR(225)
);

-- Rating Category
CREATE TABLE Rating_Category (
    ratingcategory_ID INT AUTO_INCREMENT PRIMARY KEY,
    ratingcategory_name VARCHAR(100),
    ratingcategory_from INT,
    ratingcategory_to INT
);


-- Role Info
CREATE TABLE Role_Info (
    role_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(225)
);

-- Person
CREATE TABLE Person (
    person_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_ID INT,
    name_ID INT,
    person_Nationality VARCHAR(225),
    person_Gender VARCHAR(225),
    person_DateOfBirth DATE,
    person_RatingScore DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    contactinfo_ID INT,
    FOREIGN KEY (name_ID) REFERENCES Name_Info(name_ID),
    FOREIGN KEY (contactinfo_ID) REFERENCES Contact_Info(contactinfo_ID),
    FOREIGN KEY (role_ID) REFERENCES Role_Info(role_ID)
);

-- Rating 
CREATE TABLE Rating (
    rating_ID INT AUTO_INCREMENT PRIMARY KEY,
    rater_ID INT NOT NULL,
    rated_ID INT NOT NULL,
    rating_value DECIMAL(2,1) NOT NULL,
    rating_description VARCHAR(255),
    rating_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rater_ID) REFERENCES Person(person_ID),
    FOREIGN KEY (rated_ID) REFERENCES Person(person_ID)
);

-- Tour Spots
CREATE TABLE Tour_Spots (
    spots_ID INT AUTO_INCREMENT PRIMARY KEY,
    spots_Name VARCHAR(225),
    spots_Description VARCHAR(225),
    spots_category VARCHAR(225),
    spots_Address VARCHAR(225),
    spots_GoogleLink VARCHAR(500)
);

-- Tour Package
CREATE TABLE Tour_Package (
    tourPackage_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_Name VARCHAR(225),
    tourPackage_Description TEXT,
    tourPackage_Capacity VARCHAR(50),
    tourPackage_Duration VARCHAR(50),
    tourPackage_TotalDays INT DEFAULT 1 COMMENT 'Total number of days for the tour'
);

-- Tour Package Itinerary (Detailed day-by-day schedule with timestamps)
CREATE TABLE Tour_Package_Itinerary (
    itinerary_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_ID INT NOT NULL,
    spots_ID INT NULL COMMENT 'NULL for break times (Lunch, Break, Sleep)',
    day_number INT NOT NULL COMMENT 'Day number in the tour (1, 2, 3, etc.)',
    sequence_order INT NOT NULL COMMENT 'Order of visit within the day',
    start_time TIME NOT NULL COMMENT 'Start time for this spot (e.g., 08:00:00)',
    end_time TIME NOT NULL COMMENT 'End time for this spot (e.g., 10:00:00)',
    activity_description TEXT COMMENT 'Specific activities at this spot or break type (Lunch Break, Rest Time, Sleep)',
    notes TEXT COMMENT 'Additional notes or instructions',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_package_day_sequence (tourPackage_ID, day_number, sequence_order)
);

-- Companion Category
CREATE TABLE Companion_Category (
    companioncategory_ID INT AUTO_INCREMENT PRIMARY KEY,
    companioncategory_name VARCHAR(100)
);

-- Companion Info
CREATE TABLE Companion_Info (
    companion_ID INT AUTO_INCREMENT PRIMARY KEY,
    companion_name VARCHAR(225),
    companioncategory_ID INT,
    FOREIGN KEY (companioncategory_ID) REFERENCES Companion_Category(companioncategory_ID)
);


-- SCHEDULE
CREATE TABLE Schedule (
    schedule_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_ID INT, -- Links to the package this schedule is for
    guide_ID INT,       -- Optional: Assign the guide who will lead this specific schedule
    schedule_StartDateTime DATETIME NOT NULL,
    schedule_EndDateTime DATETIME,
    schedule_Capacity INT, -- The available slots for this scheduled trip
    schedule_MeetingSpot VARCHAR(255),
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID),
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) -- Guide is now linked here
);

-- Booking
CREATE TABLE Booking (
    booking_ID INT AUTO_INCREMENT PRIMARY KEY,
    customer_ID INT,
    schedule_ID INT,       
    tourPackage_ID INT,    
    booking_Status VARCHAR(225),
    booking_PAX INT,
    FOREIGN KEY (customer_ID) REFERENCES Person(person_ID),
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID),
    FOREIGN KEY (schedule_ID) REFERENCES Schedule(schedule_ID)
);

-- Booking Companion Bundle
CREATE TABLE Booking_Bundle (
    bookingbundle_ID INT AUTO_INCREMENT PRIMARY KEY,
    companion_ID INT,
    booking_ID INT,
    FOREIGN KEY (companion_ID) REFERENCES Companion_Info(companion_ID),
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID)
);

-- Payment
CREATE TABLE Payment_Info (
    paymentinfo_ID INT AUTO_INCREMENT PRIMARY KEY,
    booking_ID INT,
    paymentinfo_Amount DECIMAL(10,2),
    paymentinfo_Date DATE,
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID)
);

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
CREATE INDEX idx_itinerary_package ON Tour_Package_Itinerary(tourPackage_ID, day_number, sequence_order);
CREATE INDEX idx_itinerary_spot ON Tour_Package_Itinerary(spots_ID);

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
    tp.tourPackage_TotalDays,
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
LEFT JOIN Payment_Info pi ON b.booking_ID = pi.booking_ID
LEFT JOIN Payment_Transaction pt ON pi.paymentinfo_ID = pt.paymentinfo_ID;

-- View for Tour Package Itinerary Details
CREATE OR REPLACE VIEW v_tour_package_itinerary AS
SELECT 
    tp.tourPackage_ID,
    tp.tourPackage_Name,
    tp.tourPackage_Description,
    tp.tourPackage_TotalDays,
    tpi.itinerary_ID,
    tpi.day_number,
    tpi.sequence_order,
    ts.spots_ID,
    ts.spots_Name,
    ts.spots_Description,
    ts.spots_category,
    ts.spots_Address,
    ts.spots_GoogleLink,
    tpi.start_time,
    tpi.end_time,
    CONCAT(TIME_FORMAT(tpi.start_time, '%h:%i %p'), ' - ', TIME_FORMAT(tpi.end_time, '%h:%i %p')) as time_range,
    tpi.activity_description,
    tpi.notes
FROM Tour_Package tp
LEFT JOIN Tour_Package_Itinerary tpi ON tp.tourPackage_ID = tpi.tourPackage_ID
LEFT JOIN Tour_Spots ts ON tpi.spots_ID = ts.spots_ID
ORDER BY tp.tourPackage_ID, tpi.day_number, tpi.sequence_order;

-- Sample admin user (password: admin123)
-- Note: This is a hashed password, you should change it in production
-- IMPORTANT: Make sure to import tourguidesystem-data.sql first for country codes!

-- Create Philippines country code if it doesn't exist
INSERT IGNORE INTO Country_Code (countrycode_name, countrycode_number) 
VALUES ('Philippines', '+63');

-- Get the Philippines country code ID
SET @ph_country_code = (SELECT countrycode_ID FROM Country_Code WHERE countrycode_name = 'Philippines' LIMIT 1);

-- Create admin phone number
INSERT INTO Phone_Number (countrycode_ID, phone_number) 
VALUES (@ph_country_code, '9123456789');
SET @admin_phone_id = LAST_INSERT_ID();

-- Create admin address
INSERT INTO Address_Info (address_houseno, address_street, address_barangay, address_city, address_province, address_country)
VALUES ('N/A', 'N/A', 'N/A', 'Zamboanga City', 'Zamboanga del Sur', 'Philippines');
SET @admin_address_id = LAST_INSERT_ID();

-- Create admin emergency contact
INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship)
VALUES (@admin_phone_id, 'System Admin', 'Self');
SET @admin_emergency_id = LAST_INSERT_ID();

-- Create admin contact info
INSERT INTO Contact_Info (address_ID, phone_ID, emergency_ID, contactinfo_email)
VALUES (@admin_address_id, @admin_phone_id, @admin_emergency_id, 'admin@tourismozamboanga.com');
SET @contact_id = LAST_INSERT_ID();

-- Create admin name
INSERT INTO Name_Info (name_first, name_last) VALUES ('System', 'Administrator');
SET @name_id = LAST_INSERT_ID();

-- Create admin person
INSERT INTO Person (role_ID, name_ID, contactinfo_ID, person_Nationality, person_Gender, person_DateOfBirth) 
VALUES (1, @name_id, @contact_id, 'Filipino', 'Male', '1990-01-01');
SET @person_id = LAST_INSERT_ID();

-- Create admin login
INSERT INTO User_Login (person_ID, username, password_hash, is_active) 
VALUES (@person_id, 'admin@tourismozamboanga.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- ============================================================================
-- SAMPLE DATA: Tour Package with Detailed Itinerary
-- ============================================================================
-- Example: 3-Day Zamboanga City Tour with timestamps
-- Uncomment the code below to insert sample data
/*
INSERT INTO Tour_Package (tourPackage_Name, tourPackage_Description, tourPackage_Capacity, tourPackage_Duration, tourPackage_TotalDays)
VALUES (
    'Zamboanga City Heritage Tour',
    'A comprehensive 3-day tour exploring the rich history, culture, and natural beauty of Zamboanga City',
    '15',
    '3 Days / 2 Nights',
    3
);
SET @package_id = LAST_INSERT_ID();

-- Day 1 Itinerary
INSERT INTO Tour_Package_Itinerary (tourPackage_ID, spots_ID, day_number, sequence_order, start_time, end_time, activity_description, notes)
VALUES 
(@package_id, 1, 1, 1, '08:00:00', '10:00:00', 'Morning walk along the scenic Paseo del Mar, enjoy the sea breeze and take photos', 'Bring comfortable walking shoes'),
(@package_id, 2, 1, 2, '10:30:00', '12:00:00', 'Visit Fort Pilar and explore the historical shrine and museum', 'Entrance fee: PHP 50'),
(@package_id, 3, 1, 3, '13:00:00', '14:30:00', 'Lunch at a local restaurant featuring Zamboanga cuisine', 'Try the famous curacha and satti'),
(@package_id, 4, 1, 4, '15:00:00', '17:00:00', 'Explore Plaza Pershing and City Hall area', 'Great for photography');

-- Day 2 Itinerary
INSERT INTO Tour_Package_Itinerary (tourPackage_ID, spots_ID, day_number, sequence_order, start_time, end_time, activity_description, notes)
VALUES 
(@package_id, 5, 2, 1, '06:00:00', '08:00:00', 'Early morning island hopping to Great Santa Cruz Island', 'Famous for pink sand beach'),
(@package_id, 6, 2, 2, '09:00:00', '11:00:00', 'Snorkeling and beach activities', 'Bring swimwear and sunscreen'),
(@package_id, 7, 2, 3, '12:00:00', '13:30:00', 'Beachside lunch with fresh seafood', 'Included in package'),
(@package_id, 8, 2, 4, '15:00:00', '17:00:00', 'Visit Yakan Weaving Village', 'See traditional weaving demonstrations');

-- Day 3 Itinerary
INSERT INTO Tour_Package_Itinerary (tourPackage_ID, spots_ID, day_number, sequence_order, start_time, end_time, activity_description, notes)
VALUES 
(@package_id, 9, 3, 1, '08:00:00', '10:00:00', 'Visit Pasonanca Park and Tree House', 'Nature walk and bird watching'),
(@package_id, 10, 3, 2, '10:30:00', '12:00:00', 'Shopping at Barter Trade Center', 'Buy souvenirs and local products'),
(@package_id, 11, 3, 3, '13:00:00', '14:00:00', 'Farewell lunch at a seaside restaurant', 'Last meal together'),
(@package_id, 12, 3, 4, '14:30:00', '15:30:00', 'Transfer to airport/hotel', 'End of tour');
*/

-- ============================================================================
-- HOW TO USE THE NEW ITINERARY SYSTEM
-- ============================================================================
-- 1. Create a tour package with tourPackage_TotalDays
-- 2. Add itinerary items to Tour_Package_Itinerary table with:
--    - day_number: Which day (1, 2, 3, etc.)
--    - sequence_order: Order within that day (1, 2, 3, etc.)
--    - start_time and end_time: Specific time slots (e.g., '08:00:00', '10:00:00')
--    - activity_description: What tourists will do at this spot
--    - notes: Additional instructions or requirements
-- 3. Query using the v_tour_package_itinerary view for formatted display
--
-- Example query to view itinerary:
-- SELECT * FROM v_tour_package_itinerary WHERE tourPackage_ID = 1;
-- ============================================================================

-- End of database updates
