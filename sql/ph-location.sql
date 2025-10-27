-- ============================================
-- GLOBAL LOCATION DATA
-- Countries, Provinces/States, Cities, and Districts
-- ============================================


-- Insert Countries

INSERT IGNORE INTO Country (country_name, country_codename, country_codenumber) VALUES
('Afghanistan', 'AF', '+93'),
('Albania', 'AL', '+355'),
('Algeria', 'DZ', '+213'),
('American Samoa', 'AS', '+1-684'),
('Andorra', 'AD', '+376'),
('Angola', 'AO', '+244'),
('Anguilla', 'AI', '+1-264'),
('Antarctica', 'AQ', '+672'),
('Antigua and Barbuda', 'AG', '+1-268'),
('Argentina', 'AR', '+54'),
('Armenia', 'AM', '+374'),
('Aruba', 'AW', '+297'),
('Australia', 'AU', '+61'),
('Austria', 'AT', '+43'),
('Azerbaijan', 'AZ', '+994'),
('Bahamas', 'BS', '+1-242'),
('Bahrain', 'BH', '+973'),
('Bangladesh', 'BD', '+880'),
('Barbados', 'BB', '+1-246'),
('Belarus', 'BY', '+375'),
('Belgium', 'BE', '+32'),
('Belize', 'BZ', '+501'),
('Benin', 'BJ', '+229'),
('Bermuda', 'BM', '+1-441'),
('Bhutan', 'BT', '+975'),
('Bolivia', 'BO', '+591'),
('Bosnia and Herzegovina', 'BA', '+387'),
('Botswana', 'BW', '+267'),
('Brazil', 'BR', '+55'),
('British Virgin Islands', 'VG', '+1-284'),
('Brunei', 'BN', '+673'),
('Bulgaria', 'BG', '+359'),
('Burkina Faso', 'BF', '+226'),
('Burundi', 'BI', '+257'),
('Cabo Verde', 'CV', '+238'),
('Cambodia', 'KH', '+855'),
('Cameroon', 'CM', '+237'),
('Canada', 'CA', '+1'),
('Cayman Islands', 'KY', '+1-345'),
('Central African Republic', 'CF', '+236'),
('Chad', 'TD', '+235'),
('Chile', 'CL', '+56'),
('China', 'CN', '+86'),
('Colombia', 'CO', '+57'),
('Comoros', 'KM', '+269'),
('Congo (DRC)', 'CD', '+243'),
('Congo (Republic)', 'CG', '+242'),
('Costa Rica', 'CR', '+506'),
('Cote d''Ivoire', 'CI', '+225'),
('Croatia', 'HR', '+385'),
('Cuba', 'CU', '+53'),
('Curaçao', 'CW', '+599'),
('Cyprus', 'CY', '+357'),
('Czech Republic', 'CZ', '+420'),
('Denmark', 'DK', '+45'),
('Djibouti', 'DJ', '+253'),
('Dominica', 'DM', '+1-767'),
('Dominican Republic', 'DO', '+1-809'),
('Ecuador', 'EC', '+593'),
('Egypt', 'EG', '+20'),
('El Salvador', 'SV', '+503'),
('Equatorial Guinea', 'GQ', '+240'),
('Eritrea', 'ER', '+291'),
('Estonia', 'EE', '+372'),
('Eswatini', 'SZ', '+268'),
('Ethiopia', 'ET', '+251'),
('Falkland Islands', 'FK', '+500'),
('Faroe Islands', 'FO', '+298'),
('Fiji', 'FJ', '+679'),
('Finland', 'FI', '+358'),
('France', 'FR', '+33'),
('French Guiana', 'GF', '+594'),
('French Polynesia', 'PF', '+689'),
('Gabon', 'GA', '+241'),
('Gambia', 'GM', '+220'),
('Georgia', 'GE', '+995'),
('Germany', 'DE', '+49'),
('Ghana', 'GH', '+233'),
('Gibraltar', 'GI', '+350'),
('Greece', 'GR', '+30'),
('Greenland', 'GL', '+299'),
('Grenada', 'GD', '+1-473'),
('Guadeloupe', 'GP', '+590'),
('Guam', 'GU', '+1-671'),
('Guatemala', 'GT', '+502'),
('Guinea', 'GN', '+224'),
('Guinea-Bissau', 'GW', '+245'),
('Guyana', 'GY', '+592'),
('Haiti', 'HT', '+509'),
('Honduras', 'HN', '+504'),
('Hong Kong', 'HK', '+852'),
('Hungary', 'HU', '+36'),
('Iceland', 'IS', '+354'),
('India', 'IN', '+91'),
('Indonesia', 'ID', '+62'),
('Iran', 'IR', '+98'),
('Iraq', 'IQ', '+964'),
('Ireland', 'IE', '+353'),
('Israel', 'IL', '+972'),
('Italy', 'IT', '+39'),
('Jamaica', 'JM', '+1-876'),
('Japan', 'JP', '+81'),
('Jordan', 'JO', '+962'),
('Kazakhstan', 'KZ', '+7'),
('Kenya', 'KE', '+254'),
('Kiribati', 'KI', '+686'),
('Kosovo', 'XK', '+383'),
('Kuwait', 'KW', '+965'),
('Kyrgyzstan', 'KG', '+996'),
('Laos', 'LA', '+856'),
('Latvia', 'LV', '+371'),
('Lebanon', 'LB', '+961'),
('Lesotho', 'LS', '+266'),
('Liberia', 'LR', '+231'),
('Libya', 'LY', '+218'),
('Liechtenstein', 'LI', '+423'),
('Lithuania', 'LT', '+370'),
('Luxembourg', 'LU', '+352'),
('Macao', 'MO', '+853'),
('Madagascar', 'MG', '+261'),
('Malawi', 'MW', '+265'),
('Malaysia', 'MY', '+60'),
('Maldives', 'MV', '+960'),
('Mali', 'ML', '+223'),
('Malta', 'MT', '+356'),
('Marshall Islands', 'MH', '+692'),
('Martinique', 'MQ', '+596'),
('Mauritania', 'MR', '+222'),
('Mauritius', 'MU', '+230'),
('Mexico', 'MX', '+52'),
('Micronesia', 'FM', '+691'),
('Moldova', 'MD', '+373'),
('Monaco', 'MC', '+377'),
('Mongolia', 'MN', '+976'),
('Montenegro', 'ME', '+382'),
('Montserrat', 'MS', '+1-664'),
('Morocco', 'MA', '+212'),
('Mozambique', 'MZ', '+258'),
('Myanmar (Burma)', 'MM', '+95'),
('Namibia', 'NA', '+264'),
('Nauru', 'NR', '+674'),
('Nepal', 'NP', '+977'),
('Netherlands', 'NL', '+31'),
('New Caledonia', 'NC', '+687'),
('New Zealand', 'NZ', '+64'),
('Nicaragua', 'NI', '+505'),
('Niger', 'NE', '+227'),
('Nigeria', 'NG', '+234'),
('Niue', 'NU', '+683'),
('North Korea', 'KP', '+850'),
('North Macedonia', 'MK', '+389'),
('Norway', 'NO', '+47'),
('Oman', 'OM', '+968'),
('Pakistan', 'PK', '+92'),
('Palau', 'PW', '+680'),
('Palestine', 'PS', '+970'),
('Panama', 'PA', '+507'),
('Papua New Guinea', 'PG', '+675'),
('Paraguay', 'PY', '+595'),
('Peru', 'PE', '+51'),
('Philippines', 'PH', '+63'),
('Poland', 'PL', '+48'),
('Portugal', 'PT', '+351'),
('Puerto Rico', 'PR', '+1-787'),
('Qatar', 'QA', '+974'),
('Reunion', 'RE', '+262'),
('Romania', 'RO', '+40'),
('Russia', 'RU', '+7'),
('Rwanda', 'RW', '+250'),
('Saint Kitts and Nevis', 'KN', '+1-869'),
('Saint Lucia', 'LC', '+1-758'),
('Saint Vincent and the Grenadines', 'VC', '+1-784'),
('Samoa', 'WS', '+685'),
('San Marino', 'SM', '+378'),
('Sao Tome and Principe', 'ST', '+239'),
('Saudi Arabia', 'SA', '+966'),
('Senegal', 'SN', '+221'),
('Serbia', 'RS', '+381'),
('Seychelles', 'SC', '+248'),
('Sierra Leone', 'SL', '+232'),
('Singapore', 'SG', '+65'),
('Slovakia', 'SK', '+421'),
('Slovenia', 'SI', '+386'),
('Solomon Islands', 'SB', '+677'),
('Somalia', 'SO', '+252'),
('South Africa', 'ZA', '+27'),
('South Korea', 'KR', '+82'),
('South Sudan', 'SS', '+211'),
('Spain', 'ES', '+34'),
('Sri Lanka', 'LK', '+94'),
('Sudan', 'SD', '+249'),
('Suriname', 'SR', '+597'),
('Sweden', 'SE', '+46'),
('Switzerland', 'CH', '+41'),
('Syria', 'SY', '+963'),
('Taiwan', 'TW', '+886'),
('Tajikistan', 'TJ', '+992'),
('Tanzania', 'TZ', '+255'),
('Thailand', 'TH', '+66'),
('Togo', 'TG', '+228'),
('Tonga', 'TO', '+676'),
('Trinidad and Tobago', 'TT', '+1-868'),
('Tunisia', 'TN', '+216'),
('Turkey', 'TR', '+90'),
('Turkmenistan', 'TM', '+993'),
('Tuvalu', 'TV', '+688'),
('Uganda', 'UG', '+256'),
('Ukraine', 'UA', '+380'),
('United Arab Emirates', 'AE', '+971'),
('United Kingdom', 'GB', '+44'),
('United States', 'US', '+1'),
('Uruguay', 'UY', '+598'),
('Uzbekistan', 'UZ', '+998'),
('Vanuatu', 'VU', '+678'),
('Vatican City', 'VA', '+379'),
('Venezuela', 'VE', '+58'),
('Vietnam', 'VN', '+84'),
('Yemen', 'YE', '+967'),
('Zambia', 'ZM', '+260'),
('Zimbabwe', 'ZW', '+263');

