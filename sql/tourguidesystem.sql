
-- Country
CREATE TABLE Country (
    country_ID INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL UNIQUE,
    country_codename VARCHAR(10),
    country_codenumber VARCHAR(10)
);


-- Phone Number
CREATE TABLE Phone_Number (
    phone_ID INT AUTO_INCREMENT PRIMARY KEY,
    country_ID INT,
    phone_number VARCHAR(15) NOT NULL ,
    FOREIGN KEY (country_ID) REFERENCES Country(country_ID),
    UNIQUE KEY unique_phone_per_country (country_ID, phone_number)
);


-- Province
CREATE TABLE Province (
    province_ID INT AUTO_INCREMENT PRIMARY KEY,
    province_name VARCHAR(100) NOT NULL,
    country_ID INT NOT NULL,
    FOREIGN KEY (country_ID) REFERENCES Country(country_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_province_per_country (province_name, country_ID)
);

-- City
CREATE TABLE City (
    city_ID INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL,
    province_ID INT NOT NULL,
    FOREIGN KEY (province_ID) REFERENCES Province(province_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_city_per_province (city_name, province_ID)
);

-- Barangay
CREATE TABLE Barangay (
    barangay_ID INT AUTO_INCREMENT PRIMARY KEY,
    barangay_name VARCHAR(100) NOT NULL,
    city_ID INT NOT NULL,
    FOREIGN KEY (city_ID) REFERENCES City(city_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_barangay_per_city (barangay_name, city_ID)
);

-- Address
CREATE TABLE Address_Info (
    address_ID INT AUTO_INCREMENT PRIMARY KEY,
    address_houseno VARCHAR(50) NOT NULL,
    address_street VARCHAR(50) NOT NULL,
    barangay_ID INT NOT NULL,
    FOREIGN KEY (barangay_ID) REFERENCES Barangay(barangay_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_full_address (
        address_houseno,
        address_street,
        barangay_ID
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
    name_ID INT,
    person_Nationality VARCHAR(225),
    person_Gender VARCHAR(225),
    person_DateOfBirth DATE,
    contactinfo_ID INT,
    FOREIGN KEY (name_ID) REFERENCES Name_Info(name_ID),
    FOREIGN KEY (contactinfo_ID) REFERENCES Contact_Info(contactinfo_ID)
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
    tourPackage_Description VARCHAR(225),
    tourPackage_Capacity VARCHAR(50),
    tourPackage_Duration VARCHAR(50),
    spots_ID INT,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID)
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

-- Account Role - Links User_Login to Roles with separate rating scores
CREATE TABLE IF NOT EXISTS Account_Role (
    account_role_ID INT AUTO_INCREMENT PRIMARY KEY,
    login_ID INT NOT NULL,
    role_ID INT NOT NULL,
    role_rating_score DECIMAL(3,2) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (login_ID) REFERENCES User_Login(login_ID) ON DELETE CASCADE,
    FOREIGN KEY (role_ID) REFERENCES Role_Info(role_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_login_role (login_ID, role_ID)
);

-- Rating 
CREATE TABLE Rating (
    rating_ID INT AUTO_INCREMENT PRIMARY KEY,
    rater_account_role_ID INT NOT NULL,
    rated_account_role_ID INT NOT NULL,
    rating_value DECIMAL(2,1) NOT NULL,
    rating_description VARCHAR(255),
    rating_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rater_account_role_ID) REFERENCES Account_Role(account_role_ID),
    FOREIGN KEY (rated_account_role_ID) REFERENCES Account_Role(account_role_ID)
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




CREATE INDEX idx_booking_status ON Booking(booking_Status);
CREATE INDEX idx_booking_customer ON Booking(customer_ID);
CREATE INDEX idx_schedule_guide ON Schedule(guide_ID);
CREATE INDEX idx_schedule_date ON Schedule(schedule_StartDateTime);
CREATE INDEX idx_payment_booking ON Payment_Info(booking_ID);
CREATE INDEX idx_rating_rated ON Rating(rated_account_role_ID);
CREATE INDEX idx_rating_rater ON Rating(rater_account_role_ID);
CREATE INDEX idx_user_login_username ON User_Login(username);
CREATE INDEX idx_activity_log_user ON Activity_Log(user_ID);
CREATE INDEX idx_notifications_user ON Notifications(user_ID, is_read);
CREATE INDEX idx_account_role_login ON Account_Role(login_ID);
CREATE INDEX idx_account_role_role ON Account_Role(role_ID);
CREATE INDEX idx_province_country ON Province(country_ID);
CREATE INDEX idx_city_province ON City(province_ID);
CREATE INDEX idx_barangay_city ON Barangay(city_ID);
CREATE INDEX idx_address_barangay ON Address_Info(barangay_ID);

-- Create views for common queries
CREATE OR REPLACE VIEW v_user_details AS
SELECT 
    p.person_ID,
    ul.login_ID,
    ar.role_ID,
    r.role_name,
    ar.role_rating_score as rating,
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    n.name_first,
    n.name_last,
    ci.contactinfo_email as email,
    ph.phone_number,
    ul.username,
    ul.last_login,
    ul.is_active,
    ar.is_active as role_is_active
FROM Person p
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info r ON ar.role_ID = r.role_ID;

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

-- View for complete address with hierarchical location data
CREATE OR REPLACE VIEW v_address_complete AS
SELECT 
    a.address_ID,
    a.address_houseno,
    a.address_street,
    b.barangay_ID,
    b.barangay_name,
    c.city_ID,
    c.city_name,
    pr.province_ID,
    pr.province_name,
    co.country_ID,
    co.country_name,
    co.country_codenumber,
    CONCAT(a.address_houseno, ' ', a.address_street, ', ', 
           b.barangay_name, ', ', c.city_name, ', ', 
           pr.province_name, ', ', co.country_name) as full_address
FROM Address_Info a
INNER JOIN Barangay b ON a.barangay_ID = b.barangay_ID
INNER JOIN City c ON b.city_ID = c.city_ID
INNER JOIN Province pr ON c.province_ID = pr.province_ID
INNER JOIN Country co ON pr.country_ID = co.country_ID;

CREATE TABLE IF NOT EXISTS Package_Spots (
    package_spot_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_ID INT NOT NULL,
    spots_ID INT NOT NULL,
    spot_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_package_spot (tourPackage_ID, spots_ID)
);

CREATE INDEX idx_package_spots_package ON Package_Spots(tourPackage_ID);
CREATE INDEX idx_package_spots_spot ON Package_Spots(spots_ID);

INSERT INTO Package_Spots (tourPackage_ID, spots_ID, spot_order)
SELECT tourPackage_ID, spots_ID, 1
FROM Tour_Package
WHERE spots_ID IS NOT NULL;

-- Custom Package Requests
-- Allows tourists to request custom packages from specific guides
CREATE TABLE IF NOT EXISTS Custom_Package_Request (
    request_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourist_ID INT NOT NULL,
    guide_ID INT NOT NULL,
    tourPackage_ID INT, -- NULL if creating new, or references existing package to customize
    request_title VARCHAR(255) NOT NULL,
    request_description TEXT,
    preferred_date DATE,
    preferred_duration VARCHAR(50),
    number_of_pax INT,
    budget_range VARCHAR(100),
    special_requirements TEXT,
    request_status ENUM('pending', 'accepted', 'rejected', 'cancelled', 'completed') DEFAULT 'pending',
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tourist_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE SET NULL
);

-- Custom Package Spots - spots requested by tourist for custom package
CREATE TABLE IF NOT EXISTS Custom_Package_Spots (
    custom_spot_ID INT AUTO_INCREMENT PRIMARY KEY,
    request_ID INT NOT NULL,
    spots_ID INT NOT NULL,
    priority INT DEFAULT 0, -- 1=must visit, 2=would like to visit, 3=optional
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_ID) REFERENCES Custom_Package_Request(request_ID) ON DELETE CASCADE,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID) ON DELETE CASCADE
);

-- Guide Package Offerings
-- Packages created by guides for their services
CREATE TABLE IF NOT EXISTS Guide_Package_Offering (
    offering_ID INT AUTO_INCREMENT PRIMARY KEY,
    guide_ID INT NOT NULL,
    tourPackage_ID INT NOT NULL,
    offering_price DECIMAL(10,2) NOT NULL,
    price_per_person DECIMAL(10,2),
    min_pax INT DEFAULT 1,
    max_pax INT,
    is_customizable TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    availability_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_guide_package (guide_ID, tourPackage_ID)
);

-- Package Request Messages - communication between tourist and guide
CREATE TABLE IF NOT EXISTS Package_Request_Messages (
    message_ID INT AUTO_INCREMENT PRIMARY KEY,
    request_ID INT NOT NULL,
    sender_ID INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_ID) REFERENCES Custom_Package_Request(request_ID) ON DELETE CASCADE,
    FOREIGN KEY (sender_ID) REFERENCES Person(person_ID) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_custom_request_tourist ON Custom_Package_Request(tourist_ID);
CREATE INDEX idx_custom_request_guide ON Custom_Package_Request(guide_ID);
CREATE INDEX idx_custom_request_status ON Custom_Package_Request(request_status);
CREATE INDEX idx_guide_offering_guide ON Guide_Package_Offering(guide_ID);
CREATE INDEX idx_guide_offering_active ON Guide_Package_Offering(is_active);


