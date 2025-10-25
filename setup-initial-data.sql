-- Initial data setup for Tour Guide System
-- Run this after creating the database schema

-- Insert Country Codes
INSERT IGNORE INTO Country_Code (countrycode_name, countrycode_number) VALUES
('Philippines', '+63'),
('United States', '+1'),
('United Kingdom', '+44'),
('Australia', '+61'),
('Canada', '+1'),
('Japan', '+81'),
('South Korea', '+82'),
('China', '+86'),
('Singapore', '+65'),
('Malaysia', '+60');

-- Insert Roles
INSERT IGNORE INTO Role_Info (role_ID, role_name) VALUES
(1, 'Admin'),
(2, 'Tour Guide'),
(3, 'Tourist');

-- Insert Rating Categories
INSERT IGNORE INTO Rating_Category (ratingcategory_name, ratingcategory_from, ratingcategory_to) VALUES
('Excellent', 5, 5),
('Very Good', 4, 4),
('Good', 3, 3),
('Fair', 2, 2),
('Poor', 1, 1);

-- Insert Companion Categories
INSERT IGNORE INTO Companion_Category (companioncategory_name) VALUES
('Adult'),
('Child'),
('Senior Citizen'),
('Infant');

-- Insert Payment Methods
INSERT IGNORE INTO Payment_Method (method_name, method_type, is_active, processing_fee) VALUES
('Credit Card', 'card', 1, 2.50),
('Debit Card', 'card', 1, 2.00),
('GCash', 'ewallet', 1, 0.00),
('PayMaya', 'ewallet', 1, 0.00),
('Bank Transfer', 'bank', 1, 0.00),
('Cash', 'cash', 1, 0.00);

-- Verify data
SELECT 'Country Codes:' as Info;
SELECT * FROM Country_Code;

SELECT 'Roles:' as Info;
SELECT * FROM Role_Info;

SELECT 'Payment Methods:' as Info;
SELECT * FROM Payment_Method;
