
INSERT INTO country_code(countrycode_name, countrycode_number) VALUES
('Afghanistan', '+93'),
('Albania', '+355'),
('Algeria', '+213'),
('American Samoa', '+1-684'),
('Andorra', '+376'),
('Angola', '+244'),
('Anguilla', '+1-264'),
('Antarctica', '+672'),
('Antigua and Barbuda', '+1-268'),
('Argentina', '+54'),
('Armenia', '+374'),
('Aruba', '+297'),
('Australia', '+61'),
('Austria', '+43'),
('Azerbaijan', '+994'),
('Bahamas', '+1-242'),
('Bahrain', '+973'),
('Bangladesh', '+880'),
('Barbados', '+1-246'),
('Belarus', '+375'),
('Belgium', '+32'),
('Belize', '+501'),
('Benin', '+229'),
('Bermuda', '+1-441'),
('Bhutan', '+975'),
('Bolivia', '+591'),
('Bosnia and Herzegovina', '+387'),
('Botswana', '+267'),
('Brazil', '+55'),
('British Virgin Islands', '+1-284'),
('Brunei', '+673'),
('Bulgaria', '+359'),
('Burkina Faso', '+226'),
('Burundi', '+257'),
('Cabo Verde', '+238'),
('Cambodia', '+855'),
('Cameroon', '+237'),
('Canada', '+1'),
('Cayman Islands', '+1-345'),
('Central African Republic', '+236'),
('Chad', '+235'),
('Chile', '+56'),
('China', '+86'),
('Colombia', '+57'),
('Comoros', '+269'),
('Congo (DRC)', '+243'),
('Congo (Republic)', '+242'),
('Costa Rica', '+506'),
('Cote d''Ivoire', '+225'),
('Croatia', '+385'),
('Cuba', '+53'),
('Curaçao', '+599'),
('Cyprus', '+357'),
('Czech Republic', '+420'),
('Denmark', '+45'),
('Djibouti', '+253'),
('Dominica', '+1-767'),
('Dominican Republic', '+1-809'),
('Ecuador', '+593'),
('Egypt', '+20'),
('El Salvador', '+503'),
('Equatorial Guinea', '+240'),
('Eritrea', '+291'),
('Estonia', '+372'),
('Eswatini', '+268'),
('Ethiopia', '+251'),
('Falkland Islands', '+500'),
('Faroe Islands', '+298'),
('Fiji', '+679'),
('Finland', '+358'),
('France', '+33'),
('French Guiana', '+594'),
('French Polynesia', '+689'),
('Gabon', '+241'),
('Gambia', '+220'),
('Georgia', '+995'),
('Germany', '+49'),
('Ghana', '+233'),
('Gibraltar', '+350'),
('Greece', '+30'),
('Greenland', '+299'),
('Grenada', '+1-473'),
('Guadeloupe', '+590'),
('Guam', '+1-671'),
('Guatemala', '+502'),
('Guinea', '+224'),
('Guinea-Bissau', '+245'),
('Guyana', '+592'),
('Haiti', '+509'),
('Honduras', '+504'),
('Hong Kong', '+852'),
('Hungary', '+36'),
('Iceland', '+354'),
('India', '+91'),
('Indonesia', '+62'),
('Iran', '+98'),
('Iraq', '+964'),
('Ireland', '+353'),
('Israel', '+972'),
('Italy', '+39'),
('Jamaica', '+1-876'),
('Japan', '+81'),
('Jordan', '+962'),
('Kazakhstan', '+7'),
('Kenya', '+254'),
('Kiribati', '+686'),
('Kosovo', '+383'),
('Kuwait', '+965'),
('Kyrgyzstan', '+996'),
('Laos', '+856'),
('Latvia', '+371'),
('Lebanon', '+961'),
('Lesotho', '+266'),
('Liberia', '+231'),
('Libya', '+218'),
('Liechtenstein', '+423'),
('Lithuania', '+370'),
('Luxembourg', '+352'),
('Macao', '+853'),
('Madagascar', '+261'),
('Malawi', '+265'),
('Malaysia', '+60'),
('Maldives', '+960'),
('Mali', '+223'),
('Malta', '+356'),
('Marshall Islands', '+692'),
('Martinique', '+596'),
('Mauritania', '+222'),
('Mauritius', '+230'),
('Mexico', '+52'),
('Micronesia', '+691'),
('Moldova', '+373'),
('Monaco', '+377'),
('Mongolia', '+976'),
('Montenegro', '+382'),
('Montserrat', '+1-664'),
('Morocco', '+212'),
('Mozambique', '+258'),
('Myanmar (Burma)', '+95'),
('Namibia', '+264'),
('Nauru', '+674'),
('Nepal', '+977'),
('Netherlands', '+31'),
('New Caledonia', '+687'),
('New Zealand', '+64'),
('Nicaragua', '+505'),
('Niger', '+227'),
('Nigeria', '+234'),
('Niue', '+683'),
('North Korea', '+850'),
('North Macedonia', '+389'),
('Norway', '+47'),
('Oman', '+968'),
('Pakistan', '+92'),
('Palau', '+680'),
('Palestine', '+970'),
('Panama', '+507'),
('Papua New Guinea', '+675'),
('Paraguay', '+595'),
('Peru', '+51'),
('Philippines', '+63'),
('Poland', '+48'),
('Portugal', '+351'),
('Puerto Rico', '+1-787'),
('Qatar', '+974'),
('Reunion', '+262'),
('Romania', '+40'),
('Russia', '+7'),
('Rwanda', '+250'),
('Saint Kitts and Nevis', '+1-869'),
('Saint Lucia', '+1-758'),
('Saint Vincent and the Grenadines', '+1-784'),
('Samoa', '+685'),
('San Marino', '+378'),
('Sao Tome and Principe', '+239'),
('Saudi Arabia', '+966'),
('Senegal', '+221'),
('Serbia', '+381'),
('Seychelles', '+248'),
('Sierra Leone', '+232'),
('Singapore', '+65'),
('Slovakia', '+421'),
('Slovenia', '+386'),
('Solomon Islands', '+677'),
('Somalia', '+252'),
('South Africa', '+27'),
('South Korea', '+82'),
('South Sudan', '+211'),
('Spain', '+34'),
('Sri Lanka', '+94'),
('Sudan', '+249'),
('Suriname', '+597'),
('Sweden', '+46'),
('Switzerland', '+41'),
('Syria', '+963'),
('Taiwan', '+886'),
('Tajikistan', '+992'),
('Tanzania', '+255'),
('Thailand', '+66'),
('Togo', '+228'),
('Tonga', '+676'),
('Trinidad and Tobago', '+1-868'),
('Tunisia', '+216'),
('Turkey', '+90'),
('Turkmenistan', '+993'),
('Tuvalu', '+688'),
('Uganda', '+256'),
('Ukraine', '+380'),
('United Arab Emirates', '+971'),
('United Kingdom', '+44'),
('United States', '+1'),
('Uruguay', '+598'),
('Uzbekistan', '+998'),
('Vanuatu', '+678'),
('Vatican City', '+379'),
('Venezuela', '+58'),
('Vietnam', '+84'),
('Yemen', '+967'),
('Zambia', '+260'),
('Zimbabwe', '+263');