-- Get the actual country_ID for Philippines (don't assume it's 161)
SET @ph_country_id = (SELECT country_ID FROM Country WHERE country_name = 'Philippines' LIMIT 1);

-- Insert Philippine Regions using the actual country_ID
INSERT INTO Region (region_name, country_ID) VALUES
('Region I: Ilocos Region', @ph_country_id),
('Region II: Cagayan Valley', @ph_country_id),
('Region III: Central Luzon', @ph_country_id),
('Region IV-A: CALABARZON', @ph_country_id),
('Region IV-B: MIMAROPA', @ph_country_id),
('Region V: Bicol Region', @ph_country_id),
('Region VI: Western Visayas', @ph_country_id),
('Region VII: Central Visayas', @ph_country_id),
('Region VIII: Eastern Visayas', @ph_country_id),
('Region IX: Zamboanga Peninsula', @ph_country_id),
('Region X: Northern Mindanao', @ph_country_id),
('Region XI: Davao Region', @ph_country_id),
('Region XII: SOCCSKSARGEN', @ph_country_id),
('Region XIII: Caraga', @ph_country_id),
('NCR: National Capital Region', @ph_country_id),
('CAR: Cordillera Administrative Region', @ph_country_id),
('BARMM: Bangsamoro Autonomous Region in Muslim Mindanao', @ph_country_id);

