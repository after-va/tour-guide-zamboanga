--  Country
CREATE TABLE Country (
    country_ID INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL UNIQUE,
    country_codename VARCHAR(10),
    country_codenumber VARCHAR(10)
);


--  Phone Number
CREATE TABLE Phone_Number (
    phone_ID INT AUTO_INCREMENT PRIMARY KEY,
    country_ID INT,
    phone_number VARCHAR(15) NOT NULL ,
    FOREIGN KEY (country_ID) REFERENCES Country(country_ID),
    UNIQUE KEY unique_phone_per_country (country_ID, phone_number)
);

--  Region 
CREATE TABLE Region (
    region_ID INT AUTO_INCREMENT PRIMARY KEY,
    region_name VARCHAR(100) NOT NULL,
    country_ID INT NOT NULL,
    FOREIGN KEY (country_ID) REFERENCES Country(country_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_region_per_country (region_name, country_ID)
);

--  Province
CREATE TABLE Province (
    province_ID INT AUTO_INCREMENT PRIMARY KEY,
    province_name VARCHAR(100) NOT NULL,
    region_ID INT NOT NULL,
    FOREIGN KEY (region_ID) REFERENCES Region(region_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_province_per_region (province_name, region_ID)
);

--  City
CREATE TABLE City (
    city_ID INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL,
    province_ID INT NOT NULL,
    FOREIGN KEY (province_ID) REFERENCES Province(province_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_city_per_province (city_name, province_ID)
);

--  Barangay
CREATE TABLE Barangay (
    barangay_ID INT AUTO_INCREMENT PRIMARY KEY,
    barangay_name VARCHAR(100) NOT NULL,
    city_ID INT NOT NULL,
    FOREIGN KEY (city_ID) REFERENCES City(city_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_barangay_per_city (barangay_name, city_ID)
);

--  Address
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

--  Emergency Contact Info
CREATE TABLE Emergency_Info (
    emergency_ID INT AUTO_INCREMENT PRIMARY KEY,
    emergency_Name VARCHAR(225) NOT NULL,
    emergency_Relationship VARCHAR(225) NOT NULL,
    phone_ID INT,
    FOREIGN KEY (phone_ID) REFERENCES Phone_Number(phone_ID)
);

--  Contact Info
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

--  Name Info
CREATE TABLE Name_Info (
    name_ID INT AUTO_INCREMENT PRIMARY KEY,
    name_first VARCHAR(100) NOT NULL,
    name_second VARCHAR(225),
    name_middle VARCHAR(225),
    name_last VARCHAR(225) NOT NULL,
    name_suffix VARCHAR(225)
);

--  Person
CREATE TABLE Person (
    person_ID INT AUTO_INCREMENT PRIMARY KEY,
    name_ID INT,
    contactinfo_ID INT,
    person_Nationality VARCHAR(225),
    person_Gender VARCHAR(225),
    person_DateOfBirth DATE,
    FOREIGN KEY (name_ID) REFERENCES Name_Info(name_ID),
    FOREIGN KEY (contactinfo_ID) REFERENCES Contact_Info(contactinfo_ID)
);


--  User
CREATE TABLE User_Login(
    user_ID INT AUTO_INCREMENT PRIMARY KEY,
    person_ID INT,
    user_username VARCHAR(100) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    FOREIGN KEY (person_ID) REFERENCES Person(person_ID)
);

--  Role 
CREATE TABLE Role (
    role_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL UNIQUE
);

--  Account Info
CREATE TABLE Account_Info (
    account_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT,
    role_ID INT,
    account_status ENUM('Active', 'Suspended', 'Pending'),
    account_rating_score DECIMAL(3,2) DEFAULT 0.00,
    account_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_ID) REFERENCES User_Login(user_ID),
    FOREIGN KEY (role_ID) REFERENCES Role(role_ID)
);

CREATE TABLE Action (
    action_ID INT AUTO_INCREMENT PRIMARY KEY,
    action_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Activity_Log (
    activity_ID INT AUTO_INCREMENT PRIMARY KEY,
    account_ID INT,
    action_ID INT,
    activity_description TEXT,
    activity_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_ID) REFERENCES Account_Info(account_ID),
    FOREIGN KEY (action_ID) REFERENCES Action(action_ID)
);

--  ==============================
--  ADMIN SYSTEM TABLES
--  ==============================

CREATE TABLE Admin(
    admin_ID INT AUTO_INCREMENT PRIMARY KEY,
    account_ID INT,
    admin_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_ID) REFERENCES Account_Info(account_ID)
);

--  ==============================
--  GUIDE SYSTEM TABLES
-- ============================== 

CREATE TABLE Guide_License(
    lisence_ID INT AUTO_INCREMENT PRIMARY KEY,
    lisence_number VARCHAR(100) NOT NULL UNIQUE,
    lisence_created_date DATE NOT NULL,
    lisence_issued_date DATE NOT NULL,
    lisence_issued_by VARCHAR(225) NOT NULL,
    lisence_expiry_date DATE NOT NULL,
    lisence_verification_status VARCHAR(50) NOT NULL,
    lisence_status VARCHAR(50) NOT NULL
);

CREATE TABLE Languages(
    languages_ID INT AUTO_INCREMENT PRIMARY KEY,
    language_name ENUM('English', 'Chavacano', 'Filipino') NOT NULL UNIQUE
);

CREATE TABLE Guide(
    guide_ID INT AUTO_INCREMENT PRIMARY KEY,
    account_ID INT,
    lisence_ID INT,
    FOREIGN KEY (account_ID) REFERENCES Account_Info(account_ID),
    FOREIGN KEY (lisence_ID) REFERENCES Guide_License(lisence_ID)
);

CREATE TABLE Guide_Languages(
    guide_ID INT,
    languages_ID INT,
    PRIMARY KEY (guide_ID, languages_ID),
    FOREIGN KEY (guide_ID) REFERENCES Guide(guide_ID) ON DELETE CASCADE,
    FOREIGN KEY (languages_ID) REFERENCES Languages(languages_ID) ON DELETE CASCADE
);


-- ==============================
--  SCHEDULE SYSTEM TABLES
-- ==============================

CREATE TABLE Pricing(
    pricing_ID INT AUTO_INCREMENT PRIMARY KEY,
    pricing_currency VARCHAR(10) NOT NULL,
    pricing_based DECIMAL(10,2) NOT NULL,
    pricing_discount DECIMAL(10,2) NOT NULL,
    pricing_total DECIMAL(10,2) NOT NULL
);

CREATE TABLE Number_Of_People(
    numberofpeople_ID INT AUTO_INCREMENT PRIMARY KEY,
    pricing_ID INT,
    numberofpeople_adult INT NOT NULL,
    numberofpeople_children INT NOT NULL,
    numberofpeople_maximum INT NOT NULL,
    numberofpeople_based VARCHAR(50) NOT NULL,
    FOREIGN KEY (pricing_ID) REFERENCES Pricing(pricing_ID)
);

CREATE TABLE Schedule(
    schedule_ID INT AUTO_INCREMENT PRIMARY KEY,
    numberofpeople_ID INT,
    schedule_date DATE NOT NULL,
    schedule_start_time TIME NOT NULL,
    schedule_end_time TIME NOT NULL,
    FOREIGN KEY (numberofpeople_ID) REFERENCES Number_Of_People(numberofpeople_ID)    
);

-- ==============================
--  TOURIST SPOTS SYSTEM TABLES
-- ==============================

--  Tour Spot
CREATE TABLE Tour_Spots(
    spots_ID INT AUTO_INCREMENT PRIMARY KEY,
    spots_name VARCHAR(225) NOT NULL,
    spots_category  VARCHAR(225) NOT NULL,
    spots_description TEXT,
    spots_address VARCHAR(500) NOT NULL,
    spots_googlelink VARCHAR(500)

);


--  Tour Packages
CREATE TABLE Tour_Package(
    tourpackage_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourpackage_name VARCHAR(225) NOT NULL,
    tourpackage_desc TEXT,
    guide_ID INT,
    schedule_ID INT,
    FOREIGN KEY (schedule_ID) REFERENCES Schedule(schedule_ID),
    FOREIGN KEY (guide_ID) REFERENCES Guide(guide_ID)
);

--  Tour Package Spots (Many-to-Many Relationship)
CREATE TABLE Tour_Package_Spots(
    tourpackage_ID INT,
    spots_ID INT,
    PRIMARY KEY (tourpackage_ID, spots_ID),
    FOREIGN KEY (tourpackage_ID) REFERENCES Tour_Package(tourpackage_ID) ON DELETE CASCADE,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID) ON DELETE CASCADE
);

CREATE TABLE Request_Package(
    request_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourpackage_ID INT,
    request_status VARCHAR(50) NOT NULL,
    rejection_reason TEXT,
    request_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    request_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tourpackage_ID) REFERENCES Tour_Package(tourpackage_ID)
);



