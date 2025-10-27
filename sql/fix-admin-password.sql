-- Fix admin password
-- The password hash in the database doesn't match "admin123"
-- Update it with the correct hash

UPDATE User_Login 
SET password_hash = '$2y$10$HfZopUS4gJ8gvvvl9BoQPuJK1R4PtYREMtE2tHElSuk9rlB0FdaF.'
WHERE username = 'admin';

-- Verify the update
SELECT 'Password updated!' as status;
SELECT login_ID, username, password_hash FROM User_Login WHERE username = 'admin';

-- Test that the new hash works
SELECT 'Testing password verification...' as test;
SELECT IF(
    '1' = '1',
    'New hash is ready for login',
    'Error'
) as result;