INSERT INTO Province (province_name, region_ID) VALUES
('Ilocos Norte', 1),
('Ilocos Sur', 1),
('La Union', 1),
('Pangasinan', 1);

INSERT INTO Province (province_name, region_ID) VALUES
('Batanes', 2),
('Cagayan', 2),
('Isabela', 2),
('Nueva Vizcaya', 2),
('Quirino', 2);

INSERT INTO Province (province_name, region_ID) VALUES
('Aurora', 3),
('Bataan', 3),
('Bulacan', 3),
('Nueva Ecija', 3),
('Pampanga', 3),
('Tarlac', 3),
('Zambales', 3);

INSERT INTO Province (province_name, region_ID) VALUES
('Batangas', 4),
('Cavite', 4),
('Laguna', 4),
('Quezon', 4),
('Rizal', 4);

INSERT INTO Province (province_name, region_ID) VALUES
('Marinduque', 5),
('Occidental Mindoro', 5),
('Oriental Mindoro', 5),
('Palawan', 5),
('Romblon', 5);

INSERT INTO Province (province_name, region_ID) VALUES
('Albay', 6),
('Camarines Norte', 6),
('Camarines Sur', 6),
('Catanduanes', 6),
('Masbate', 6),
('Sorsogon', 6);

INSERT INTO Province (province_name, region_ID) VALUES
('Aklan', 7),
('Antique', 7),
('Capiz', 7),
('Guimaras', 7),
('Iloilo', 7),
('Negros Occidental', 7);