-- ==============================
--  BOOKING SYSTEM TABLES
-- ==============================

CREATE TABLE Booking(
    booking_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourist_ID INT,
    booking_status VARCHAR(50) NOT NULL,
    booking_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tourpackage_ID INT,
    FOREIGN KEY (tourpackage_ID) REFERENCES Tour_Package(tourpackage_ID),
    FOREIGN KEY (tourist_ID) REFERENCES Account_Info(account_ID)
);

CREATE TABLE Companion_Category(
    companion_category_ID INT AUTO_INCREMENT PRIMARY KEY,
    companion_category_name ENUM('Adult', 'Children', 'Senior', 'PWD') NOT NULL UNIQUE
);

CREATE TABLE Companion(
    companion_ID INT AUTO_INCREMENT PRIMARY KEY,
    companion_name VARCHAR(225) NOT NULL,
    companion_category_ID INT,
    FOREIGN KEY (companion_category_ID) REFERENCES Companion_Category(companion_category_ID)
);

CREATE TABLE Booking_Bundle(
    bookingbundle_ID INT AUTO_INCREMENT PRIMARY KEY,
    booking_ID INT,
    companion_ID INT,
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID),
    FOREIGN KEY (companion_ID) REFERENCES Companion(companion_ID)
);


-- ==============================
--  PAYMENT SYSTEM TABLES
-- ==============================

