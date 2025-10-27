-- ============================================
-- FIX BARANGAY CITY_ID MISMATCH
-- ============================================
-- The barangays were inserted with wrong city_IDs
-- This script clears them so you can re-import with correct IDs

-- Delete all barangays for cities 11-161 (keep cities 1-10 and Zamboanga)
DELETE FROM Barangay 
WHERE city_ID BETWEEN 11 AND 161
AND city_ID != 117; -- Keep Zamboanga City (ID 117)

SELECT 'Incorrect barangays deleted!' as message;
SELECT 'Now you need to re-import with correct city_IDs' as next_step;