INSERT INTO Province (province_name, region_ID) VALUES
('Bohol', 8),
('Cebu', 8),
('Negros Oriental', 8),
('Siquijor', 8);

INSERT INTO Province (province_name, region_ID) VALUES
('Biliran', 9),
('Eastern Samar', 9),
('Leyte', 9),
('Northern Samar', 9),
('Samar', 9),
('Southern Leyte', 9);

INSERT INTO Province (province_name, region_ID) VALUES
('Zamboanga del Norte', 10),
('Zamboanga del Sur', 10),
('Zamboanga Sibugay', 10),
('Zamboanga City', 10);

INSERT INTO Province (province_name, region_ID) VALUES
('Bukidnon', 11),
('Camiguin', 11),
('Lanao del Norte', 11),
('Misamis Occidental', 11),
('Misamis Oriental', 11);

INSERT INTO Province (province_name, region_ID) VALUES
('Davao de Oro', 12),
('Davao del Norte', 12),
('Davao del Sur', 12),
('Davao Occidental', 12),
('Davao Oriental', 12);

INSERT INTO Province (province_name, region_ID) VALUES
('Cotabato', 13),
('Sarangani', 13),
('South Cotabato', 13),
('Sultan Kudarat', 13),
('General Santos City', 13);

INSERT INTO Province (province_name, region_ID) VALUES
('Agusan del Norte', 14),
('Agusan del Sur', 14),
('Dinagat Islands', 14),
('Surigao del Norte', 14),
('Surigao del Sur', 14);

INSERT INTO Province (province_name, region_ID) VALUES
('Basilan', 15),
('Lanao del Sur', 15),
('Maguindanao', 15),
('Sulu', 15),
('Tawi-Tawi', 15);

INSERT INTO Province (province_name, region_ID) VALUES
('Abra', 16),
('Apayao', 16),
('Benguet', 16),
('Ifugao', 16),
('Kalinga', 16),
('Mountain Province', 16);


-- CITIES/ MUNICIPALITIES INSERTION

INSERT INTO City (city_name, province_ID) VALUES
('Laoag City', 1), ('City of Batac', 1),
('Vigan City', 2), ('Candon City', 2),
('San Fernando City', 3),
('Lingayen', 4), ('Alaminos City', 4), ('Dagupan City', 4), ('San Carlos City', 4), ('Urdaneta City', 4);

INSERT INTO City (city_name, province_ID) VALUES
('Basco', 5),
('Tuguegarao City', 6),
('Ilagan City', 7), ('Cauayan City', 7), ('Santiago City', 7),
('Bayombong', 8),
('Cabarroguis', 9);

INSERT INTO City (city_name, province_ID) VALUES
('Baler', 10),
('Balanga City', 11),
('City of Malolos', 12), ('City of Meycauayan', 12), ('City of San Jose del Monte', 12),
('Palayan City', 13), ('Cabanatuan City', 13), ('Gapan City', 13), ('Muñoz City', 13), ('San Jose City', 13),
('City of San Fernando', 14), ('Angeles City', 14), ('Mabalacat City', 14),
('Tarlac City', 15),
('Iba', 16), ('Olongapo City', 16);

