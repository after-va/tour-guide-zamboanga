-- Country Code
CREATE TABLE Country_Code(
    countrycode_ID INT AUTO_INCREMENT PRIMARY KEY,
    countrycode_name VARCHAR(225),
    countrycode_number VARCHAR(5),
);


-- Phone Number
CREATE TABLE Phone_Number (
    phone_ID INT AUTO_INCREMENT PRIMARY KEY,
    countrycode_ID INT,
    phone_number VARCHAR(11) NOT NULL,
    FOREIGN KEY (countrycode_ID) REFERENCES Country_Code(countrycode_ID)
);


-- Address
CREATE TABLE Address_Info (
    address_ID INT AUTO_INCREMENT PRIMARY KEY,
    address_houseno VARCHAR(50) NOT NULL,
    address_street VARCHAR(50) NOT NULL,
    address_barangay VARCHAR(100) NOT NULL,
    address_city VARCHAR(100) NOT NULL,
    address_province VARCHAR(100) NOT NULL,
    address_country VARCHAR(100) NOT NULL,
);

-- Emergency Contact Info
CREATE TABLE Emergency_Info(
    emergency_ID INT AUTO_INCREMENT PRIMARY KEY,
    emergency_Name VARCHAR(225) NOT NULL,
    emergency_Relationship VARCHAR(225) NOT NULL,
    phone_ID INT,
    FOREIGN KEY (phone_ID) REFERENCES Phone_Number(phone_ID)
);

-- Contatct Info
CREATE TABLE Contact_Info(
    contactinfo_ID INT AUTO_INCREMENT PRIMARY KEY,
    address_ID INT,
    phone_ID INT,
    contactinfo_email VARCHAR(100) NOT NULL,
    emergency_ID INT,
    FOREIGN KEY(address_ID) REFERENCES Address_Info(address_ID),
    FOREIGN KEY (phone_ID) REFERENCES Phone_Number(phone_ID),
    FOREIGN KEY (emergency_ID) REFERENCES Emergency_Info(emergency_ID)
);


-- Name
CREATE TABLE Name_Info(
    name_ID INT AUTO_INCREMENT PRIMARY KEY,
    name_first VARCHAR(100) NOT NULL,
    name_second VARCHAR(225),
    name_middle VARCHAR(225),
    name_last VARCHAR(225) NOT NULL,
    name_suffix VARCHAR(225)

);


 -- Rating Category
CREATE TABLE Rating_Category(
    category_ID INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100),
    category_from VARCHAR(225), --From person Customer can Rate Tour Guide, Tour Spots and Tour Packages, Guide can only rate Customer
    category_to VARCHAR(225) -- To Tour Guide, Tour Spots, Tour Packages, Guide to Customer
);

--Rating
CREATE TABLE Rating(
    rating_ID INT AUTO_INCREMENT PRIMARY KEY,
    rating_from INT,
    rating_to INT, 
    rating_value DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    rating_description VARCHAR(225),
    category_ID INT,
    rating_date DATE,
);


-- Role
CREATE TABLE Role_Info(
    role_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(225)
);

--Person 
CREATE TABLE Person(
    person_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_ID INT,
    name_ID INT,
    person_Nationality VARCHAR (225),
    person_Gender VARCHAR(225),
    person_CivilStatus VARCHAR(225),
    person_DateOfBirth DATE,
    rating_ID INT,
    contactinfo_ID INT, 
    FOREIGN KEY (role_ID) REFERENCES Role_Info(role_ID),
    FOREIGN KEY (name_ID) REFERENCES Name_Info(name_ID),
    FOREIGN KEY (rating_ID) REFERENCES Rating(rating_ID),
    FOREIGN KEY (contactinfo_ID) REFERENCES Contact_Info(contactinfo_ID)
);