INSERT INTO User_Status (status_ID, status_name) VALUES 
(1, 'Active'),
(2, 'Inactive'),
(3, 'Suspended')
ON DUPLICATE KEY UPDATE status_name = VALUES(status_name);

-- Insert default roles
INSERT INTO Role_Info (role_name) VALUES 
('Admin'),
('Tour Guide'),
('Tourist')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- Insert default payment methods
INSERT INTO Payment_Method (method_name, method_type, processing_fee) VALUES
('Credit Card', 'card', 2.50),
('Debit Card', 'card', 2.50),
('GCash', 'ewallet', 1.00),
('PayMaya', 'ewallet', 1.00),
('Bank Transfer', 'bank', 0.00),
('Cash', 'cash', 0.00)
ON DUPLICATE KEY UPDATE method_name = VALUES(method_name);

-- Insert default system settings
INSERT INTO System_Settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Tourismo Zamboanga', 'text', 'Website name'),
('site_email', 'info@tourismozamboanga.com', 'email', 'Contact email'),
('booking_fee', '200', 'number', 'Service fee per booking in PHP'),
('cancellation_hours', '24', 'number', 'Hours before tour to allow cancellation'),
('max_booking_days', '90', 'number', 'Maximum days in advance for booking'),
('min_booking_hours', '24', 'number', 'Minimum hours in advance for booking')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);