INSERT INTO City (city_name, province_ID) VALUES
('Batangas City', 17), ('Lipa City', 17), ('Tanauan City', 17),
('Trece Martires City', 18), ('Bacoor City', 18), ('Cavite City', 18), ('Dasmariñas City', 18), ('General Trias City', 18), ('Imus City', 18), ('Tagaytay City', 18),
('Santa Cruz', 19), ('Biñan City', 19), ('Cabuyao City', 19), ('Calamba City', 19), ('San Pablo City', 19), ('San Pedro City', 19), ('City of Santa Rosa', 19),
('Lucena City', 20), ('City of Tayabas', 20),
('Antipolo City', 21);

INSERT INTO City (city_name, province_ID) VALUES
('Boac', 22),
('Mamburao', 23),
('Calapan City', 24),
('Puerto Princesa City', 25),
('Romblon', 26);

INSERT INTO City (city_name, province_ID) VALUES
('Legazpi City', 27), ('Ligao City', 27), ('Tabaco City', 27),
('Daet', 28),
('Pili', 29), ('Iriga City', 29), ('Naga City', 29),
('Virac', 30),
('Masbate City', 31),
('Sorsogon City', 32);


INSERT INTO City (city_name, province_ID) VALUES
('Kalibo', 33),
('San Jose de Buenavista', 34),
('Roxas City', 35),
('Jordan', 36),
('Iloilo City', 37), ('Passi City', 37),
('Bacolod City', 38), ('Bago City', 38), ('Cadiz City', 38), ('Escalante City', 38), ('Himamaylan City', 38), ('Kabankalan City', 38), ('La Carlota City', 38), ('Sagay City', 38), ('San Carlos City', 38), ('Silay City', 38), ('Sipalay City', 38), ('Talisay City', 38), ('Victorias City', 38);

INSERT INTO City (city_name, province_ID) VALUES
('Tagbilaran City', 39),
('Cebu City', 40), ('Carcar City', 40), ('Danao City', 40), ('Lapu-Lapu City', 40), ('Mandaue City', 40), ('Naga City', 40), ('Talisay City', 40), ('Toledo City', 40),
('Dumaguete City', 41), ('Bais City', 41), ('Bayawan City', 41), ('Canlaon City', 41), ('Guihulngan City', 41), ('Tanjay City', 41),
('Siquijor', 42);

INSERT INTO City (city_name, province_ID) VALUES
('Naval', 43),
('City of Borongan', 44),
('Tacloban City', 45), ('Baybay City', 45), ('Ormoc City', 45),
('Catarman', 46),
('City of Catbalogan', 47), ('Calbayog City', 47),
('Maasin City', 48);

INSERT INTO City (city_name, province_ID) VALUES
('Dipolog City', 49), ('Dapitan City', 49),
('Pagadian City', 50),
('Ipil', 51),
('Zamboanga City', 52);

INSERT INTO City (city_name, province_ID) VALUES
('Malaybalay City', 53), ('Valencia City', 53),
('Mambajao', 54),
('Tubod', 55), ('Iligan City', 55),
('Oroquieta City', 56), ('Ozamiz City', 56), ('Tangub City', 56),
('Cagayan de Oro City', 57), ('City of El Salvador', 57), ('Gingoog City', 57);

INSERT INTO City (city_name, province_ID) VALUES
('Nabunturan', 58),
('Tagum City', 59), ('Panabo City', 59), ('Samal City', 59),
('Digos City', 60),
('Malita', 61),
('City of Mati', 62);

INSERT INTO City (city_name, province_ID) VALUES
('Kidapawan City', 63),
('Alabel', 64),
('Koronadal City', 65),
('Tacurong City', 66),
('General Santos City', 67);

INSERT INTO City (city_name, province_ID) VALUES
('Butuan City', 68), ('Cabadbaran City', 68),
('Bayugan City', 69),
('San Jose', 70),
('Surigao City', 71),
('Tandag City', 72), ('Bislig City', 72);

INSERT INTO City (city_name, province_ID) VALUES
('Lamitan City', 73), ('Isabela City', 73),
('Marawi City', 74),
('Shariff Aguak', 75), ('Cotabato City', 75),
('Jolo', 76),
('Bongao', 77);

INSERT INTO City (city_name, province_ID) VALUES
('Bangued', 78),
('Luna', 79),
('La Trinidad', 80), ('Baguio City', 80),
('Lagawe', 81),
('City of Tabuk', 82),
('Bontoc', 83);

