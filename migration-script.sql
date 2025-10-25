-- Migration Script for Account_Role Changes
-- This script migrates existing data from the old schema to the new schema
-- Run this AFTER creating the new Account_Role table

-- WARNING: Backup your database before running this script!

-- Step 1: Migrate existing Person role_ID to Account_Role table
-- This creates Account_Role entries for all existing users
INSERT INTO Account_Role (login_ID, role_ID, role_rating_score, is_active)
SELECT 
    ul.login_ID,
    p.role_ID,
    p.person_RatingScore,
    1 as is_active
FROM Person p
INNER JOIN User_Login ul ON p.person_ID = ul.person_ID
WHERE p.role_ID IS NOT NULL;

-- Step 2: Update existing Rating records to use account_role_ID
-- First, add temporary columns to store the mapping
ALTER TABLE Rating ADD COLUMN temp_rater_person_ID INT;
ALTER TABLE Rating ADD COLUMN temp_rated_person_ID INT;

-- Store the old person IDs temporarily
UPDATE Rating r
SET temp_rater_person_ID = r.rater_ID,
    temp_rated_person_ID = r.rated_ID;

-- Update rater_account_role_ID
UPDATE Rating r
INNER JOIN User_Login ul ON r.temp_rater_person_ID = ul.person_ID
INNER JOIN Account_Role ar ON ul.login_ID = ar.login_ID
SET r.rater_account_role_ID = ar.account_role_ID;

-- Update rated_account_role_ID
UPDATE Rating r
INNER JOIN User_Login ul ON r.temp_rated_person_ID = ul.person_ID
INNER JOIN Account_Role ar ON ul.login_ID = ar.login_ID
SET r.rated_account_role_ID = ar.account_role_ID;

-- Drop temporary columns
ALTER TABLE Rating DROP COLUMN temp_rater_person_ID;
ALTER TABLE Rating DROP COLUMN temp_rated_person_ID;

-- Step 3: Drop old columns from Person table (ONLY after verifying migration)
-- UNCOMMENT THESE LINES AFTER VERIFYING THE MIGRATION WAS SUCCESSFUL:
-- ALTER TABLE Person DROP FOREIGN KEY Person_ibfk_3; -- or whatever the constraint name is for role_ID
-- ALTER TABLE Person DROP COLUMN role_ID;
-- ALTER TABLE Person DROP COLUMN person_RatingScore;

-- Step 4: Verify migration
-- Check that all users have Account_Role entries
SELECT 
    'Users without Account_Role' as check_type,
    COUNT(*) as count
FROM User_Login ul
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
WHERE ar.account_role_ID IS NULL;

-- Check that all ratings have been migrated
SELECT 
    'Ratings without account_role_ID' as check_type,
    COUNT(*) as count
FROM Rating
WHERE rater_account_role_ID IS NULL OR rated_account_role_ID IS NULL;

-- Display summary of Account_Role entries
SELECT 
    r.role_name,
    COUNT(*) as user_count,
    AVG(ar.role_rating_score) as avg_rating,
    MIN(ar.role_rating_score) as min_rating,
    MAX(ar.role_rating_score) as max_rating
FROM Account_Role ar
INNER JOIN Role_Info r ON ar.role_ID = r.role_ID
WHERE ar.is_active = 1
GROUP BY r.role_name;
