-- Fix admin login issue by ensuring admin has Account_Role entry

-- First, check if admin exists
SELECT 'Checking admin user...' as status;

-- Add Account_Role for admin if it doesn't exist
INSERT IGNORE INTO Account_Role (login_ID, role_ID, is_approved) 
SELECT ul.login_ID, 1, 1 
FROM User_Login ul 
WHERE ul.username = 'admin' 
AND ul.login_ID NOT IN (SELECT login_ID FROM Account_Role WHERE role_ID = 1);

-- Verify admin now has role
SELECT 'Admin role assignment:' as status;
SELECT ul.login_ID, ul.username, ar.role_ID, ri.role_name, ar.is_approved
FROM User_Login ul
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin';