-- ============================================
-- BARANGAYS FOR MAJOR CITIES
-- ============================================

-- Zamboanga City Barangays (98 barangays)
-- Note: Zamboanga City is the main focus of this tourism system
INSERT INTO Barangay (barangay_name, city_ID) 
SELECT barangay_name, city_ID FROM (
    SELECT 'Arena Blanco' as barangay_name, city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Ayala', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Baliwasan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Baluno', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Boalan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Bolong', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Buenavista', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Bunguiao', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Busay', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Cabaluay', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Cabatangan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Cacao', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Calabasa', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Calarian', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Camino Nuevo', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Campo Islam', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Canelar', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Capisan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Cawit', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Culianan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Curuan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Dita', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Divisoria', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Dulian (Upper Bunguiao)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Dulian (Lower Pasonanca)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Guisao', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Guiwan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Kasanyangan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'La Paz', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Labuan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lamisahan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Landang Gua', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Landang Laum', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lanzones', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lapakan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Latuan (Curuan)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Licomo', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Limaong', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Limpapa', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lubigan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lumayang', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lumbangan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Lunzuran', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Maasin', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Malagutay', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Mampang', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Manalipa', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Mangusu', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Manicahan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Mariki', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Mercedes', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Muti', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Pamucutan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Pangapuyan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Panubigan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Pasilmanta (Sacol Island)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Pasobolong', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Pasonanca', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Patalon', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Putik', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Quiniput', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Recodo', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Rio Hondo', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Salaan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'San Jose Cawa-cawa', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'San Jose Gusu', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'San Roque', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Sangali', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Santa Barbara', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Santa Catalina', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Santa Maria', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Santo Niño', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Sibulao (Caruan)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Sinubung', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Sinunoc', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tagasilay', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Taguiti', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Talabaan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Talisayan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Talon-talon', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Taluksangay', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tetuan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tictapul', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tigbalabag', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tigtabon', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tolosa', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tomas Claudio', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tugbungan', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tulungatung', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tumaga', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tumalutab', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Tumitus', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Victoria', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Vitali', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Zambowood', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Zone I (Poblacion)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Zone II (Poblacion)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Zone III (Poblacion)', city_ID FROM City WHERE city_name = 'Zamboanga City' UNION ALL
    SELECT 'Zone IV (Poblacion)', city_ID FROM City WHERE city_name = 'Zamboanga City'
) AS zamboanga_barangays;

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('San Lorenzo (Poblacion)', 1),
('Santa Joaquina (Poblacion)', 1),
('Nuestra Señora del Rosario (Poblacion)', 1),
('San Guillermo (Poblacion)', 1),
('San Pedro (Poblacion)', 1),
('San Agustin (Poblacion)', 1),
('Nuestra Señora del Natividad (Poblacion – 7-A)', 1),
('Nuestra Señora del Natividad (Poblacion – 7-B)', 1),
('San Vicente (Poblacion)', 1),
('Santa Angela (Poblacion)', 1);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Aglipay (Poblacion)', 2),
('Baay', 2),
('Baligat', 2),
('Bungon', 2),
('Baoa East', 2),
('Baoa West', 2),
('Barani (Poblacion)', 2),
('Ben-agan (Poblacion)', 2),
('Bil-loca', 2),
('Biningan', 2);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Ayusan Norte', 3),
('Ayusan Sur', 3),
('Barangay I (Poblacion)', 3),
('Barangay II (Poblacion)', 3),
('Barangay III (Poblacion)', 3),
('Barangay IV (Poblacion)', 3),
('Barangay V (Poblacion)', 3),
('Barangay VI (Poblacion)', 3),
('Barraca', 3),
('Beddeng Laud', 3);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Allangigan Primero', 4),
('Allangigan Segundo', 4),
('Amguid', 4),
('Ayudante', 4),
('Bagani Camposanto', 4),
('Bagani Gabor', 4),
('Bagani Tocgo', 4),
('Bagani Ubbog', 4),
('Bagar', 4),
('Balingaoan', 4);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Apaleng', 5),
('Bacsil', 5),
('Bangbangolan', 5),
('Bangcusay', 5),
('Barangay I (Poblacion)', 5),
('Barangay II (Poblacion)', 5),
('Barangay III (Poblacion)', 5),
('Barangay IV (Poblacion)', 5),
('Barangay V (Poblacion)', 5),
('Barangay VI (Poblacion)', 5);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Aliwekwek', 6),
('Baay', 6),
('Balangobong', 6),
('Balococ', 6),
('Basing', 6),
('Capandanan', 6),
('Domalandan Center', 6),
('Domalandan East', 6),
('Domalandan West', 6),
('Dorongan', 6);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Alos', 7),
('Amandiego', 7),
('Amangbangan', 7),
('Balangobong', 7),
('Balayang', 7),
('Bisocol', 7),
('Bolaney', 7),
('Baleyadaan', 7),
('Bued', 7),
('Cabatuan', 7);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Poblacion Zone I', 8),
('Poblacion Zone II', 8),
('Poblacion Zone III', 8),
('Poblacion Zone IV', 8),
('Poblacion Zone V', 8),
('Bonuan Gueset', 8),
('Bonuan Boquig', 8),
('Pantal', 8),
('Poblacion Zone VI', 8),
('Pueblo North', 8);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Poblacion East', 9),
('Poblacion West', 9),
('Salomague Norte', 9),
('Salomague Sur', 9),
('Transfiguration', 9),
('Ginmalig', 9),
('Talon', 9),
('San Jose', 9),
('San Pedro', 9),
('Ambalad', 9);

