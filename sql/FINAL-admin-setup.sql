-- FINAL ADMIN SETUP - Complete from scratch
-- This is the definitive admin setup script

-- ============================================
-- STEP 1: Clean up old admin data
-- ============================================
DELETE FROM Account_Role WHERE login_ID IN (SELECT login_ID FROM User_Login WHERE username IN ('admin', 'admin_new'));
DELETE FROM User_Login WHERE username IN ('admin', 'admin_new');
DELETE FROM Person WHERE name_ID IN (SELECT name_ID FROM Name_Info WHERE name_first IN ('System', 'Admin'));
DELETE FROM Name_Info WHERE name_first IN ('System', 'Admin');

-- ============================================
-- STEP 2: Ensure all required data exists
-- ============================================

-- Ensure roles exist
DELETE FROM Role_Info;
INSERT INTO Role_Info (role_ID, role_name) VALUES 
(1, 'Admin'),
(2, 'Guide'),
(3, 'Tourist');

-- Ensure country exists
INSERT IGNORE INTO Country (country_name, country_codename, country_codenumber) 
VALUES ('Philippines', 'PH', '+63');

-- Ensure region exists
INSERT IGNORE INTO Region (region_name, country_ID) 
SELECT 'Zamboanga Peninsula', country_ID FROM Country WHERE country_name = 'Philippines' LIMIT 1;

-- Ensure province exists
INSERT IGNORE INTO Province (province_name, region_ID) 
SELECT 'Zamboanga del Sur', region_ID FROM Region WHERE region_name = 'Zamboanga Peninsula' LIMIT 1;

-- Ensure city exists
INSERT IGNORE INTO City (city_name, province_ID) 
SELECT 'Zamboanga City', province_ID FROM Province WHERE province_name = 'Zamboanga del Sur' LIMIT 1;

-- Ensure barangay exists
INSERT IGNORE INTO Barangay (barangay_name, city_ID) 
SELECT 'Zone 1', city_ID FROM City WHERE city_name = 'Zamboanga City' LIMIT 1;

-- Ensure phone exists
INSERT IGNORE INTO Phone_Number (country_ID, phone_number) 
SELECT country_ID, '0000000000' FROM Country WHERE country_name = 'Philippines' LIMIT 1;

-- Ensure emergency contact exists
INSERT IGNORE INTO Emergency_Info (emergency_Name, emergency_Relationship, phone_ID) 
SELECT 'N/A', 'N/A', phone_ID FROM Phone_Number WHERE phone_number = '0000000000' LIMIT 1;

-- Ensure address exists
INSERT IGNORE INTO Address_Info (address_houseno, address_street, barangay_ID) 
SELECT 'N/A', 'N/A', barangay_ID FROM Barangay WHERE barangay_name = 'Zone 1' LIMIT 1;

-- Ensure contact info exists
INSERT IGNORE INTO Contact_Info (address_ID, phone_ID, contactinfo_email, emergency_ID) 
SELECT 
    (SELECT address_ID FROM Address_Info WHERE address_houseno = 'N/A' AND address_street = 'N/A' LIMIT 1),
    (SELECT phone_ID FROM Phone_Number WHERE phone_number = '0000000000' LIMIT 1),
    'admin@tourismozamboanga.com',
    (SELECT emergency_ID FROM Emergency_Info WHERE emergency_Name = 'N/A' LIMIT 1);

-- ============================================
-- STEP 3: Create admin name
-- ============================================
INSERT INTO Name_Info (name_first, name_second, name_middle, name_last, name_suffix) 
VALUES ('Admin', NULL, NULL, 'Account', NULL);

-- ============================================
-- STEP 4: Create admin person
-- ============================================
INSERT INTO Person (name_ID, person_Nationality, person_Gender, person_DateOfBirth, contactinfo_ID) 
SELECT 
    name_ID,
    'Filipino',
    'Other',
    '1990-01-01',
    (SELECT contactinfo_ID FROM Contact_Info WHERE contactinfo_email = 'admin@tourismozamboanga.com' LIMIT 1)
FROM Name_Info 
WHERE name_first = 'Admin' AND name_last = 'Account' 
LIMIT 1;

-- ============================================
-- STEP 5: Create admin user login
-- ============================================
-- Password hash for "admin123"
INSERT INTO User_Login (person_ID, username, password_hash) 
SELECT 
    person_ID,
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
FROM Person 
WHERE name_ID = (SELECT name_ID FROM Name_Info WHERE name_first = 'Admin' AND name_last = 'Account' LIMIT 1)
LIMIT 1;

-- ============================================
-- STEP 6: Link admin to Admin role
-- ============================================
INSERT INTO Account_Role (login_ID, role_ID, is_approved) 
SELECT login_ID, 1, 1 
FROM User_Login 
WHERE username = 'admin'
LIMIT 1;

-- ============================================
-- STEP 7: Verify everything is correct
-- ============================================
SELECT '✓ ADMIN SETUP COMPLETE' as status;
SELECT '================================' as separator;

SELECT 'Admin Credentials:' as section;
SELECT 'Username: admin' as info;
SELECT 'Password: admin123' as info;

SELECT '================================' as separator;
SELECT 'Database Verification:' as section;

SELECT ul.login_ID, ul.username, ul.password_hash,
       p.person_ID, CONCAT(n.name_first, ' ', n.name_last) as full_name,
       ar.account_role_ID, ar.role_ID, ri.role_name, ar.is_approved
FROM User_Login ul
INNER JOIN Person p ON ul.person_ID = p.person_ID
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin';

SELECT '================================' as separator;
SELECT 'If you see the admin record above, login should work!' as note;