INSERT INTO tour_spots(spots_Name, spots_Description, spots_category, spots_Address, spots_GoogleLink) VALUES 
('Great Santa Cruz Island (Pink Sand Beach)', 'Famous for its unique pink-hued sand, which gets its color from crushed red organ pipe corals mixing with the white sand. Its a great spot for swimming, picnicking, and has a mangrove lagoon tour.', 'Beach', 'Zamboanga City', 'https://maps.app.goo.gl/3SR4NzSbEoCMeu689'), 
('Fort Pilar','A 17th-century military defense fortress built by the Spanish. It is now a Latin American-style outdoor shrine dedicated to the Our Lady of the Pillar and houses the National Museum Western-Southern Mindanao Regional Museum.','Historical','N.S. Valderosa St., Zamboanga City','https://maps.app.goo.gl/KfRWjCMRfhtMSn9Z7'), 
('Paseo del Mar','A vibrant seaside promenade near Fort Pilar, popular for strolling, enjoying the sunset, and serving as the jump-off point for Great Santa Cruz Island. Its also a great spot to try local food, like the famous Knickerbocker dessert.','Entertainment','N S Valderosa St. (Right beside Fort Pilar), Zone IV, Zamboanga City','https://maps.app.goo.gl/yfL5eojhbs3hdWqV8'), 
('Yakan Weaving Village (or Yakan Weaving Center)','A place to witness the artistry of the Yakan indigenous people, who are renowned for their intricate, vibrant, hand-woven textiles and crafts. You can buy their products here.','Cultural','Upper Calarian, Labuan - Limpapa National Road, Zamboanga City','https://maps.app.goo.gl/YXzJViyeq3mPAsmy8'), 
('Pasonanca Park','A sprawling urban park featuring a Boy Scout camp, a public swimming pool, a Tree House (which used to host guests of the former mayor), and the El Museo de Zamboanga.','Nature','Pasonanca Road, Brgy. Pasonanca, Zamboanga City','https://maps.app.goo.gl/cP7Bdk9aMBVhL19X6'), 
('Merloquet Falls','A beautiful two-tiered waterfall located outside the city center, known for its unique, stair-like rock formations.','Nature','Brgy. Sibulao, Zamboanga City (Approximately 1-2 hours travel from the city proper)','https://maps.app.goo.gl/AQab8f5XXX68wsbV7'), 
('Once Islas','A cluster of 11 islands (though only a few are open to the public) offering island hopping, pristine beaches, and eco-cultural tourism experiences, such as Bisaya-Bisaya and Baung-Baung Islands.','Beach','Panubigan Ferry Terminal, Brgy. Panubigan (The designated jump-off point for the islands)','https://maps.app.goo.gl/Y4DoN4487Pi3DQ5q9'), 
('Zamboanga City Hall','A beautiful, well-preserved colonial-era building with historical significance, often included in a city walking tour.','Historical','N S Valderosa St., Zone IV, Zamboanga City','https://maps.app.goo.gl/T2yXcvBQaj1NBZrU6'), 
('Taluksangay Mosque','The oldest mosque in the Zamboanga Peninsula (built in 1885), distinguished by its distinctive red domes and recognized as a significant center for the propagation of Islam.','Religious','Brgy. Taluksangay, Zamboanga City','https://maps.app.goo.gl/tyEbMvVsNan8aeDR7'), 
('Metropolitan Cathedral of the Immaculate Conception','The main Catholic cathedral in the city, known for its distinct, modern architectural design.','Religious','La Purisima St., Zamboanga City','https://maps.app.goo.gl/vU5hH8E3MMMHbn7g7');






CREATE INDEX idx_booking_status ON Booking(booking_Status);
CREATE INDEX idx_booking_customer ON Booking(customer_ID);
CREATE INDEX idx_schedule_guide ON Schedule(guide_ID);
CREATE INDEX idx_schedule_date ON Schedule(schedule_StartDateTime);
CREATE INDEX idx_payment_booking ON Payment_Info(booking_ID);
CREATE INDEX idx_rating_rated ON Rating(rated_ID);
CREATE INDEX idx_user_login_username ON User_Login(username);
CREATE INDEX idx_activity_log_user ON Activity_Log(user_ID);
CREATE INDEX idx_notifications_user ON Notifications(user_ID, is_read);

-- Create views for common queries
CREATE OR REPLACE VIEW v_user_details AS
SELECT 
    p.person_ID,
    p.role_ID,
    r.role_name,
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    n.name_first,
    n.name_last,
    ci.contactinfo_email as email,
    ph.phone_number,
    p.person_RatingScore as rating,
    ul.username,
    ul.last_login,
    ul.is_active
FROM Person p
LEFT JOIN Role_Info r ON p.role_ID = r.role_ID
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID;

CREATE OR REPLACE VIEW v_booking_details AS
SELECT 
    b.booking_ID,
    b.booking_Status,
    b.booking_PAX,
    CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
    CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
    tp.tourPackage_Name,
    ts.spots_Name,
    s.schedule_StartDateTime,
    s.schedule_EndDateTime,
    s.schedule_MeetingSpot,
    pi.paymentinfo_Amount,
    pi.paymentinfo_Date,
    pt.transaction_status as payment_status
FROM Booking b
LEFT JOIN Person t ON b.customer_ID = t.person_ID
LEFT JOIN Name_Info tn ON t.name_ID = tn.name_ID
LEFT JOIN Schedule s ON b.schedule_ID = s.schedule_ID
LEFT JOIN Person g ON s.guide_ID = g.person_ID
LEFT JOIN Name_Info gn ON g.name_ID = gn.name_ID
LEFT JOIN Tour_Package tp ON b.tourPackage_ID = tp.tourPackage_ID
LEFT JOIN Tour_Spots ts ON tp.spots_ID = ts.spots_ID
LEFT JOIN Payment_Info pi ON b.booking_ID = pi.booking_ID
LEFT JOIN Payment_Transaction pt ON pi.paymentinfo_ID = pt.paymentinfo_ID;

CREATE TABLE IF NOT EXISTS Package_Spots (
    package_spot_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_ID INT NOT NULL,
    spots_ID INT NOT NULL,
    spot_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    FOREIGN KEY (spots_ID) REFERENCES Tour_Spots(spots_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_package_spot (tourPackage_ID, spots_ID)
);

CREATE INDEX idx_package_spots_package ON Package_Spots(tourPackage_ID);
CREATE INDEX idx_package_spots_spot ON Package_Spots(spots_ID);