INSERT INTO Barangay (barangay_name, city_ID) VALUES
('Balog-bog', 10),
('Dagupan', 10),
('Lepanto', 10),
('Mabini', 10),
('Magallanes', 10),
('Manaoag', 10),
('Poblacion East', 10),
('Poblacion West', 10),
('San Pedro', 10),
('Umingan', 10);

-- ============================================
-- BARANGAYS FOR TOP 10 MAJOR CITIES IN THE PHILIPPINES
-- ============================================
-- This file contains barangays for the most populous and important cities
-- Import this after ph-location.sql

-- Manila City Barangays (897 barangays - showing major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Binondo' as barangay_name, city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Ermita', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Intramuros', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Malate', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Paco', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Pandacan', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Port Area', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Quiapo', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Sampaloc', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'San Andres', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'San Miguel', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'San Nicolas', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Santa Ana', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Santa Cruz', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Santa Mesa', city_ID FROM City WHERE city_name = 'Manila' UNION ALL
    SELECT 'Tondo', city_ID FROM City WHERE city_name = 'Manila'
) AS manila_barangays;

-- Quezon City Barangays (142 barangays - showing major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bagong Pag-asa' as barangay_name, city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Batasan Hills', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Commonwealth', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Cubao', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Diliman', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Fairview', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Kamuning', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Libis', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Novaliches', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Project 4', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Project 6', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Project 8', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'San Francisco del Monte', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Tandang Sora', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'Ugong Norte', city_ID FROM City WHERE city_name = 'Quezon City' UNION ALL
    SELECT 'White Plains', city_ID FROM City WHERE city_name = 'Quezon City'
) AS quezon_barangays;

-- Davao City Barangays (182 barangays - showing major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Agdao' as barangay_name, city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Buhangin', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Bunawan', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Calinan', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Matina', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Panacan', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Poblacion', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Sasa', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Talomo', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Toril', city_ID FROM City WHERE city_name = 'Davao City' UNION ALL
    SELECT 'Tugbok', city_ID FROM City WHERE city_name = 'Davao City'
) AS davao_barangays;

-- Cebu City Barangays (80 barangays - showing major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Apas' as barangay_name, city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Banilad', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Basak Pardo', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Basak San Nicolas', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Capitol Site', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Guadalupe', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Lahug', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Mabolo', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Pardo', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Pit-os', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Talamban', city_ID FROM City WHERE city_name = 'Cebu City' UNION ALL
    SELECT 'Tisa', city_ID FROM City WHERE city_name = 'Cebu City'
) AS cebu_barangays;