CREATE TABLE Payment_Info(
    paymentinfo_ID INT AUTO_INCREMENT PRIMARY KEY,
    booking_ID INT,
    paymentinfo_total_amount DECIMAL(10,2) NOT NULL,
    paymentinfo_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID)
);

CREATE TABLE Method(
    method_ID INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(100) NOT NULL UNIQUE,
    method_type VARCHAR(100) NOT NULL,
    method_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    method_processing_fee DECIMAL(10,2) NOT NULL,
    method_is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE Payment_Transaction(
    transaction_ID INT AUTO_INCREMENT PRIMARY KEY,
    paymentinfo_ID INT,
    method_ID INT,
    transaction_status VARCHAR(50) NOT NULL,
    transaction_reference VARCHAR(100) NOT NULL UNIQUE,
    transaction_created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaction_updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paymentinfo_ID) REFERENCES Payment_Info(paymentinfo_ID),
    FOREIGN KEY (method_ID) REFERENCES Method(method_ID)
);

CREATE TABLE Refund(
    refund_ID INT AUTO_INCREMENT PRIMARY KEY,
    transaction_ID INT,
    refund_reason TEXT,
    refund_status VARCHAR(50) NOT NULL,
    refund_requested_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    refund_approval_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    refund_fee DECIMAL(10,2),
    refund_total_amount DECIMAL(10,2),
    FOREIGN KEY (transaction_ID) REFERENCES Payment_Transaction(transaction_ID)
);

-- ==============================
--  RATING SYSTEM TABLES
-- ==============================

CREATE TABLE Rating(
    rating_ID INT AUTO_INCREMENT PRIMARY KEY,
    rater_account_ID INT,
    rating_type ENUM('Tourist', 'Guide', 'Tour Spots', 'Tour Package') NOT NULL,
    rating_account_ID INT,
    rating_tourpackage_ID INT,
    rating_tourspots_ID INT,
    rating_value DECIMAL(2,1) NOT NULL,
    rating_description TEXT,
    rating_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rater_account_ID) REFERENCES Account_Info(account_ID),
    FOREIGN KEY (rating_account_ID) REFERENCES Account_Info(account_ID),
    FOREIGN KEY (rating_tourpackage_ID) REFERENCES Tour_Package(tourpackage_ID),
    FOREIGN KEY (rating_tourspots_ID) REFERENCES Tour_Spots(spots_ID)
);
 
 CREATE TABLE Review_Image(
    review_ID INT AUTO_INCREMENT PRIMARY KEY,
    rating_ID INT,
    review_image_path VARCHAR(255) NOT NULL,
    review_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rating_ID) REFERENCES Rating(rating_ID)
 );

-- ==============================
--  VIEW SYSTEM TABLES
-- ==============================







