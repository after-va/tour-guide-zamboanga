-- Create Admin User Properly
-- This fixes the Contact_Info foreign key issue

-- Step 0: Ensure Philippines country code exists
INSERT IGNORE INTO Country_Code (countrycode_name, countrycode_number) 
VALUES ('Philippines', '+63');

-- Get Philippines country code ID
SET @ph_country_code = (SELECT countrycode_ID FROM Country_Code WHERE countrycode_name = 'Philippines' LIMIT 1);

-- Step 1: Create a dummy phone number for admin
INSERT INTO Phone_Number (countrycode_ID, phone_number) 
VALUES (@ph_country_code, '9123456789');
SET @admin_phone_id = LAST_INSERT_ID();

-- Step 2: Create a dummy address for admin
INSERT INTO Address_Info (address_houseno, address_street, address_barangay, address_city, address_province, address_country)
VALUES ('N/A', 'N/A', 'N/A', 'Zamboanga City', 'Zamboanga del Sur', 'Philippines');
SET @admin_address_id = LAST_INSERT_ID();

-- Step 3: Create emergency contact
INSERT INTO Emergency_Info (phone_ID, emergency_Name, emergency_Relationship)
VALUES (@admin_phone_id, 'System Admin', 'Self');
SET @admin_emergency_id = LAST_INSERT_ID();

-- Step 4: Create contact info with all required fields
INSERT INTO Contact_Info (address_ID, phone_ID, emergency_ID, contactinfo_email)
VALUES (@admin_address_id, @admin_phone_id, @admin_emergency_id, 'admin@tourismozamboanga.com');
SET @admin_contact_id = LAST_INSERT_ID();

-- Step 5: Create name info
INSERT INTO Name_Info (name_first, name_last) 
VALUES ('System', 'Administrator');
SET @admin_name_id = LAST_INSERT_ID();

-- Step 6: Create person record
INSERT INTO Person (role_ID, name_ID, contactinfo_ID, person_Nationality, person_Gender, person_DateOfBirth) 
VALUES (1, @admin_name_id, @admin_contact_id, 'Filipino', 'Male', '1990-01-01');
SET @admin_person_id = LAST_INSERT_ID();

-- Step 7: Create login credentials
-- Password: admin123
INSERT INTO User_Login (person_ID, username, password_hash, is_active) 
VALUES (@admin_person_id, 'admin@tourismozamboanga.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Verify the admin user was created
SELECT 'Admin user created successfully!' as Status;
SELECT p.person_ID, n.name_first, n.name_last, r.role_name, ul.username, ul.is_active
FROM Person p
JOIN Name_Info n ON p.name_ID = n.name_ID
JOIN Role_Info r ON p.role_ID = r.role_ID
JOIN User_Login ul ON p.person_ID = ul.person_ID
WHERE ul.username = 'admin@tourismozamboanga.com';
