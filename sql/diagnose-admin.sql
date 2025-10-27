-- Diagnostic script to check admin account setup

SELECT '=== CHECKING ADMIN ACCOUNT ===' as status;

-- Check if admin user exists
SELECT 'User_Login table:' as section;
SELECT login_ID, person_ID, username FROM User_Login WHERE username = 'admin';

-- Check if admin person exists
SELECT 'Person table:' as section;
SELECT p.person_ID, n.name_first, n.name_last, p.contactinfo_ID
FROM Person p
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
WHERE p.person_ID IN (SELECT person_ID FROM User_Login WHERE username = 'admin');

-- Check if admin has Account_Role
SELECT 'Account_Role table:' as section;
SELECT ar.account_role_ID, ar.login_ID, ar.role_ID, ar.is_approved, ri.role_name
FROM Account_Role ar
LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ar.login_ID IN (SELECT login_ID FROM User_Login WHERE username = 'admin');

-- Test the exact login query used by the app
SELECT 'Testing login query:' as section;
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

-- Check if roles exist
SELECT 'Role_Info table:' as section;
SELECT role_ID, role_name FROM Role_Info ORDER BY role_ID;
