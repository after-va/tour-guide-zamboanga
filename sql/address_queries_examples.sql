-- Example Queries for Hierarchical Address System
-- Demonstrates how to fetch cascading location data

-- ========================================
-- 1. Get all provinces for a specific country
-- ========================================
SELECT province_ID, province_name 
FROM Province 
WHERE country_ID = 1  -- Philippines
ORDER BY province_name;

-- ========================================
-- 2. Get all cities for a specific province
-- ========================================
SELECT city_ID, city_name 
FROM City 
WHERE province_ID = 1  -- Zamboanga del Sur
ORDER BY city_name;

-- ========================================
-- 3. Get all barangays for a specific city
-- ========================================
SELECT barangay_ID, barangay_name 
FROM Barangay 
WHERE city_ID = 1  -- Zamboanga City
ORDER BY barangay_name;

-- ========================================
-- 4. Get complete address hierarchy for a specific address
-- ========================================
SELECT * FROM v_address_complete 
WHERE address_ID = 1;

-- ========================================
-- 5. Get all addresses in a specific barangay with full details
-- ========================================
SELECT 
    a.address_ID,
    CONCAT(a.address_houseno, ' ', a.address_street) as street_address,
    b.barangay_name,
    c.city_name,
    pr.province_name,
    co.country_name
FROM Address_Info a
INNER JOIN Barangay b ON a.barangay_ID = b.barangay_ID
INNER JOIN City c ON b.city_ID = c.city_ID
INNER JOIN Province pr ON c.province_ID = pr.province_ID
INNER JOIN Country co ON pr.country_ID = co.country_ID
WHERE b.barangay_name = 'Tetuan';

-- ========================================
-- 6. Cascading dropdown example: Get provinces when country is selected
-- ========================================
-- Step 1: User selects country (e.g., Philippines with country_ID = 1)
SELECT province_ID, province_name 
FROM Province 
WHERE country_ID = 1;

-- Step 2: User selects province (e.g., Zamboanga del Sur with province_ID = 1)
SELECT city_ID, city_name 
FROM City 
WHERE province_ID = 1;

-- Step 3: User selects city (e.g., Zamboanga City with city_ID = 1)
SELECT barangay_ID, barangay_name 
FROM Barangay 
WHERE city_ID = 1;

-- ========================================
-- 7. Get all locations in a hierarchical tree format
-- ========================================
SELECT 
    co.country_name,
    pr.province_name,
    c.city_name,
    b.barangay_name,
    COUNT(a.address_ID) as address_count
FROM Country co
LEFT JOIN Province pr ON co.country_ID = pr.country_ID
LEFT JOIN City c ON pr.province_ID = c.province_ID
LEFT JOIN Barangay b ON c.city_ID = b.city_ID
LEFT JOIN Address_Info a ON b.barangay_ID = a.barangay_ID
GROUP BY co.country_ID, pr.province_ID, c.city_ID, b.barangay_ID
ORDER BY co.country_name, pr.province_name, c.city_name, b.barangay_name;

-- ========================================
-- 8. Find all persons living in a specific city
-- ========================================
SELECT 
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    CONCAT(a.address_houseno, ' ', a.address_street) as street_address,
    b.barangay_name,
    c.city_name
FROM Person p
INNER JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
INNER JOIN Address_Info a ON ci.address_ID = a.address_ID
INNER JOIN Barangay b ON a.barangay_ID = b.barangay_ID
INNER JOIN City c ON b.city_ID = c.city_ID
INNER JOIN Name_Info n ON p.name_ID = n.name_ID
WHERE c.city_name = 'Zamboanga City';

-- ========================================
-- 9. Get location statistics
-- ========================================
SELECT 
    co.country_name,
    COUNT(DISTINCT pr.province_ID) as province_count,
    COUNT(DISTINCT c.city_ID) as city_count,
    COUNT(DISTINCT b.barangay_ID) as barangay_count,
    COUNT(a.address_ID) as address_count
FROM Country co
LEFT JOIN Province pr ON co.country_ID = pr.country_ID
LEFT JOIN City c ON pr.province_ID = c.province_ID
LEFT JOIN Barangay b ON c.city_ID = b.city_ID
LEFT JOIN Address_Info a ON b.barangay_ID = a.barangay_ID
GROUP BY co.country_ID
ORDER BY co.country_name;

-- ========================================
-- 10. Search for locations by name (useful for autocomplete)
-- ========================================
-- Search barangays
SELECT 
    b.barangay_ID,
    b.barangay_name,
    c.city_name,
    pr.province_name,
    co.country_name
FROM Barangay b
INNER JOIN City c ON b.city_ID = c.city_ID
INNER JOIN Province pr ON c.province_ID = pr.province_ID
INNER JOIN Country co ON pr.country_ID = co.country_ID
WHERE b.barangay_name LIKE '%Tet%'
ORDER BY b.barangay_name;

-- ========================================
-- 11. Insert new address with hierarchical data
-- ========================================
-- Example: Adding a new address
-- First, ensure the location hierarchy exists, then insert the address
INSERT INTO Address_Info (address_houseno, address_street, barangay_ID) 
VALUES ('999', 'New Street', 1);

-- ========================================
-- 12. Get reverse lookup: From address to full location details
-- ========================================
SELECT 
    a.address_ID,
    a.address_houseno,
    a.address_street,
    b.barangay_name,
    c.city_name,
    pr.province_name,
    co.country_name,
    co.country_code
FROM Address_Info a
INNER JOIN Barangay b ON a.barangay_ID = b.barangay_ID
INNER JOIN City c ON b.city_ID = c.city_ID
INNER JOIN Province pr ON c.province_ID = pr.province_ID
INNER JOIN Country co ON pr.country_ID = co.country_ID
WHERE a.address_ID = 1;
