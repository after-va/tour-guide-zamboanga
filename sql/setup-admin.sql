-- Setup script to create initial admin user
-- Run this after importing tourguidesystem.sql and tourguidesystem-data.sql

-- Insert default country
INSERT IGNORE INTO Country (country_name, country_codename, country_codenumber) 
VALUES ('Philippines', 'PH', '+63');

-- Insert default region
INSERT IGNORE INTO Region (region_name, country_ID) 
SELECT 'Zamboanga Peninsula', country_ID FROM Country WHERE country_name = 'Philippines' LIMIT 1;

-- Insert default province
INSERT IGNORE INTO Province (province_name, region_ID) 
SELECT 'Zamboanga del Sur', region_ID FROM Region WHERE region_name = 'Zamboanga Peninsula' LIMIT 1;

-- Insert default city
INSERT IGNORE INTO City (city_name, province_ID) 
SELECT 'Zamboanga City', province_ID FROM Province WHERE province_name = 'Zamboanga del Sur' LIMIT 1;

-- Insert default barangay
INSERT IGNORE INTO Barangay (barangay_name, city_ID) 
SELECT 'Zone 1', city_ID FROM City WHERE city_name = 'Zamboanga City' LIMIT 1;

-- Insert default phone (use INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO Phone_Number (country_ID, phone_number) 
SELECT country_ID, '0000000000' FROM Country WHERE country_name = 'Philippines' LIMIT 1;

-- Insert default emergency contact (use INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO Emergency_Info (emergency_Name, emergency_Relationship, phone_ID) 
SELECT 'N/A', 'N/A', phone_ID FROM Phone_Number WHERE phone_number = '0000000000' LIMIT 1;

-- Insert default address (use INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO Address_Info (address_houseno, address_street, barangay_ID) 
SELECT 'N/A', 'N/A', barangay_ID FROM Barangay WHERE barangay_name = 'Zone 1' LIMIT 1;

-- Insert default contact info
INSERT INTO Contact_Info (address_ID, phone_ID, contactinfo_email, emergency_ID) 
SELECT 
    (SELECT address_ID FROM Address_Info WHERE address_houseno = 'N/A' AND address_street = 'N/A' LIMIT 1),
    (SELECT phone_ID FROM Phone_Number WHERE phone_number = '0000000000' LIMIT 1),
    'admin@tourismozamboanga.com',
    (SELECT emergency_ID FROM Emergency_Info WHERE emergency_Name = 'N/A' LIMIT 1);

-- Insert admin name
INSERT INTO Name_Info (name_first, name_second, name_middle, name_last, name_suffix) 
VALUES ('System', NULL, NULL, 'Administrator', NULL);

-- Insert admin person
INSERT INTO Person (name_ID, person_Nationality, person_Gender, person_DateOfBirth, contactinfo_ID) 
SELECT 
    (SELECT name_ID FROM Name_Info WHERE name_first = 'System' AND name_last = 'Administrator' LIMIT 1),
    'Filipino',
    'Other',
    '1990-01-01',
    (SELECT contactinfo_ID FROM Contact_Info WHERE contactinfo_email = 'admin@tourismozamboanga.com' LIMIT 1);

-- Insert admin login (password: admin123)
INSERT INTO User_Login (person_ID, username, password_hash) 
SELECT 
    person_ID,
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
FROM Person 
WHERE name_ID = (SELECT name_ID FROM Name_Info WHERE name_first = 'System' AND name_last = 'Administrator' LIMIT 1)
LIMIT 1;

-- Link admin to role (Admin role_ID is 1)
INSERT INTO Account_Role (login_ID, role_ID) 
SELECT login_ID, 1 FROM User_Login WHERE username = 'admin' LIMIT 1;

-- Confirm creation
SELECT 'Admin user created successfully!' as message,
       'Username: admin' as username,
       'Password: admin123' as password,
       'Please change the password after first login!' as warning;
