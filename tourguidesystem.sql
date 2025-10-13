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
    
    
    UNIQUE KEY unique_full_address (
    address_houseno, 
    address_street, 
    address_barangay, 
    address_city, 
    address_province, 
    address_country
);
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
    person_CivilStatus VARCHAR(225),
    person_DateOfBirth DATE,
    person_RatingScore DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    contactinfo_ID INT, 
    FOREIGN KEY (role_ID) REFERENCES Role_Info(role_ID),
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



