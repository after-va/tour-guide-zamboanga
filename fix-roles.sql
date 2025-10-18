-- Fix for Role_Info table
-- The roles are already inserted in tourguidesystem.sql
-- This file is just for reference

-- Roles should be:
-- 1 = Admin
-- 2 = Tour Guide  
-- 3 = Tourist

-- If roles are missing, run this:
INSERT INTO Role_Info (role_ID, role_name) VALUES 
(1, 'Admin'),
(2, 'Tour Guide'),
(3, 'Tourist')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);
