-- Ensure all required roles exist in Role_Info table

INSERT IGNORE INTO Role_Info (role_ID, role_name) VALUES 
(1, 'Admin'),
(2, 'Guide'),
(3, 'Tourist');

-- Verify roles
SELECT 'Roles in database:' as status;
SELECT role_ID, role_name FROM Role_Info ORDER BY role_ID;
