-- ============================================
-- CLEANUP SCRIPT - Run this FIRST before importing PART files
-- ============================================
-- This removes duplicate barangays that are already in ph-location.sql

-- Delete barangays for major cities that are already in ph-location.sql
-- These cities already have barangays from the "top 10 major cities" section

DELETE FROM Barangay WHERE city_ID IN (
    53,  -- Antipolo City (already has barangays in ph-location.sql)
    89,  -- Cebu City
    126, -- Cagayan de Oro City
    -- Add Manila, Quezon City, etc. if they exist in your city table
    (SELECT city_ID FROM City WHERE city_name = 'Manila' LIMIT 1),
    (SELECT city_ID FROM City WHERE city_name = 'Quezon City' LIMIT 1),
    (SELECT city_ID FROM City WHERE city_name = 'Davao City' LIMIT 1),
    (SELECT city_ID FROM City WHERE city_name = 'Caloocan City' LIMIT 1),
    (SELECT city_ID FROM City WHERE city_name = 'Makati City' LIMIT 1),
    (SELECT city_ID FROM City WHERE city_name = 'Taguig City' LIMIT 1),
    (SELECT city_ID FROM City WHERE city_name = 'Pasig City' LIMIT 1)
);

-- Also delete any barangays from the old incorrect imports (cities 11-161, except Zamboanga)
DELETE FROM Barangay 
WHERE city_ID BETWEEN 11 AND 161
AND city_ID != 117; -- Keep Zamboanga City (ID 117)

SELECT 'Cleanup complete! Now import PART1-4 files in order.' as message;
