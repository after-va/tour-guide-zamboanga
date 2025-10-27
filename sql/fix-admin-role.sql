-- Fix admin user to have Admin role (role_ID = 1)

-- Update the admin user's role from 3 to 1
UPDATE Account_Role 
SET role_ID = 1 
WHERE login_ID = (SELECT login_ID FROM User_Login WHERE username = 'admin' LIMIT 1);

-- Verify the fix
SELECT 'Admin role updated successfully!' as message;

SELECT ul.username, ri.role_name, ar.role_ID
FROM User_Login ul
JOIN Account_Role ar ON ul.login_ID = ar.login_ID
JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin';