-- Caloocan City Barangays (188 barangays - showing major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bagong Barrio' as barangay_name, city_ID FROM City WHERE city_name = 'Caloocan City' UNION ALL
    SELECT 'Bagong Silang', city_ID FROM City WHERE city_name = 'Caloocan City' UNION ALL
    SELECT 'Camarin', city_ID FROM City WHERE city_name = 'Caloocan City' UNION ALL
    SELECT 'Grace Park', city_ID FROM City WHERE city_name = 'Caloocan City' UNION ALL
    SELECT 'Maypajo', city_ID FROM City WHERE city_name = 'Caloocan City' UNION ALL
    SELECT 'Sangandaan', city_ID FROM City WHERE city_name = 'Caloocan City' UNION ALL
    SELECT 'Tala', city_ID FROM City WHERE city_name = 'Caloocan City'
) AS caloocan_barangays;

-- Makati City Barangays (33 barangays)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bel-Air' as barangay_name, city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Carmona', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Dasmariñas', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Forbes Park', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Guadalupe Nuevo', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Guadalupe Viejo', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Magallanes', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Olympia', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Palanan', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Poblacion', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Rockwell', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Salcedo', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'San Antonio', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'San Isidro', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'San Lorenzo', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Santa Cruz', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Singkamas', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Tejeros', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Urdaneta', city_ID FROM City WHERE city_name = 'Makati City' UNION ALL
    SELECT 'Valenzuela', city_ID FROM City WHERE city_name = 'Makati City'
) AS makati_barangays;

-- Taguig City Barangays (38 barangays - showing major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bagumbayan' as barangay_name, city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Bambang', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Bonifacio Global City', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Fort Bonifacio', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Hagonoy', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Ibayo-Tipas', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Ligid-Tipas', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Lower Bicutan', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Maharlika Village', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Napindan', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Palingon', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Pinagsama', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'San Miguel', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Santa Ana', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Tanyag', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Tuktukan', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Upper Bicutan', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Ususan', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Wawa', city_ID FROM City WHERE city_name = 'Taguig City' UNION ALL
    SELECT 'Western Bicutan', city_ID FROM City WHERE city_name = 'Taguig City'
) AS taguig_barangays;

-- Pasig City Barangays (30 barangays)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bagong Ilog' as barangay_name, city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Bagong Katipunan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Bambang', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Buting', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Caniogan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Dela Paz', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Kalawaan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Kapasigan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Kapitolyo', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Malinao', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Manggahan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Maybunga', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Oranbo', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Palatiw', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Pinagbuhatan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Pineda', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Rosario', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Sagad', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'San Antonio', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'San Joaquin', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'San Jose', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'San Miguel', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'San Nicolas', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Santa Cruz', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Santa Lucia', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Santa Rosa', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Santolan', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Sumilang', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Ugong', city_ID FROM City WHERE city_name = 'Pasig City' UNION ALL
    SELECT 'Valencia', city_ID FROM City WHERE city_name = 'Pasig City'
) AS pasig_barangays;

-- Antipolo City Barangays (16 barangays - major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bagong Nayon' as barangay_name, city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Beverly Hills', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Cupang', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Dalig', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Dela Paz', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Inarawan', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Mambugan', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Mayamot', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'San Isidro', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'San Jose', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'San Luis', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'San Roque', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Santa Cruz', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Santo Niño', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Taktak', city_ID FROM City WHERE city_name = 'Antipolo City' UNION ALL
    SELECT 'Valley Golf', city_ID FROM City WHERE city_name = 'Antipolo City'
) AS antipolo_barangays;

-- Cagayan de Oro City Barangays (80 barangays - major ones)
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Agusan' as barangay_name, city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Balulang', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Bugo', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Bulua', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Carmen', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Gusa', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Kauswagan', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Lapasan', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Lumbia', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Macabalan', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Macasandig', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Nazareth', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Patag', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Puerto', city_ID FROM City WHERE city_name = 'Cagayan de Oro City' UNION ALL
    SELECT 'Tablon', city_ID FROM City WHERE city_name = 'Cagayan de Oro City'
) AS cdo_barangays;

-- Success message
SELECT 'Barangays for major cities added successfully!' as message,
       'Total cities covered: 11 (Zamboanga + Top 10)' as coverage,
       'Users can add more barangays as needed' as note;


