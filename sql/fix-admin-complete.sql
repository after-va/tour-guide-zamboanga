-- Complete fix for admin login issue
-- Run this after all other migrations

-- Step 1: Ensure is_approved column exists with correct default
ALTER TABLE Account_Role ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1;

-- Step 2: Update any NULL is_approved values to 1
UPDATE Account_Role SET is_approved = 1 WHERE is_approved IS NULL;

-- Step 3: Ensure admin has Account_Role entry
INSERT IGNORE INTO Account_Role (login_ID, role_ID, is_approved) 
SELECT ul.login_ID, 1, 1 
FROM User_Login ul 
WHERE ul.username = 'admin' 
AND ul.login_ID NOT IN (SELECT login_ID FROM Account_Role WHERE role_ID = 1);

-- Step 4: Verify admin setup
SELECT 'Admin Login Verification:' as status;
SELECT 
    ul.login_ID,
    ul.username,
    ul.person_ID,
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    ar.role_ID,
    ri.role_name,
    ar.is_approved,
    'Ready to login' as status
FROM User_Login ul
LEFT JOIN Person p ON ul.person_ID = p.person_ID
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin';

-- Step 5: Test the login query that the app uses
SELECT 'Testing login query:' as status;
SELECT ul.login_ID, ul.username, p.person_ID, 
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    ar.account_role_ID, ar.role_ID, ri.role_name
FROM User_Login ul
INNER JOIN Person p ON ul.person_ID = p.person_ID
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin'
LIMIT 1;
