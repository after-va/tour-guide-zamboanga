-- Create a fresh admin account from scratch
-- This script handles everything needed for a working admin account

-- Step 1: Ensure roles are correct
DELETE FROM Role_Info WHERE role_ID IN (1, 2, 3);
INSERT INTO Role_Info (role_ID, role_name) VALUES 
(1, 'Admin'),
(2, 'Guide'),
(3, 'Tourist');

-- Step 2: Get or create the default country
INSERT IGNORE INTO Country (country_name, country_codename, country_codenumber) 
VALUES ('Philippines', 'PH', '+63');

-- Step 3: Get or create default region
INSERT IGNORE INTO Region (region_name, country_ID) 
SELECT 'Zamboanga Peninsula', country_ID FROM Country WHERE country_name = 'Philippines' LIMIT 1;

-- Step 4: Get or create default province
INSERT IGNORE INTO Province (province_name, region_ID) 
SELECT 'Zamboanga del Sur', region_ID FROM Region WHERE region_name = 'Zamboanga Peninsula' LIMIT 1;

-- Step 5: Get or create default city
INSERT IGNORE INTO City (city_name, province_ID) 
SELECT 'Zamboanga City', province_ID FROM Province WHERE province_name = 'Zamboanga del Sur' LIMIT 1;

-- Step 6: Get or create default barangay
INSERT IGNORE INTO Barangay (barangay_name, city_ID) 
SELECT 'Zone 1', city_ID FROM City WHERE city_name = 'Zamboanga City' LIMIT 1;

-- Step 7: Get or create default phone
INSERT IGNORE INTO Phone_Number (country_ID, phone_number) 
SELECT country_ID, '0000000000' FROM Country WHERE country_name = 'Philippines' LIMIT 1;

-- Step 8: Get or create default emergency contact
INSERT IGNORE INTO Emergency_Info (emergency_Name, emergency_Relationship, phone_ID) 
SELECT 'N/A', 'N/A', phone_ID FROM Phone_Number WHERE phone_number = '0000000000' LIMIT 1;

-- Step 9: Get or create default address
INSERT IGNORE INTO Address_Info (address_houseno, address_street, barangay_ID) 
SELECT 'N/A', 'N/A', barangay_ID FROM Barangay WHERE barangay_name = 'Zone 1' LIMIT 1;

-- Step 10: Get or create default contact info
INSERT IGNORE INTO Contact_Info (address_ID, phone_ID, contactinfo_email, emergency_ID) 
SELECT 
    (SELECT address_ID FROM Address_Info WHERE address_houseno = 'N/A' AND address_street = 'N/A' LIMIT 1),
    (SELECT phone_ID FROM Phone_Number WHERE phone_number = '0000000000' LIMIT 1),
    'admin@tourismozamboanga.com',
    (SELECT emergency_ID FROM Emergency_Info WHERE emergency_Name = 'N/A' LIMIT 1);

-- Step 11: Create admin name
INSERT IGNORE INTO Name_Info (name_first, name_second, name_middle, name_last, name_suffix) 
VALUES ('Admin', NULL, NULL, 'User', NULL);

-- Step 12: Create admin person
INSERT IGNORE INTO Person (name_ID, person_Nationality, person_Gender, person_DateOfBirth, contactinfo_ID) 
SELECT 
    (SELECT name_ID FROM Name_Info WHERE name_first = 'Admin' AND name_last = 'User' LIMIT 1),
    'Filipino',
    'Other',
    '1990-01-01',
    (SELECT contactinfo_ID FROM Contact_Info WHERE contactinfo_email = 'admin@tourismozamboanga.com' LIMIT 1);

-- Step 13: Delete old admin user if exists
DELETE FROM User_Login WHERE username = 'admin_new';

-- Step 14: Create new admin login (password: admin123)
INSERT INTO User_Login (person_ID, username, password_hash) 
SELECT 
    person_ID,
    'admin_new',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
FROM Person 
WHERE name_ID = (SELECT name_ID FROM Name_Info WHERE name_first = 'Admin' AND name_last = 'User' LIMIT 1)
LIMIT 1;

-- Step 15: Delete old admin role if exists
DELETE FROM Account_Role WHERE login_ID IN (SELECT login_ID FROM User_Login WHERE username = 'admin_new');

-- Step 16: Link new admin to role
INSERT INTO Account_Role (login_ID, role_ID, is_approved) 
SELECT login_ID, 1, 1 FROM User_Login WHERE username = 'admin_new' LIMIT 1;

-- Step 17: Verify everything
SELECT '=== ADMIN ACCOUNT CREATED ===' as status;
SELECT 'New admin username: admin_new' as info;
SELECT 'Password: admin123' as info;

SELECT 'Verification:' as section;
SELECT ul.login_ID, ul.username, p.person_ID, 
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    ar.account_role_ID, ar.role_ID, ri.role_name, ar.is_approved
FROM User_Login ul
INNER JOIN Person p ON ul.person_ID = p.person_ID
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin_new'
LIMIT 1;
