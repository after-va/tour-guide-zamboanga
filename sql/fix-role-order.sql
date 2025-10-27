-- Fix the role order - Admin should be role_ID 1, not Tourist

-- Delete the incorrect roles
DELETE FROM Role_Info WHERE role_ID IN (1, 2, 3);

-- Re-insert with correct order
INSERT INTO Role_Info (role_ID, role_name) VALUES 
(1, 'Admin'),
(2, 'Guide'),
(3, 'Tourist');

-- Verify
SELECT 'Corrected Role_Info:' as status;
SELECT role_ID, role_name FROM Role_Info ORDER BY role_ID;
