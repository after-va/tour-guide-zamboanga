-- Setup script to create initial admin user
-- Run this after importing tourguidesystem.sql and tourguidesystem-data.sql

-- Insert default country
INSERT INTO Country (country_name, country_codename, country_codenumber) 
VALUES ('Philippines', 'PH', '+63')
ON DUPLICATE KEY UPDATE country_name = 'Philippines';

SET @country_id = LAST_INSERT_ID();
IF @country_id = 0 THEN
    SELECT country_ID INTO @country_id FROM Country WHERE country_name = 'Philippines' LIMIT 1;
END IF;

-- Insert default region
INSERT INTO Region (region_name, country_ID) 
VALUES ('Zamboanga Peninsula', @country_id)
ON DUPLICATE KEY UPDATE region_name = 'Zamboanga Peninsula';

SET @region_id = LAST_INSERT_ID();
IF @region_id = 0 THEN
    SELECT region_ID INTO @region_id FROM Region WHERE region_name = 'Zamboanga Peninsula' LIMIT 1;
END IF;

-- Insert default province
INSERT INTO Province (province_name, region_ID) 
VALUES ('Zamboanga del Sur', @region_id)
ON DUPLICATE KEY UPDATE province_name = 'Zamboanga del Sur';

SET @province_id = LAST_INSERT_ID();
IF @province_id = 0 THEN
    SELECT province_ID INTO @province_id FROM Province WHERE province_name = 'Zamboanga del Sur' LIMIT 1;
END IF;

-- Insert default city
INSERT INTO City (city_name, province_ID) 
VALUES ('Zamboanga City', @province_id)
ON DUPLICATE KEY UPDATE city_name = 'Zamboanga City';

SET @city_id = LAST_INSERT_ID();
IF @city_id = 0 THEN
    SELECT city_ID INTO @city_id FROM City WHERE city_name = 'Zamboanga City' LIMIT 1;
END IF;

-- Insert default barangay
INSERT INTO Barangay (barangay_name, city_ID) 
VALUES ('Zone 1', @city_id)
ON DUPLICATE KEY UPDATE barangay_name = 'Zone 1';

SET @barangay_id = LAST_INSERT_ID();
IF @barangay_id = 0 THEN
    SELECT barangay_ID INTO @barangay_id FROM Barangay WHERE barangay_name = 'Zone 1' LIMIT 1;
END IF;

-- Insert default address
INSERT INTO Address_Info (address_houseno, address_street, barangay_ID) 
VALUES ('N/A', 'N/A', @barangay_id);

SET @address_id = LAST_INSERT_ID();

-- Insert default phone
INSERT INTO Phone_Number (country_ID, phone_number) 
VALUES (@country_id, '0000000000');

SET @phone_id = LAST_INSERT_ID();

-- Insert default emergency contact
INSERT INTO Emergency_Info (emergency_Name, emergency_Relationship, phone_ID) 
VALUES ('N/A', 'N/A', @phone_id);

SET @emergency_id = LAST_INSERT_ID();

-- Insert default contact info
INSERT INTO Contact_Info (address_ID, phone_ID, contactinfo_email, emergency_ID) 
VALUES (@address_id, @phone_id, 'admin@tourismozamboanga.com', @emergency_id);

SET @contactinfo_id = LAST_INSERT_ID();

-- Insert admin name
INSERT INTO Name_Info (name_first, name_second, name_middle, name_last, name_suffix) 
VALUES ('System', NULL, NULL, 'Administrator', NULL);

SET @name_id = LAST_INSERT_ID();

-- Insert admin person
INSERT INTO Person (name_ID, person_Nationality, person_Gender, person_DateOfBirth, contactinfo_ID) 
VALUES (@name_id, 'Filipino', 'Other', '1990-01-01', @contactinfo_id);

SET @person_id = LAST_INSERT_ID();

-- Insert admin login (password: admin123)
INSERT INTO User_Login (person_ID, username, password_hash) 
VALUES (@person_id, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SET @login_id = LAST_INSERT_ID();

-- Insert admin role
INSERT INTO Role_Info (role_name) 
VALUES ('Admin')
ON DUPLICATE KEY UPDATE role_name = 'Admin';

SET @role_id = 3; -- Admin role is always 3

-- Link admin to role
INSERT INTO Account_Role (login_ID, role_ID) 
VALUES (@login_id, @role_id);

-- Confirm creation
SELECT 'Admin user created successfully!' as message,
       'Username: admin' as username,
       'Password: admin123' as password,
       'Please change the password after first login!' as warning;
