-- ============================================
-- GLOBAL LOCATION DATA
-- Countries, Provinces/States, Cities, and Districts
-- ============================================

-- Assuming a complete DDL for the tables:

-- 1. Country Table (already implemented in your original structure)
ALTER TABLE Country ADD UNIQUE (country_name);
ALTER TABLE Country ADD UNIQUE (country_codename);

-- 2. Province/State Table
-- Ensures the same province name cannot exist twice within the same country.
ALTER TABLE Province ADD UNIQUE (province_name, country_ID);

-- 3. City Table
-- Ensures the same city name cannot exist twice within the same province/state.
ALTER TABLE City ADD UNIQUE (city_name, province_ID);

-- 4. District Table
-- Ensures the same district name cannot exist twice within the same city.
ALTER TABLE District ADD UNIQUE (district_name, city_ID);



-- Insert Countries

INSERT INTO Country (country_name, country_codename, countrycode_codenumber) VALUES
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

-- ============================================
-- UNITED STATES - States, Cities, Districts
-- ============================================

-- California
INSERT INTO Province (province_name, country_ID) 
SELECT 'California', country_ID FROM Country WHERE country_codenamename = 'US';

INSERT INTO City (city_name, province_ID)
SELECT 'Los Angeles', province_ID FROM Province WHERE province_name = 'California';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'Hollywood' UNION ALL
    SELECT 'Beverly Hills' UNION ALL
    SELECT 'Santa Monica' UNION ALL
    SELECT 'Venice'
) AS districts
CROSS JOIN City WHERE city_name = 'Los Angeles';

INSERT INTO City (city_name, province_ID)
SELECT 'San Francisco', province_ID FROM Province WHERE province_name = 'California';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Financial District' AS barangay_name UNION ALL
    SELECT 'Chinatown' UNION ALL
    SELECT 'Mission District' UNION ALL
    SELECT 'North Beach' UNION ALL
    SELECT 'Haight-Ashbury'
) AS districts
CROSS JOIN City WHERE city_name = 'San Francisco';

INSERT INTO City (city_name, province_ID)
SELECT 'San Diego', province_ID FROM Province WHERE province_name = 'California';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'La Jolla' UNION ALL
    SELECT 'Pacific Beach' UNION ALL
    SELECT 'Gaslamp Quarter'
) AS districts
CROSS JOIN City WHERE city_name = 'San Diego';

-- New York
INSERT INTO Province (province_name, country_ID) 
SELECT 'New York', country_ID FROM Country WHERE country_codename = 'US';

INSERT INTO City (city_name, province_ID)
SELECT 'New York City', province_ID FROM Province WHERE province_name = 'New York';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Manhattan' AS barangay_name UNION ALL
    SELECT 'Brooklyn' UNION ALL
    SELECT 'Queens' UNION ALL
    SELECT 'Bronx' UNION ALL
    SELECT 'Staten Island'
) AS districts
CROSS JOIN City WHERE city_name = 'New York City';

-- Texas
INSERT INTO Province (province_name, country_ID) 
SELECT 'Texas', country_ID FROM Country WHERE country_codename = 'US';

INSERT INTO City (city_name, province_ID)
SELECT 'Houston', province_ID FROM Province WHERE province_name = 'Texas';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'Midtown' UNION ALL
    SELECT 'Montrose' UNION ALL
    SELECT 'Heights'
) AS districts
CROSS JOIN City WHERE city_name = 'Houston';

INSERT INTO City (city_name, province_ID)
SELECT 'Austin', province_ID FROM Province WHERE province_name = 'Texas';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'South Congress' UNION ALL
    SELECT 'East Austin' UNION ALL
    SELECT 'West Lake Hills'
) AS districts
CROSS JOIN City WHERE city_name = 'Austin';

-- Florida
INSERT INTO Province (province_name, country_ID) 
SELECT 'Florida', country_ID FROM Country WHERE country_codename = 'US';

INSERT INTO City (city_name, province_ID)
SELECT 'Miami', province_ID FROM Province WHERE province_name = 'Florida';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'South Beach' UNION ALL
    SELECT 'Coral Gables' UNION ALL
    SELECT 'Wynwood'
) AS districts
CROSS JOIN City WHERE city_name = 'Miami';

-- ============================================
-- UNITED KINGDOM - Regions, Cities, Districts
-- ============================================

-- England
INSERT INTO Province (province_name, country_ID) 
SELECT 'England', country_ID FROM Country WHERE country_codename = 'GB';

INSERT INTO City (city_name, province_ID)
SELECT 'London', province_ID FROM Province WHERE province_name = 'England';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Westminster' AS barangay_name UNION ALL
    SELECT 'Camden' UNION ALL
    SELECT 'Kensington' UNION ALL
    SELECT 'Tower Hamlets' UNION ALL
    SELECT 'Southwark'
) AS districts
CROSS JOIN City WHERE city_name = 'London';

INSERT INTO City (city_name, province_ID)
SELECT 'Manchester', province_ID FROM Province WHERE province_name = 'England';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'City Centre' AS barangay_name UNION ALL
    SELECT 'Northern Quarter' UNION ALL
    SELECT 'Ancoats' UNION ALL
    SELECT 'Salford'
) AS districts
CROSS JOIN City WHERE city_name = 'Manchester';

-- Scotland
INSERT INTO Province (province_name, country_ID) 
SELECT 'Scotland', country_ID FROM Country WHERE country_codename = 'GB';

INSERT INTO City (city_name, province_ID)
SELECT 'Edinburgh', province_ID FROM Province WHERE province_name = 'Scotland';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Old Town' AS barangay_name UNION ALL
    SELECT 'New Town' UNION ALL
    SELECT 'Leith' UNION ALL
    SELECT 'Stockbridge'
) AS districts
CROSS JOIN City WHERE city_name = 'Edinburgh';

-- ============================================
-- FRANCE - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Île-de-France', country_ID FROM Country WHERE country_codename = 'FR';

INSERT INTO City (city_name, province_ID)
SELECT 'Paris', province_ID FROM Province WHERE province_name = 'Île-de-France';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT '1st Arrondissement' AS barangay_name UNION ALL
    SELECT '8th Arrondissement' UNION ALL
    SELECT 'Marais' UNION ALL
    SELECT 'Montmartre' UNION ALL
    SELECT 'Latin Quarter'
) AS districts
CROSS JOIN City WHERE city_name = 'Paris';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Provence-Alpes-Côte d''Azur', country_ID FROM Country WHERE country_codename = 'FR';

INSERT INTO City (city_name, province_ID)
SELECT 'Nice', province_ID FROM Province WHERE province_name = 'Provence-Alpes-Côte d''Azur';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Vieux Nice' AS barangay_name UNION ALL
    SELECT 'Promenade des Anglais' UNION ALL
    SELECT 'Cimiez' UNION ALL
    SELECT 'Port'
) AS districts
CROSS JOIN City WHERE city_name = 'Nice';

-- ============================================
-- GERMANY - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Bavaria', country_ID FROM Country WHERE country_codename = 'DE';

INSERT INTO City (city_name, province_ID)
SELECT 'Munich', province_ID FROM Province WHERE province_name = 'Bavaria';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Altstadt' AS barangay_name UNION ALL
    SELECT 'Schwabing' UNION ALL
    SELECT 'Maxvorstadt' UNION ALL
    SELECT 'Haidhausen'
) AS districts
CROSS JOIN City WHERE city_name = 'Munich';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Berlin', country_ID FROM Country WHERE country_codename = 'DE';

INSERT INTO City (city_name, province_ID)
SELECT 'Berlin', province_ID FROM Province WHERE province_name = 'Berlin';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Mitte' AS barangay_name UNION ALL
    SELECT 'Kreuzberg' UNION ALL
    SELECT 'Charlottenburg' UNION ALL
    SELECT 'Prenzlauer Berg' UNION ALL
    SELECT 'Friedrichshain'
) AS districts
CROSS JOIN City WHERE city_name = 'Berlin';

-- ============================================
-- JAPAN - Prefectures, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Tokyo', country_ID FROM Country WHERE country_codename = 'JP';

INSERT INTO City (city_name, province_ID)
SELECT 'Tokyo', province_ID FROM Province WHERE province_name = 'Tokyo';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Shibuya' AS barangay_name UNION ALL
    SELECT 'Shinjuku' UNION ALL
    SELECT 'Minato' UNION ALL
    SELECT 'Chiyoda' UNION ALL
    SELECT 'Chuo'
) AS districts
CROSS JOIN City WHERE city_name = 'Tokyo';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Osaka', country_ID FROM Country WHERE country_codename = 'JP';

INSERT INTO City (city_name, province_ID)
SELECT 'Osaka', province_ID FROM Province WHERE province_name = 'Osaka';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Kita' AS barangay_name UNION ALL
    SELECT 'Chuo' UNION ALL
    SELECT 'Namba' UNION ALL
    SELECT 'Tennoji'
) AS districts
CROSS JOIN City WHERE city_name = 'Osaka';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Kyoto', country_ID FROM Country WHERE country_codename = 'JP';

INSERT INTO City (city_name, province_ID)
SELECT 'Kyoto', province_ID FROM Province WHERE province_name = 'Kyoto';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Higashiyama' AS barangay_name UNION ALL
    SELECT 'Gion' UNION ALL
    SELECT 'Arashiyama' UNION ALL
    SELECT 'Fushimi'
) AS districts
CROSS JOIN City WHERE city_name = 'Kyoto';

-- ============================================
-- AUSTRALIA - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'New South Wales', country_ID FROM Country WHERE country_codename = 'AU';

INSERT INTO City (city_name, province_ID)
SELECT 'Sydney', province_ID FROM Province WHERE province_name = 'New South Wales';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'CBD' AS barangay_name UNION ALL
    SELECT 'Bondi' UNION ALL
    SELECT 'Manly' UNION ALL
    SELECT 'Darlinghurst' UNION ALL
    SELECT 'Surry Hills'
) AS districts
CROSS JOIN City WHERE city_name = 'Sydney';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Victoria', country_ID FROM Country WHERE country_codename = 'AU';

INSERT INTO City (city_name, province_ID)
SELECT 'Melbourne', province_ID FROM Province WHERE province_name = 'Victoria';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'CBD' AS barangay_name UNION ALL
    SELECT 'Fitzroy' UNION ALL
    SELECT 'St Kilda' UNION ALL
    SELECT 'Carlton' UNION ALL
    SELECT 'South Yarra'
) AS districts
CROSS JOIN City WHERE city_name = 'Melbourne';

-- ============================================
-- CANADA - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Ontario', country_ID FROM Country WHERE country_codename = 'CA';

INSERT INTO City (city_name, province_ID)
SELECT 'Toronto', province_ID FROM Province WHERE province_name = 'Ontario';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'Yorkville' UNION ALL
    SELECT 'The Annex' UNION ALL
    SELECT 'Distillery District' UNION ALL
    SELECT 'Kensington Market'
) AS districts
CROSS JOIN City WHERE city_name = 'Toronto';

INSERT INTO Province (province_name, country_ID) 
SELECT 'British Columbia', country_ID FROM Country WHERE country_codename = 'CA';

INSERT INTO City (city_name, province_ID)
SELECT 'Vancouver', province_ID FROM Province WHERE province_name = 'British Columbia';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'Gastown' UNION ALL
    SELECT 'Yaletown' UNION ALL
    SELECT 'Kitsilano' UNION ALL
    SELECT 'West End'
) AS districts
CROSS JOIN City WHERE city_name = 'Vancouver';

-- ============================================
-- ITALY - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Lazio', country_ID FROM Country WHERE country_codename = 'IT';

INSERT INTO City (city_name, province_ID)
SELECT 'Rome', province_ID FROM Province WHERE province_name = 'Lazio';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centro Storico' AS barangay_name UNION ALL
    SELECT 'Trastevere' UNION ALL
    SELECT 'Monti' UNION ALL
    SELECT 'Prati' UNION ALL
    SELECT 'Testaccio'
) AS districts
CROSS JOIN City WHERE city_name = 'Rome';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Lombardy', country_ID FROM Country WHERE country_codename = 'IT';

INSERT INTO City (city_name, province_ID)
SELECT 'Milan', province_ID FROM Province WHERE province_name = 'Lombardy';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centro' AS barangay_name UNION ALL
    SELECT 'Brera' UNION ALL
    SELECT 'Navigli' UNION ALL
    SELECT 'Porta Romana'
) AS districts
CROSS JOIN City WHERE city_name = 'Milan';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Veneto', country_ID FROM Country WHERE country_codename = 'IT';

INSERT INTO City (city_name, province_ID)
SELECT 'Venice', province_ID FROM Province WHERE province_name = 'Veneto';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'San Marco' AS barangay_name UNION ALL
    SELECT 'Cannaregio' UNION ALL
    SELECT 'Dorsoduro' UNION ALL
    SELECT 'Castello'
) AS districts
CROSS JOIN City WHERE city_name = 'Venice';

-- ============================================
-- SPAIN - Autonomous Communities, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Community of Madrid', country_ID FROM Country WHERE country_codename = 'ES';

INSERT INTO City (city_name, province_ID)
SELECT 'Madrid', province_ID FROM Province WHERE province_name = 'Community of Madrid';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centro' AS barangay_name UNION ALL
    SELECT 'Salamanca' UNION ALL
    SELECT 'Chamberí' UNION ALL
    SELECT 'Retiro' UNION ALL
    SELECT 'Malasaña'
) AS districts
CROSS JOIN City WHERE city_name = 'Madrid';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Catalonia', country_ID FROM Country WHERE country_codename = 'ES';

INSERT INTO City (city_name, province_ID)
SELECT 'Barcelona', province_ID FROM Province WHERE province_name = 'Catalonia';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Gothic Quarter' AS barangay_name UNION ALL
    SELECT 'Eixample' UNION ALL
    SELECT 'Gracia' UNION ALL
    SELECT 'El Born' UNION ALL
    SELECT 'Barceloneta'
) AS districts
CROSS JOIN City WHERE city_name = 'Barcelona';

-- ============================================
-- CHINA - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Beijing', country_ID FROM Country WHERE country_codename = 'CN';

INSERT INTO City (city_name, province_ID)
SELECT 'Beijing', province_ID FROM Province WHERE province_name = 'Beijing';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Dongcheng' AS barangay_name UNION ALL
    SELECT 'Xicheng' UNION ALL
    SELECT 'Chaoyang' UNION ALL
    SELECT 'Haidian' UNION ALL
    SELECT 'Fengtai'
) AS districts
CROSS JOIN City WHERE city_name = 'Beijing';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Shanghai', country_ID FROM Country WHERE country_codename = 'CN';

INSERT INTO City (city_name, province_ID)
SELECT 'Shanghai', province_ID FROM Province WHERE province_name = 'Shanghai';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Pudong' AS barangay_name UNION ALL
    SELECT 'Huangpu' UNION ALL
    SELECT 'Xuhui' UNION ALL
    SELECT 'Jing''an' UNION ALL
    SELECT 'Hongkou'
) AS districts
CROSS JOIN City WHERE city_name = 'Shanghai';

-- ============================================
-- SOUTH KOREA - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Seoul', country_ID FROM Country WHERE country_codename = 'KR';

INSERT INTO City (city_name, province_ID)
SELECT 'Seoul', province_ID FROM Province WHERE province_name = 'Seoul';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Gangnam' AS barangay_name UNION ALL
    SELECT 'Jongno' UNION ALL
    SELECT 'Jung' UNION ALL
    SELECT 'Mapo' UNION ALL
    SELECT 'Itaewon'
) AS districts
CROSS JOIN City WHERE city_name = 'Seoul';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Busan', country_ID FROM Country WHERE country_codename = 'KR';

INSERT INTO City (city_name, province_ID)
SELECT 'Busan', province_ID FROM Province WHERE province_name = 'Busan';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Haeundae' AS barangay_name UNION ALL
    SELECT 'Jung' UNION ALL
    SELECT 'Seo' UNION ALL
    SELECT 'Busanjin'
) AS districts
CROSS JOIN City WHERE city_name = 'Busan';

-- ============================================
-- THAILAND - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Bangkok', country_ID FROM Country WHERE country_codename = 'TH';

INSERT INTO City (city_name, province_ID)
SELECT 'Bangkok', province_ID FROM Province WHERE province_name = 'Bangkok';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Sukhumvit' AS barangay_name UNION ALL
    SELECT 'Silom' UNION ALL
    SELECT 'Siam' UNION ALL
    SELECT 'Rattanakosin' UNION ALL
    SELECT 'Chatuchak'
) AS districts
CROSS JOIN City WHERE city_name = 'Bangkok';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Phuket', country_ID FROM Country WHERE country_codename = 'TH';

INSERT INTO City (city_name, province_ID)
SELECT 'Phuket City', province_ID FROM Province WHERE province_name = 'Phuket';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Patong' AS barangay_name UNION ALL
    SELECT 'Kata' UNION ALL
    SELECT 'Karon' UNION ALL
    SELECT 'Old Town'
) AS districts
CROSS JOIN City WHERE city_name = 'Phuket City';

-- ============================================
-- SINGAPORE
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Singapore', country_ID FROM Country WHERE country_codename = 'SG';

INSERT INTO City (city_name, province_ID)
SELECT 'Singapore', province_ID FROM Province WHERE province_name = 'Singapore';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Marina Bay' AS barangay_name UNION ALL
    SELECT 'Orchard' UNION ALL
    SELECT 'Chinatown' UNION ALL
    SELECT 'Little India' UNION ALL
    SELECT 'Sentosa'
) AS districts
CROSS JOIN City WHERE city_name = 'Singapore';

-- ============================================
-- INDIA - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Maharashtra', country_ID FROM Country WHERE country_codename = 'IN';

INSERT INTO City (city_name, province_ID)
SELECT 'Mumbai', province_ID FROM Province WHERE province_name = 'Maharashtra';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'South Mumbai' AS barangay_name UNION ALL
    SELECT 'Bandra' UNION ALL
    SELECT 'Andheri' UNION ALL
    SELECT 'Juhu' UNION ALL
    SELECT 'Colaba'
) AS districts
CROSS JOIN City WHERE city_name = 'Mumbai';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Delhi', country_ID FROM Country WHERE country_codename = 'IN';

INSERT INTO City (city_name, province_ID)
SELECT 'New Delhi', province_ID FROM Province WHERE province_name = 'Delhi';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Connaught Place' AS barangay_name UNION ALL
    SELECT 'Karol Bagh' UNION ALL
    SELECT 'Chandni Chowk' UNION ALL
    SELECT 'Hauz Khas' UNION ALL
    SELECT 'Saket'
) AS districts
CROSS JOIN City WHERE city_name = 'New Delhi';

-- ============================================
-- UNITED ARAB EMIRATES - Emirates, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Dubai', country_ID FROM Country WHERE country_codename = 'AE';

INSERT INTO City (city_name, province_ID)
SELECT 'Dubai', province_ID FROM Province WHERE province_name = 'Dubai';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown Dubai' AS barangay_name UNION ALL
    SELECT 'Dubai Marina' UNION ALL
    SELECT 'Jumeirah' UNION ALL
    SELECT 'Deira' UNION ALL
    SELECT 'Business Bay'
) AS districts
CROSS JOIN City WHERE city_name = 'Dubai';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Abu Dhabi', country_ID FROM Country WHERE country_codename = 'AE';

INSERT INTO City (city_name, province_ID)
SELECT 'Abu Dhabi', province_ID FROM Province WHERE province_name = 'Abu Dhabi';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Al Markaziyah' AS barangay_name UNION ALL
    SELECT 'Al Zahiyah' UNION ALL
    SELECT 'Corniche' UNION ALL
    SELECT 'Yas Island'
) AS districts
CROSS JOIN City WHERE city_name = 'Abu Dhabi';

-- ============================================
-- BRAZIL - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'São Paulo', country_ID FROM Country WHERE country_codename = 'BR';

INSERT INTO City (city_name, province_ID)
SELECT 'São Paulo', province_ID FROM Province WHERE province_name = 'São Paulo';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centro' AS barangay_name UNION ALL
    SELECT 'Paulista' UNION ALL
    SELECT 'Vila Madalena' UNION ALL
    SELECT 'Jardins' UNION ALL
    SELECT 'Pinheiros'
) AS districts
CROSS JOIN City WHERE city_name = 'São Paulo';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Rio de Janeiro', country_ID FROM Country WHERE country_codename = 'BR';

INSERT INTO City (city_name, province_ID)
SELECT 'Rio de Janeiro', province_ID FROM Province WHERE province_name = 'Rio de Janeiro';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Copacabana' AS barangay_name UNION ALL
    SELECT 'Ipanema' UNION ALL
    SELECT 'Leblon' UNION ALL
    SELECT 'Centro' UNION ALL
    SELECT 'Barra da Tijuca'
) AS districts
CROSS JOIN City WHERE city_name = 'Rio de Janeiro';

-- ============================================
-- MEXICO - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Mexico City', country_ID FROM Country WHERE country_codename = 'MX';

INSERT INTO City (city_name, province_ID)
SELECT 'Mexico City', province_ID FROM Province WHERE province_name = 'Mexico City';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centro Histórico' AS barangay_name UNION ALL
    SELECT 'Polanco' UNION ALL
    SELECT 'Roma' UNION ALL
    SELECT 'Condesa' UNION ALL
    SELECT 'Coyoacán'
) AS districts
CROSS JOIN City WHERE city_name = 'Mexico City';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Quintana Roo', country_ID FROM Country WHERE country_codename = 'MX';

INSERT INTO City (city_name, province_ID)
SELECT 'Cancún', province_ID FROM Province WHERE province_name = 'Quintana Roo';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Hotel Zone' AS barangay_name UNION ALL
    SELECT 'Downtown' UNION ALL
    SELECT 'Puerto Juárez' UNION ALL
    SELECT 'Zona Hotelera'
) AS districts
CROSS JOIN City WHERE city_name = 'Cancún';

-- ============================================
-- ARGENTINA - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Buenos Aires', country_ID FROM Country WHERE country_codename = 'AR';

INSERT INTO City (city_name, province_ID)
SELECT 'Buenos Aires', province_ID FROM Province WHERE province_name = 'Buenos Aires';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Palermo' AS barangay_name UNION ALL
    SELECT 'Recoleta' UNION ALL
    SELECT 'San Telmo' UNION ALL
    SELECT 'Puerto Madero' UNION ALL
    SELECT 'Belgrano'
) AS districts
CROSS JOIN City WHERE city_name = 'Buenos Aires';

-- ============================================
-- PHILIPPINES - Regions, Cities, Barangays
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Metro Manila', country_ID FROM Country WHERE country_codename = 'PH';

INSERT INTO City (city_name, province_ID)
SELECT 'Manila', province_ID FROM Province WHERE province_name = 'Metro Manila';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Ermita' AS barangay_name UNION ALL
    SELECT 'Malate' UNION ALL
    SELECT 'Intramuros' UNION ALL
    SELECT 'Binondo' UNION ALL
    SELECT 'Quiapo'
) AS districts
CROSS JOIN City WHERE city_name = 'Manila';

INSERT INTO City (city_name, province_ID)
SELECT 'Quezon City', province_ID FROM Province WHERE province_name = 'Metro Manila';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Diliman' AS barangay_name UNION ALL
    SELECT 'Cubao' UNION ALL
    SELECT 'Kamuning' UNION ALL
    SELECT 'Loyola Heights' UNION ALL
    SELECT 'Commonwealth'
) AS districts
CROSS JOIN City WHERE city_name = 'Quezon City';

INSERT INTO City (city_name, province_ID)
SELECT 'Makati', province_ID FROM Province WHERE province_name = 'Metro Manila';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Poblacion' AS barangay_name UNION ALL
    SELECT 'Salcedo Village' UNION ALL
    SELECT 'Legaspi Village' UNION ALL
    SELECT 'Bel-Air' UNION ALL
    SELECT 'San Lorenzo'
) AS districts
CROSS JOIN City WHERE city_name = 'Makati';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Zamboanga Peninsula', country_ID FROM Country WHERE country_codename = 'PH';

INSERT INTO City (city_name, province_ID)
SELECT 'Zamboanga City', province_ID FROM Province WHERE province_name = 'Zamboanga Peninsula';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Pasonanca' AS barangay_name UNION ALL
    SELECT 'Tetuan' UNION ALL
    SELECT 'Canelar' UNION ALL
    SELECT 'Sta. Maria' UNION ALL
    SELECT 'Taluksangay' UNION ALL
    SELECT 'Panubigan' UNION ALL
    SELECT 'Sibulao'
) AS districts
CROSS JOIN City WHERE city_name = 'Zamboanga City';

-- ============================================
-- NETHERLANDS - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'North Holland', country_ID FROM Country WHERE country_codename = 'NL';

INSERT INTO City (city_name, province_ID)
SELECT 'Amsterdam', province_ID FROM Province WHERE province_name = 'North Holland';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centrum' AS barangay_name UNION ALL
    SELECT 'Jordaan' UNION ALL
    SELECT 'De Pijp' UNION ALL
    SELECT 'Oud-West' UNION ALL
    SELECT 'Noord'
) AS districts
CROSS JOIN City WHERE city_name = 'Amsterdam';

-- ============================================
-- SWITZERLAND - Cantons, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Zurich', country_ID FROM Country WHERE country_codename = 'CH';

INSERT INTO City (city_name, province_ID)
SELECT 'Zurich', province_ID FROM Province WHERE province_name = 'Zurich';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Altstadt' AS barangay_name UNION ALL
    SELECT 'Seefeld' UNION ALL
    SELECT 'Wiedikon' UNION ALL
    SELECT 'Oerlikon'
) AS districts
CROSS JOIN City WHERE city_name = 'Zurich';

-- ============================================
-- TURKEY - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Istanbul', country_ID FROM Country WHERE country_codename = 'TR';

INSERT INTO City (city_name, province_ID)
SELECT 'Istanbul', province_ID FROM Province WHERE province_name = 'Istanbul';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Sultanahmet' AS barangay_name UNION ALL
    SELECT 'Beyoğlu' UNION ALL
    SELECT 'Beşiktaş' UNION ALL
    SELECT 'Kadıköy' UNION ALL
    SELECT 'Taksim'
) AS districts
CROSS JOIN City WHERE city_name = 'Istanbul';

-- ============================================
-- EGYPT - Governorates, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Cairo', country_ID FROM Country WHERE country_codename = 'EG';

INSERT INTO City (city_name, province_ID)
SELECT 'Cairo', province_ID FROM Province WHERE province_name = 'Cairo';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Downtown' AS barangay_name UNION ALL
    SELECT 'Zamalek' UNION ALL
    SELECT 'Heliopolis' UNION ALL
    SELECT 'Maadi' UNION ALL
    SELECT 'Giza'
) AS districts
CROSS JOIN City WHERE city_name = 'Cairo';

-- ============================================
-- SOUTH AFRICA - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Western Cape', country_ID FROM Country WHERE country_codename = 'ZA';

INSERT INTO City (city_name, province_ID)
SELECT 'Cape Town', province_ID FROM Province WHERE province_name = 'Western Cape';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'City Bowl' AS barangay_name UNION ALL
    SELECT 'Waterfront' UNION ALL
    SELECT 'Sea Point' UNION ALL
    SELECT 'Camps Bay' UNION ALL
    SELECT 'Constantia'
) AS districts
CROSS JOIN City WHERE city_name = 'Cape Town';

-- ============================================
-- NEW ZEALAND - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Auckland', country_ID FROM Country WHERE country_codename = 'NZ';

INSERT INTO City (city_name, province_ID)
SELECT 'Auckland', province_ID FROM Province WHERE province_name = 'Auckland';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'CBD' AS barangay_name UNION ALL
    SELECT 'Ponsonby' UNION ALL
    SELECT 'Parnell' UNION ALL
    SELECT 'Mission Bay' UNION ALL
    SELECT 'Devonport'
) AS districts
CROSS JOIN City WHERE city_name = 'Auckland';

-- ============================================
-- SAMPLE ADDRESS DATA
-- ============================================

-- Sample addresses for various cities
INSERT INTO Address_Info (address_houseno, address_street, barangay_ID)
SELECT '123', 'Main Street', barangay_ID FROM Barangay WHERE barangay_name = 'Downtown' LIMIT 1;

INSERT INTO Address_Info (address_houseno, address_street, barangay_ID)
SELECT '456', 'Broadway', barangay_ID FROM Barangay WHERE barangay_name = 'Manhattan' LIMIT 1;

INSERT INTO Address_Info (address_houseno, address_street, barangay_ID)
SELECT '789', 'Oxford Street', barangay_ID FROM Barangay WHERE barangay_name = 'Westminster' LIMIT 1;

INSERT INTO Address_Info (address_houseno, address_street, barangay_ID)
SELECT '321', 'Champs-Élysées', barangay_ID FROM Barangay WHERE barangay_name = '8th Arrondissement' LIMIT 1;

INSERT INTO Address_Info (address_houseno, address_street, barangay_ID)
SELECT '654', 'Shibuya Crossing', barangay_ID FROM Barangay WHERE barangay_name = 'Shibuya' LIMIT 1;

-- ============================================
-- INDONESIA - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Jakarta', country_ID FROM Country WHERE country_codename = 'ID';

INSERT INTO City (city_name, province_ID)
SELECT 'Jakarta', province_ID FROM Province WHERE province_name = 'Jakarta';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Central Jakarta' AS barangay_name UNION ALL
    SELECT 'South Jakarta' UNION ALL
    SELECT 'North Jakarta' UNION ALL
    SELECT 'West Jakarta' UNION ALL
    SELECT 'East Jakarta'
) AS districts
CROSS JOIN City WHERE city_name = 'Jakarta';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Bali', country_ID FROM Country WHERE country_codename = 'ID';

INSERT INTO City (city_name, province_ID)
SELECT 'Denpasar', province_ID FROM Province WHERE province_name = 'Bali';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Sanur' AS barangay_name UNION ALL
    SELECT 'Denpasar Barat' UNION ALL
    SELECT 'Denpasar Timur' UNION ALL
    SELECT 'Denpasar Selatan'
) AS districts
CROSS JOIN City WHERE city_name = 'Denpasar';

-- ============================================
-- MALAYSIA - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Kuala Lumpur', country_ID FROM Country WHERE country_codename = 'MY';

INSERT INTO City (city_name, province_ID)
SELECT 'Kuala Lumpur', province_ID FROM Province WHERE province_name = 'Kuala Lumpur';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Bukit Bintang' AS barangay_name UNION ALL
    SELECT 'KLCC' UNION ALL
    SELECT 'Chinatown' UNION ALL
    SELECT 'Bangsar' UNION ALL
    SELECT 'Mont Kiara'
) AS districts
CROSS JOIN City WHERE city_name = 'Kuala Lumpur';

-- ============================================
-- VIETNAM - Provinces, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Ho Chi Minh City', country_ID FROM Country WHERE country_codename = 'VN';

INSERT INTO City (city_name, province_ID)
SELECT 'Ho Chi Minh City', province_ID FROM Province WHERE province_name = 'Ho Chi Minh City';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'District 1' AS barangay_name UNION ALL
    SELECT 'District 3' UNION ALL
    SELECT 'Binh Thanh' UNION ALL
    SELECT 'Phu Nhuan' UNION ALL
    SELECT 'Tan Binh'
) AS districts
CROSS JOIN City WHERE city_name = 'Ho Chi Minh City';

INSERT INTO Province (province_name, country_ID) 
SELECT 'Hanoi', country_ID FROM Country WHERE country_codename = 'VN';

INSERT INTO City (city_name, province_ID)
SELECT 'Hanoi', province_ID FROM Province WHERE province_name = 'Hanoi';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Hoan Kiem' AS barangay_name UNION ALL
    SELECT 'Ba Dinh' UNION ALL
    SELECT 'Dong Da' UNION ALL
    SELECT 'Tay Ho'
) AS districts
CROSS JOIN City WHERE city_name = 'Hanoi';

-- ============================================
-- PORTUGAL - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Lisbon', country_ID FROM Country WHERE country_codename = 'PT';

INSERT INTO City (city_name, province_ID)
SELECT 'Lisbon', province_ID FROM Province WHERE province_name = 'Lisbon';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Baixa' AS barangay_name UNION ALL
    SELECT 'Alfama' UNION ALL
    SELECT 'Bairro Alto' UNION ALL
    SELECT 'Belém' UNION ALL
    SELECT 'Chiado'
) AS districts
CROSS JOIN City WHERE city_name = 'Lisbon';

-- ============================================
-- GREECE - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Attica', country_ID FROM Country WHERE country_codename = 'GR';

INSERT INTO City (city_name, province_ID)
SELECT 'Athens', province_ID FROM Province WHERE province_name = 'Attica';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Plaka' AS barangay_name UNION ALL
    SELECT 'Monastiraki' UNION ALL
    SELECT 'Kolonaki' UNION ALL
    SELECT 'Syntagma' UNION ALL
    SELECT 'Psiri'
) AS districts
CROSS JOIN City WHERE city_name = 'Athens';

-- ============================================
-- IRELAND - Counties, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Dublin', country_ID FROM Country WHERE country_codename = 'IE';

INSERT INTO City (city_name, province_ID)
SELECT 'Dublin', province_ID FROM Province WHERE province_name = 'Dublin';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Temple Bar' AS barangay_name UNION ALL
    SELECT 'Ballsbridge' UNION ALL
    SELECT 'Rathmines' UNION ALL
    SELECT 'Smithfield' UNION ALL
    SELECT 'Docklands'
) AS districts
CROSS JOIN City WHERE city_name = 'Dublin';

-- ============================================
-- POLAND - Voivodeships, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Masovian', country_ID FROM Country WHERE country_codename = 'PL';

INSERT INTO City (city_name, province_ID)
SELECT 'Warsaw', province_ID FROM Province WHERE province_name = 'Masovian';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Śródmieście' AS barangay_name UNION ALL
    SELECT 'Mokotów' UNION ALL
    SELECT 'Praga' UNION ALL
    SELECT 'Żoliborz'
) AS districts
CROSS JOIN City WHERE city_name = 'Warsaw';

-- ============================================
-- AUSTRIA - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Vienna', country_ID FROM Country WHERE country_codename = 'AT';

INSERT INTO City (city_name, province_ID)
SELECT 'Vienna', province_ID FROM Province WHERE province_name = 'Vienna';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Innere Stadt' AS barangay_name UNION ALL
    SELECT 'Leopoldstadt' UNION ALL
    SELECT 'Landstraße' UNION ALL
    SELECT 'Wieden' UNION ALL
    SELECT 'Margareten'
) AS districts
CROSS JOIN City WHERE city_name = 'Vienna';

-- ============================================
-- BELGIUM - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Brussels', country_ID FROM Country WHERE country_codename = 'BE';

INSERT INTO City (city_name, province_ID)
SELECT 'Brussels', province_ID FROM Province WHERE province_name = 'Brussels';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'City Centre' AS barangay_name UNION ALL
    SELECT 'European Quarter' UNION ALL
    SELECT 'Ixelles' UNION ALL
    SELECT 'Saint-Gilles' UNION ALL
    SELECT 'Etterbeek'
) AS districts
CROSS JOIN City WHERE city_name = 'Brussels';

-- ============================================
-- SWEDEN - Counties, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Stockholm', country_ID FROM Country WHERE country_codename = 'SE';

INSERT INTO City (city_name, province_ID)
SELECT 'Stockholm', province_ID FROM Province WHERE province_name = 'Stockholm';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Gamla Stan' AS barangay_name UNION ALL
    SELECT 'Södermalm' UNION ALL
    SELECT 'Östermalm' UNION ALL
    SELECT 'Norrmalm' UNION ALL
    SELECT 'Djurgården'
) AS districts
CROSS JOIN City WHERE city_name = 'Stockholm';

-- ============================================
-- NORWAY - Counties, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Oslo', country_ID FROM Country WHERE country_codename = 'NO';

INSERT INTO City (city_name, province_ID)
SELECT 'Oslo', province_ID FROM Province WHERE province_name = 'Oslo';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Sentrum' AS barangay_name UNION ALL
    SELECT 'Grünerløkka' UNION ALL
    SELECT 'Frogner' UNION ALL
    SELECT 'Majorstuen' UNION ALL
    SELECT 'Aker Brygge'
) AS districts
CROSS JOIN City WHERE city_name = 'Oslo';

-- ============================================
-- DENMARK - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Capital Region', country_ID FROM Country WHERE country_codename = 'DK';

INSERT INTO City (city_name, province_ID)
SELECT 'Copenhagen', province_ID FROM Province WHERE province_name = 'Capital Region';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Indre By' AS barangay_name UNION ALL
    SELECT 'Vesterbro' UNION ALL
    SELECT 'Nørrebro' UNION ALL
    SELECT 'Østerbro' UNION ALL
    SELECT 'Christianshavn'
) AS districts
CROSS JOIN City WHERE city_name = 'Copenhagen';

-- ============================================
-- FINLAND - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Uusimaa', country_ID FROM Country WHERE country_codename = 'FI';

INSERT INTO City (city_name, province_ID)
SELECT 'Helsinki', province_ID FROM Province WHERE province_name = 'Uusimaa';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Keskusta' AS barangay_name UNION ALL
    SELECT 'Kallio' UNION ALL
    SELECT 'Kamppi' UNION ALL
    SELECT 'Punavuori' UNION ALL
    SELECT 'Töölö'
) AS districts
CROSS JOIN City WHERE city_name = 'Helsinki';

-- ============================================
-- CHILE - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Santiago Metropolitan', country_ID FROM Country WHERE country_codename = 'CL';

INSERT INTO City (city_name, province_ID)
SELECT 'Santiago', province_ID FROM Province WHERE province_name = 'Santiago Metropolitan';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Centro' AS barangay_name UNION ALL
    SELECT 'Providencia' UNION ALL
    SELECT 'Las Condes' UNION ALL
    SELECT 'Vitacura' UNION ALL
    SELECT 'Bellavista'
) AS districts
CROSS JOIN City WHERE city_name = 'Santiago';

-- ============================================
-- COLOMBIA - Departments, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Bogotá', country_ID FROM Country WHERE country_codename = 'CO';

INSERT INTO City (city_name, province_ID)
SELECT 'Bogotá', province_ID FROM Province WHERE province_name = 'Bogotá';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'La Candelaria' AS barangay_name UNION ALL
    SELECT 'Chapinero' UNION ALL
    SELECT 'Usaquén' UNION ALL
    SELECT 'Zona Rosa' UNION ALL
    SELECT 'Teusaquillo'
) AS districts
CROSS JOIN City WHERE city_name = 'Bogotá';

-- ============================================
-- PERU - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Lima', country_ID FROM Country WHERE country_codename = 'PE';

INSERT INTO City (city_name, province_ID)
SELECT 'Lima', province_ID FROM Province WHERE province_name = 'Lima';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Miraflores' AS barangay_name UNION ALL
    SELECT 'San Isidro' UNION ALL
    SELECT 'Barranco' UNION ALL
    SELECT 'Centro Histórico' UNION ALL
    SELECT 'Surco'
) AS districts
CROSS JOIN City WHERE city_name = 'Lima';

-- ============================================
-- TAIWAN - Counties, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Taipei', country_ID FROM Country WHERE country_codename = 'TW';

INSERT INTO City (city_name, province_ID)
SELECT 'Taipei', province_ID FROM Province WHERE province_name = 'Taipei';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Zhongzheng' AS barangay_name UNION ALL
    SELECT 'Xinyi' UNION ALL
    SELECT 'Da''an' UNION ALL
    SELECT 'Shilin' UNION ALL
    SELECT 'Beitou'
) AS districts
CROSS JOIN City WHERE city_name = 'Taipei';

-- ============================================
-- HONG KONG - Regions, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Hong Kong Island', country_ID FROM Country WHERE country_codename = 'HK';

INSERT INTO City (city_name, province_ID)
SELECT 'Hong Kong', province_ID FROM Province WHERE province_name = 'Hong Kong Island';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Central' AS barangay_name UNION ALL
    SELECT 'Wan Chai' UNION ALL
    SELECT 'Causeway Bay' UNION ALL
    SELECT 'Tsim Sha Tsui' UNION ALL
    SELECT 'Mong Kok'
) AS districts
CROSS JOIN City WHERE city_name = 'Hong Kong';

-- ============================================
-- ISRAEL - Districts, Cities, Neighborhoods
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Tel Aviv', country_ID FROM Country WHERE country_codename = 'IL';

INSERT INTO City (city_name, province_ID)
SELECT 'Tel Aviv', province_ID FROM Province WHERE province_name = 'Tel Aviv';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Old Jaffa' AS barangay_name UNION ALL
    SELECT 'Neve Tzedek' UNION ALL
    SELECT 'Florentin' UNION ALL
    SELECT 'Rothschild Boulevard' UNION ALL
    SELECT 'Ramat Aviv'
) AS districts
CROSS JOIN City WHERE city_name = 'Tel Aviv';

-- ============================================
-- SAUDI ARABIA - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Riyadh', country_ID FROM Country WHERE country_codename = 'SA';

INSERT INTO City (city_name, province_ID)
SELECT 'Riyadh', province_ID FROM Province WHERE province_name = 'Riyadh';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Al Olaya' AS barangay_name UNION ALL
    SELECT 'Al Malaz' UNION ALL
    SELECT 'Diplomatic Quarter' UNION ALL
    SELECT 'King Fahd District' UNION ALL
    SELECT 'Al Murabba'
) AS districts
CROSS JOIN City WHERE city_name = 'Riyadh';

-- ============================================
-- MOROCCO - Regions, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Casablanca-Settat', country_ID FROM Country WHERE country_codename = 'MA';

INSERT INTO City (city_name, province_ID)
SELECT 'Casablanca', province_ID FROM Province WHERE province_name = 'Casablanca-Settat';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Old Medina' AS barangay_name UNION ALL
    SELECT 'Maarif' UNION ALL
    SELECT 'Anfa' UNION ALL
    SELECT 'Ain Diab' UNION ALL
    SELECT 'Habous'
) AS districts
CROSS JOIN City WHERE city_name = 'Casablanca';

-- ============================================
-- KENYA - Counties, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Nairobi', country_ID FROM Country WHERE country_codename = 'KE';

INSERT INTO City (city_name, province_ID)
SELECT 'Nairobi', province_ID FROM Province WHERE province_name = 'Nairobi';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Central Business District' AS barangay_name UNION ALL
    SELECT 'Westlands' UNION ALL
    SELECT 'Karen' UNION ALL
    SELECT 'Kilimani' UNION ALL
    SELECT 'Parklands'
) AS districts
CROSS JOIN City WHERE city_name = 'Nairobi';

-- ============================================
-- NIGERIA - States, Cities, Districts
-- ============================================

INSERT INTO Province (province_name, country_ID) 
SELECT 'Lagos', country_ID FROM Country WHERE country_codename = 'NG';

INSERT INTO City (city_name, province_ID)
SELECT 'Lagos', province_ID FROM Province WHERE province_name = 'Lagos';

INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'Victoria Island' AS barangay_name UNION ALL
    SELECT 'Ikoyi' UNION ALL
    SELECT 'Lekki' UNION ALL
    SELECT 'Ikeja' UNION ALL
    SELECT 'Surulere'
) AS districts
CROSS JOIN City WHERE city_name = 'Lagos';

-- ============================================
-- END OF GLOBAL LOCATION DATA
-- ============================================

-- Afghanistan (country_ID = 1)
INSERT INTO Province (province_name, country_ID) VALUES
('Badakhshan', 1), ('Badghis', 1), ('Baghlan', 1), ('Balkh', 1),
('Bamyan', 1), ('Daykundi', 1), ('Farah', 1), ('Faryab', 1),
('Ghazni', 1), ('Ghor', 1), ('Helmand', 1), ('Herat', 1),
('Jowzjan', 1), ('Kabul', 1), ('Kandahar', 1), ('Kapisa', 1),
('Khost', 1), ('Kunar', 1), ('Kunduz', 1), ('Laghman', 1),
('Logar', 1), ('Nangarhar', 1), ('Nimroz', 1), ('Nuristan', 1),
('Paktia', 1), ('Paktika', 1), ('Panjshir', 1), ('Parwan', 1),
('Samangan', 1), ('Sar-e Pol', 1), ('Takhar', 1), ('Uruzgan', 1),
('Wardak', 1), ('Zabul', 1);

-- Armenia (country_ID = 11)
INSERT INTO Province (province_name, country_ID) VALUES
('Aragatsotn', 11), ('Ararat', 11), ('Armavir', 11), ('Gegharkunik', 11),
('Kotayk', 11), ('Lori', 11), ('Shirak', 11), ('Syunik', 11),
('Tavush', 11), ('Vayots Dzor', 11), ('Yerevan', 11);

-- Azerbaijan (country_ID = 15)
INSERT INTO Province (province_name, country_ID) VALUES
('Absheron', 15), ('Baku', 15), ('Ganja', 15), ('Lankaran', 15),
('Nakhchivan', 15), ('Qabala', 15), ('Quba', 15), ('Shaki', 15),
('Sumqayit', 15);

-- Bahrain (country_ID = 17)
INSERT INTO Province (province_name, country_ID) VALUES
('Capital Governorate', 17), ('Muharraq Governorate', 17),
('Northern Governorate', 17), ('Southern Governorate', 17);

-- Bangladesh (country_ID = 18)
INSERT INTO Province (province_name, country_ID) VALUES
('Barisal', 18), ('Chittagong', 18), ('Dhaka', 18),
('Khulna', 18), ('Mymensingh', 18), ('Rajshahi', 18),
('Rangpur', 18), ('Sylhet', 18);

-- Bhutan (country_ID = 25)
INSERT INTO Province (province_name, country_ID) VALUES
('Bumthang', 25), ('Chhukha', 25), ('Dagana', 25), ('Gasa', 25),
('Haa', 25), ('Lhuentse', 25), ('Mongar', 25), ('Paro', 25),
('Pemagatshel', 25), ('Punakha', 25), ('Samdrup Jongkhar', 25),
('Samtse', 25), ('Sarpang', 25), ('Thimphu', 25), ('Trashigang', 25),
('Trongsa', 25), ('Tsirang', 25), ('Wangdue Phodrang', 25),
('Zhemgang', 25);

-- Brunei (country_ID = 30)
INSERT INTO Province (province_name, country_ID) VALUES
('Belait', 30), ('Brunei-Muara', 30), ('Temburong', 30), ('Tutong', 30);

-- Cambodia (country_ID = 36)
INSERT INTO Province (province_name, country_ID) VALUES
('Banteay Meanchey', 36), ('Battambang', 36), ('Kampong Cham', 36),
('Kampong Chhnang', 36), ('Kampong Speu', 36), ('Kampong Thom', 36),
('Kampot', 36), ('Kandal', 36), ('Kep', 36), ('Koh Kong', 36),
('Kratie', 36), ('Mondulkiri', 36), ('Oddar Meanchey', 36),
('Pailin', 36), ('Phnom Penh', 36), ('Preah Vihear', 36),
('Prey Veng', 36), ('Pursat', 36), ('Ratanakiri', 36),
('Siem Reap', 36), ('Sihanoukville', 36), ('Stung Treng', 36),
('Svay Rieng', 36), ('Takeo', 36), ('Tbong Khmum', 36);

-- China (country_ID = 43)
INSERT INTO Province (province_name, country_ID) VALUES
('Anhui', 43), ('Beijing', 43), ('Chongqing', 43), ('Fujian', 43),
('Gansu', 43), ('Guangdong', 43), ('Guangxi', 43), ('Guizhou', 43),
('Hainan', 43), ('Hebei', 43), ('Heilongjiang', 43), ('Henan', 43),
('Hubei', 43), ('Hunan', 43), ('Inner Mongolia', 43), ('Jiangsu', 43),
('Jiangxi', 43), ('Jilin', 43), ('Liaoning', 43), ('Ningxia', 43),
('Qinghai', 43), ('Shaanxi', 43), ('Shandong', 43), ('Shanghai', 43),
('Shanxi', 43), ('Sichuan', 43), ('Tianjin', 43), ('Tibet', 43),
('Xinjiang', 43), ('Yunnan', 43), ('Zhejiang', 43);

-- India (country_ID = 99)
INSERT INTO Province (province_name, country_ID) VALUES
('Andhra Pradesh', 99), ('Arunachal Pradesh', 99), ('Assam', 99),
('Bihar', 99), ('Chhattisgarh', 99), ('Goa', 99), ('Gujarat', 99),
('Haryana', 99), ('Himachal Pradesh', 99), ('Jharkhand', 99),
('Karnataka', 99), ('Kerala', 99), ('Madhya Pradesh', 99),
('Maharashtra', 99), ('Manipur', 99), ('Meghalaya', 99),
('Mizoram', 99), ('Nagaland', 99), ('Odisha', 99),
('Punjab', 99), ('Rajasthan', 99), ('Sikkim', 99),
('Tamil Nadu', 99), ('Telangana', 99), ('Tripura', 99),
('Uttar Pradesh', 99), ('Uttarakhand', 99), ('West Bengal', 99),
('Andaman and Nicobar Islands', 99), ('Chandigarh', 99),
('Delhi', 99), ('Puducherry', 99);

-- Indonesia (country_ID = 100)
INSERT INTO Province (province_name, country_ID) VALUES
('Aceh', 100), ('Bali', 100), ('Banten', 100), ('Bengkulu', 100),
('Central Java', 100), ('Central Kalimantan', 100),
('Central Sulawesi', 100), ('East Java', 100), ('East Kalimantan', 100),
('East Nusa Tenggara', 100), ('Gorontalo', 100), ('Jakarta', 100),
('Jambi', 100), ('Lampung', 100), ('Maluku', 100),
('North Kalimantan', 100), ('North Maluku', 100),
('North Sulawesi', 100), ('North Sumatra', 100),
('Papua', 100), ('Riau', 100), ('Riau Islands', 100),
('Southeast Sulawesi', 100), ('South Kalimantan', 100),
('South Sulawesi', 100), ('South Sumatra', 100),
('West Java', 100), ('West Kalimantan', 100),
('West Nusa Tenggara', 100), ('West Papua', 100),
('West Sulawesi', 100), ('West Sumatra', 100), ('Yogyakarta', 100);


INSERT INTO Province (province_name, country_ID) VALUES
-- Afghanistan (country_ID = 1)
('Badakhshan', 1),
('Badghis', 1),
('Baghlan', 1),
('Balkh', 1),
('Bamyan', 1),
('Daykundi', 1),
('Farah', 1),
('Faryab', 1),
('Ghazni', 1),
('Ghor', 1),
('Helmand', 1),
('Herat', 1),
('Jowzjan', 1),
('Kabul', 1),
('Kandahar', 1),
('Kapisa', 1),
('Khost', 1),
('Kunar', 1),
('Kunduz', 1),
('Laghman', 1),
('Logar', 1),
('Nangarhar', 1),
('Nimroz', 1),
('Nuristan', 1),
('Paktia', 1),
('Paktika', 1),
('Panjshir', 1),
('Parwan', 1),
('Samangan', 1),
('Sar-e Pol', 1),
('Takhar', 1),
('Urozgan', 1),
('Wardak', 1),
('Zabul', 1),

-- Armenia (country_ID = 11)
('Aragatsotn', 11),
('Ararat', 11),
('Armavir', 11),
('Gegharkunik', 11),
('Kotayk', 11),
('Lori', 11),
('Shirak', 11),
('Syunik', 11),
('Tavush', 11),
('Vayots Dzor', 11),
('Yerevan', 11),

-- Azerbaijan (country_ID = 15)
('Absheron', 15),
('Ganja-Gazakh', 15),
('Guba-Khachmaz', 15),
('Lankaran', 15),
('Nakhchivan', 15),
('Shaki-Zaqatala', 15),
('Shirvan', 15),
('Upper Karabakh', 15),
('Upper Shirvan', 15),

-- Bahrain (country_ID = 17)
('Capital Governorate', 17),
('Central Governorate', 17),
('Muharraq Governorate', 17),
('Northern Governorate', 17),
('Southern Governorate', 17),

-- Bangladesh (country_ID = 18)
('Barisal', 18),
('Chittagong', 18),
('Dhaka', 18),
('Khulna', 18),
('Mymensingh', 18),
('Rajshahi', 18),
('Rangpur', 18),
('Sylhet', 18),

-- Bhutan (country_ID = 25)
('Bumthang', 25),
('Chhukha', 25),
('Dagana', 25),
('Gasa', 25),
('Haa', 25),
('Lhuntse', 25),
('Mongar', 25),
('Paro', 25),
('Pemagatshel', 25),
('Punakha', 25),
('Samdrup Jongkhar', 25),
('Samtse', 25),
('Sarpang', 25),
('Thimphu', 25),
('Trashigang', 25),
('Trashi Yangtse', 25),
('Trongsa', 25),
('Tsirang', 25),
('Wangdue Phodrang', 25),
('Zhemgang', 25);

INSERT INTO Province (province_name, country_ID) VALUES
-- Brunei (country_ID = 31)
('Brunei-Muara', 31),
('Belait', 31),
('Tutong', 31),
('Temburong', 31),

-- Cambodia (country_ID = 36)
('Banteay Meanchey', 36),
('Battambang', 36),
('Kampong Cham', 36),
('Kampong Chhnang', 36),
('Kampong Speu', 36),
('Kampong Thom', 36),
('Kampot', 36),
('Kandal', 36),
('Kep', 36),
('Koh Kong', 36),
('Kratie', 36),
('Mondulkiri', 36),
('Oddar Meanchey', 36),
('Pailin', 36),
('Phnom Penh', 36),
('Preah Sihanouk', 36),
('Preah Vihear', 36),
('Prey Veng', 36),
('Pursat', 36),
('Ratanakiri', 36),
('Siem Reap', 36),
('Stung Treng', 36),
('Svay Rieng', 36),
('Takeo', 36),
('Tbong Khmum', 36),

-- China (country_ID = 42)
('Anhui', 42),
('Beijing', 42),
('Chongqing', 42),
('Fujian', 42),
('Gansu', 42),
('Guangdong', 42),
('Guangxi', 42),
('Guizhou', 42),
('Hainan', 42),
('Hebei', 42),
('Heilongjiang', 42),
('Henan', 42),
('Hubei', 42),
('Hunan', 42),
('Inner Mongolia', 42),
('Jiangsu', 42),
('Jiangxi', 42),
('Jilin', 42),
('Liaoning', 42),
('Ningxia', 42),
('Qinghai', 42),
('Shaanxi', 42),
('Shandong', 42),
('Shanghai', 42),
('Shanxi', 42),
('Sichuan', 42),
('Tianjin', 42),
('Tibet', 42),
('Xinjiang', 42),
('Yunnan', 42),
('Zhejiang', 42),

-- India (country_ID = 101)
('Andhra Pradesh', 101),
('Arunachal Pradesh', 101),
('Assam', 101),
('Bihar', 101),
('Chhattisgarh', 101),
('Goa', 101),
('Gujarat', 101),
('Haryana', 101),
('Himachal Pradesh', 101),
('Jharkhand', 101),
('Karnataka', 101),
('Kerala', 101),
('Madhya Pradesh', 101),
('Maharashtra', 101),
('Manipur', 101),
('Meghalaya', 101),
('Mizoram', 101),
('Nagaland', 101),
('Odisha', 101),
('Punjab', 101),
('Rajasthan', 101),
('Sikkim', 101),
('Tamil Nadu', 101),
('Telangana', 101),
('Tripura', 101),
('Uttar Pradesh', 101),
('Uttarakhand', 101),
('West Bengal', 101),
('Andaman and Nicobar Islands', 101),
('Chandigarh', 101),
('Dadra and Nagar Haveli and Daman and Diu', 101),
('Delhi', 101),
('Jammu and Kashmir', 101),
('Ladakh', 101),
('Lakshadweep', 101),
('Puducherry', 101),

-- Indonesia (country_ID = 102)
('Aceh', 102),
('Bali', 102),
('Banten', 102),
('Bengkulu', 102),
('Central Java', 102),
('Central Kalimantan', 102),
('Central Sulawesi', 102),
('East Java', 102),
('East Kalimantan', 102),
('East Nusa Tenggara', 102),
('Gorontalo', 102),
('Jakarta', 102),
('Jambi', 102),
('Lampung', 102),
('Maluku', 102),
('North Kalimantan', 102),
('North Maluku', 102),
('North Sulawesi', 102),
('North Sumatra', 102),
('Papua', 102),
('Riau', 102),
('Riau Islands', 102),
('Southeast Sulawesi', 102),
('South Kalimantan', 102),
('South Sulawesi', 102),
('South Sumatra', 102),
('West Java', 102),
('West Kalimantan', 102),
('West Nusa Tenggara', 102),
('West Papua', 102),
('West Sulawesi', 102),
('West Sumatra', 102),
('Yogyakarta', 102),

-- Iran (country_ID = 103)
('Alborz', 103),
('Ardabil', 103),
('Bushehr', 103),
('Chaharmahal and Bakhtiari', 103),
('East Azerbaijan', 103),
('Esfahan', 103),
('Fars', 103),
('Gilan', 103),
('Golestan', 103),
('Hamadan', 103),
('Hormozgan', 103),
('Ilam', 103),
('Kerman', 103),
('Kermanshah', 103),
('Khuzestan', 103),
('Kohgiluyeh and Boyer-Ahmad', 103),
('Kurdistan', 103),
('Lorestan', 103),
('Markazi', 103),
('Mazandaran', 103),
('North Khorasan', 103),
('Qazvin', 103),
('Qom', 103),
('Razavi Khorasan', 103),
('Semnan', 103),
('Sistan and Baluchestan', 103),
('South Khorasan', 103),
('Tehran', 103),
('West Azerbaijan', 103),
('Yazd', 103),
('Zanjan', 103);

INSERT INTO Province (province_name, country_ID) VALUES
-- Iraq (country_ID = 104)
('Al Anbar', 104),
('Al Muthanna', 104),
('Al Qadisiyyah', 104),
('Babil', 104),
('Baghdad', 104),
('Basra', 104),
('Dhi Qar', 104),
('Diyala', 104),
('Dohuk', 104),
('Erbil', 104),
('Karbala', 104),
('Kirkuk', 104),
('Maysan', 104),
('Najaf', 104),
('Nineveh', 104),
('Saladin', 104),
('Sulaymaniyah', 104),
('Wasit', 104),

-- Israel (country_ID = 107)
('Central District', 107),
('Haifa District', 107),
('Jerusalem District', 107),
('Northern District', 107),
('Southern District', 107),
('Tel Aviv District', 107),

-- Japan (country_ID = 110)
('Hokkaido', 110),
('Aomori', 110),
('Iwate', 110),
('Miyagi', 110),
('Akita', 110),
('Yamagata', 110),
('Fukushima', 110),
('Ibaraki', 110),
('Tochigi', 110),
('Gunma', 110),
('Saitama', 110),
('Chiba', 110),
('Tokyo', 110),
('Kanagawa', 110),
('Niigata', 110),
('Toyama', 110),
('Ishikawa', 110),
('Fukui', 110),
('Yamanashi', 110),
('Nagano', 110),
('Gifu', 110),
('Shizuoka', 110),
('Aichi', 110),
('Mie', 110),
('Shiga', 110),
('Kyoto', 110),
('Osaka', 110),
('Hyogo', 110),
('Nara', 110),
('Wakayama', 110),
('Tottori', 110),
('Shimane', 110),
('Okayama', 110),
('Hiroshima', 110),
('Yamaguchi', 110),
('Tokushima', 110),
('Kagawa', 110),
('Ehime', 110),
('Kochi', 110),
('Fukuoka', 110),
('Saga', 110),
('Nagasaki', 110),
('Kumamoto', 110),
('Oita', 110),
('Miyazaki', 110),
('Kagoshima', 110),
('Okinawa', 110),

-- Jordan (country_ID = 111)
('Ajloun', 111),
('Amman', 111),
('Aqaba', 111),
('Balqa', 111),
('Irbid', 111),
('Jerash', 111),
('Karak', 111),
('Maan', 111),
('Madaba', 111),
('Mafraq', 111),
('Tafilah', 111),
('Zarqa', 111),

-- Kazakhstan (country_ID = 112)
('Akmola', 112),
('Aktobe', 112),
('Almaty', 112),
('Atyrau', 112),
('East Kazakhstan', 112),
('Jambyl', 112),
('Karaganda', 112),
('Kostanay', 112),
('Kyzylorda', 112),
('Mangystau', 112),
('North Kazakhstan', 112),
('Pavlodar', 112),
('Turkistan', 112),
('West Kazakhstan', 112),
('Nur-Sultan', 112),

-- Kuwait (country_ID = 117)
('Al Asimah', 117),
('Hawalli', 117),
('Farwaniya', 117),
('Mubarak Al-Kabeer', 117),
('Ahmadi', 117),
('Jahra', 117),

-- Lebanon (country_ID = 122)
('Akkar', 122),
('Baalbek-Hermel', 122),
('Beirut', 122),
('Bekaa', 122),
('Mount Lebanon', 122),
('Nabatieh', 122),
('North Lebanon', 122),
('South Lebanon', 122),

-- Malaysia (country_ID = 124)
('Johor', 124),
('Kedah', 124),
('Kelantan', 124),
('Melaka', 124),
('Negeri Sembilan', 124),
('Pahang', 124),
('Perak', 124),
('Perlis', 124),
('Pulau Pinang', 124),
('Sabah', 124),
('Sarawak', 124),
('Selangor', 124),
('Terengganu', 124),
('Kuala Lumpur', 124),
('Labuan', 124),
('Putrajaya', 124),

-- Mongolia (country_ID = 150)
('Arkhangai', 150),
('Bayan-Ölgii', 150),
('Bayankhongor', 150),
('Bulgan', 150),
('Darkhan-Uul', 150),
('Dornod', 150),
('Dornogovi', 150),
('Dundgovi', 150),
('Govi-Altai', 150),
('Govisümber', 150),
('Khentii', 150),
('Khovd', 150),
('Khovsgol', 150),
('Ömnögovi', 150),
('Orkhon', 150),
('Selenge', 150),
('Sükhbaatar', 150),
('Töv', 150),
('Uvs', 150),
('Zavkhan', 150),
('Ulaanbaatar', 150),

-- Nepal (country_ID = 174)
('Koshi', 174),
('Madhesh', 174),
('Bagmati', 174),
('Gandaki', 174),
('Lumbini', 174),
('Karnali', 174),
('Sudurpashchim', 174),

-- Philippines (country_ID = 157)
('Ilocos Region', 157),
('Cagayan Valley', 157),
('Central Luzon', 157),
('Calabarzon', 157),
('Mimaropa', 157),
('Bicol Region', 157),
('Western Visayas', 157),
('Central Visayas', 157),
('Eastern Visayas', 157),
('Zamboanga Peninsula', 157),
('Northern Mindanao', 157),
('Davao Region', 157),
('SOCCSKSARGEN', 157),
('Caraga', 157),
('Bangsamoro', 157),
('Cordillera Administrative Region', 157),
('National Capital Region', 157);

-- ============================================
-- EUROPE
-- ============================================

INSERT INTO Province (province_name, country_ID) VALUES
-- Albania (country_ID = 2)
('Berat', 2),
('Dibër', 2),
('Durrës', 2),
('Elbasan', 2),
('Fier', 2),
('Gjirokastër', 2),
('Korçë', 2),
('Kukës', 2),
('Lezhë', 2),
('Shkodër', 2),
('Tirana', 2),
('Vlorë', 2),

-- Andorra (country_ID = 5)
('Andorra la Vella', 5),
('Canillo', 5),
('Encamp', 5),
('Escaldes-Engordany', 5),
('La Massana', 5),
('Ordino', 5),
('Sant Julià de Lòria', 5),

-- Austria (country_ID = 13)
('Burgenland', 13),
('Carinthia', 13),
('Lower Austria', 13),
('Upper Austria', 13),
('Salzburg', 13),
('Styria', 13),
('Tyrol', 13),
('Vorarlberg', 13),
('Vienna', 13),

-- Belgium (country_ID = 20)
('Antwerp', 20),
('Brussels-Capital Region', 20),
('East Flanders', 20),
('Flemish Brabant', 20),
('Hainaut', 20),
('Liège', 20),
('Limburg', 20),
('Luxembourg', 20),
('Namur', 20),
('Walloon Brabant', 20),
('West Flanders', 20),

-- Bosnia and Herzegovina (country_ID = 23)
('Brčko District', 23),
('Federation of Bosnia and Herzegovina', 23),
('Republika Srpska', 23),

-- Bulgaria (country_ID = 29)
('Blagoevgrad', 29),
('Burgas', 29),
('Varna', 29),
('Veliko Tarnovo', 29),
('Vidin', 29),
('Vratsa', 29),
('Gabrovo', 29),
('Kardzhali', 29),
('Kyustendil', 29),
('Lovech', 29),
('Montana', 29),
('Pazardzhik', 29),
('Pernik', 29),
('Pleven', 29),
('Plovdiv', 29),
('Razgrad', 29),
('Ruse', 29),
('Silistra', 29),
('Sliven', 29),
('Smolyan', 29),
('Sofia', 29),
('Sofia Province', 29),
('Stara Zagora', 29),
('Targovishte', 29),
('Haskovo', 29),
('Shumen', 29),
('Yambol', 29),

-- Croatia (country_ID = 41)
('Bjelovar-Bilogora', 41),
('Brodsko-Posavska', 41),
('Dubrovnik-Neretva', 41),
('Istria', 41),
('Karlovac', 41),
('Koprivnica-Križevci', 41),
('Krapina-Zagorje', 41),
('Lika-Senj', 41),
('Međimurje', 41),
('Osijek-Baranja', 41),
('Požega-Slavonia', 41),
('Primorje-Gorski Kotar', 41),
('Šibenik-Knin', 41),
('Sisak-Moslavina', 41),
('Split-Dalmatia', 41),
('Varaždin', 41),
('Virovitica-Podravina', 41),
('Vukovar-Srijem', 41),
('Zadar', 41),
('Zagreb', 41),

-- Cyprus (country_ID = 45)
('Famagusta', 45),
('Kyrenia', 45),
('Larnaca', 45),
('Limassol', 45),
('Nicosia', 45),
('Paphos', 45),

-- Czech Republic (country_ID = 46)
('Prague', 46),
('Central Bohemian', 46),
('South Bohemian', 46),
('Plzeň', 46),
('Karlovy Vary', 46),
('Ústí nad Labem', 46),
('Liberec', 46),
('Hradec Králové', 46),
('Pardubice', 46),
('Vysočina', 46),
('South Moravian', 46),
('Olomouc', 46),
('Zlín', 46),
('Moravian-Silesian', 46),

-- Denmark (country_ID = 47)
('Capital Region', 47),
('Central Denmark Region', 47),
('North Denmark Region', 47),
('Region of Southern Denmark', 47),
('Region Zealand', 47),

-- Estonia (country_ID = 53)
('Harju', 53),
('Hiiu', 53),
('Ida-Viru', 53),
('Jõgeva', 53),
('Järva', 53),
('Lääne', 53),
('Lääne-Viru', 53),
('Pärnu', 53),
('Põlva', 53),
('Rapla', 53),
('Saare', 53),
('Tartu', 53),
('Valga', 53),
('Viljandi', 53),
('Võru', 53);

INSERT INTO Province (province_name, country_ID) VALUES
-- Finland (country_ID = 55)
('Åland Islands', 55),
('Central Finland', 55),
('Kainuu', 55),
('Lapland', 55),
('North Karelia', 55),
('North Ostrobothnia', 55),
('Northern Savonia', 55),
('Ostrobothnia', 55),
('Pirkanmaa', 55),
('Satakunta', 55),
('South Karelia', 55),
('Southern Ostrobothnia', 55),
('Southern Savonia', 55),
('Uusimaa', 55),
('Varsinais-Suomi', 55),

-- France (country_ID = 56)
('Auvergne-Rhône-Alpes', 56),
('Bourgogne-Franche-Comté', 56),
('Brittany', 56),
('Centre-Val de Loire', 56),
('Corsica', 56),
('Grand Est', 56),
('Hauts-de-France', 56),
('Île-de-France', 56),
('Normandy', 56),
('Nouvelle-Aquitaine', 56),
('Occitanie', 56),
('Pays de la Loire', 56),
('Provence-Alpes-Côte dAzur', 56),

-- Germany (country_ID = 61)
('Baden-Württemberg', 61),
('Bavaria', 61),
('Berlin', 61),
('Brandenburg', 61),
('Bremen', 61),
('Hamburg', 61),
('Hesse', 61),
('Lower Saxony', 61),
('Mecklenburg-Vorpommern', 61),
('North Rhine-Westphalia', 61),
('Rhineland-Palatinate', 61),
('Saarland', 61),
('Saxony', 61),
('Saxony-Anhalt', 61),
('Schleswig-Holstein', 61),
('Thuringia', 61),

-- Greece (country_ID = 69)
('Attica', 69),
('Central Greece', 69),
('Central Macedonia', 69),
('Crete', 69),
('East Macedonia and Thrace', 69),
('Epirus', 69),
('Ionian Islands', 69),
('North Aegean', 69),
('Peloponnese', 69),
('South Aegean', 69),
('Thessaly', 69),
('Western Greece', 69),
('Western Macedonia', 69),

-- Hungary (country_ID = 70)
('Bács-Kiskun', 70),
('Baranya', 70),
('Békés', 70),
('Borsod-Abaúj-Zemplén', 70),
('Csongrád-Csanád', 70),
('Fejér', 70),
('Győr-Moson-Sopron', 70),
('Hajdú-Bihar', 70),
('Heves', 70),
('Jász-Nagykun-Szolnok', 70),
('Komárom-Esztergom', 70),
('Nógrád', 70),
('Pest', 70),
('Somogy', 70),
('Szabolcs-Szatmár-Bereg', 70),
('Tolna', 70),
('Vas', 70),
('Veszprém', 70),
('Zala', 70),
('Budapest', 70),

-- Iceland (country_ID = 71)
('Capital Region', 71),
('Southern Peninsula', 71),
('Western Region', 71),
('Westfjords', 71),
('Northwest', 71),
('Northeast', 71),
('East', 71),
('South', 71),

-- Ireland (country_ID = 72)
('Carlow', 72),
('Cavan', 72),
('Clare', 72),
('Cork', 72),
('Donegal', 72),
('Dublin', 72),
('Galway', 72),
('Kerry', 72),
('Kildare', 72),
('Kilkenny', 72),
('Laois', 72),
('Leitrim', 72),
('Limerick', 72),
('Longford', 72),
('Louth', 72),
('Mayo', 72),
('Meath', 72),
('Monaghan', 72),
('Offaly', 72),
('Roscommon', 72),
('Sligo', 72),
('Tipperary', 72),
('Waterford', 72),
('Westmeath', 72),
('Wexford', 72),
('Wicklow', 72),

-- Italy (country_ID = 77)
('Abruzzo', 77),
('Aosta Valley', 77),
('Apulia', 77),
('Basilicata', 77),
('Calabria', 77),
('Campania', 77),
('Emilia-Romagna', 77),
('Friuli Venezia Giulia', 77),
('Lazio', 77),
('Liguria', 77),
('Lombardy', 77),
('Marche', 77),
('Molise', 77),
('Piedmont', 77),
('Sardinia', 77),
('Sicily', 77),
('Trentino-Alto Adige/Südtirol', 77),
('Tuscany', 77),
('Umbria', 77),
('Veneto', 77),

-- Latvia (country_ID = 91)
('Kurzeme', 91),
('Latgale', 91),
('Riga', 91),
('Vidzeme', 91),
('Zemgale', 91),

-- Lithuania (country_ID = 92)
('Alytus', 92),
('Kaunas', 92),
('Klaipėda', 92),
('Marijampolė', 92),
('Panevėžys', 92),
('Šiauliai', 92),
('Tauragė', 92),
('Telšiai', 92),
('Utena', 92),
('Vilnius', 92),

-- Luxembourg (country_ID = 93)
('Diekirch', 93),
('Grevenmacher', 93),
('Luxembourg', 93),

-- Malta (country_ID = 94)
('Gozo', 94),
('Malta', 94),

-- Netherlands (country_ID = 95)
('Drenthe', 95),
('Flevoland', 95),
('Friesland', 95),
('Gelderland', 95),
('Groningen', 95),
('Limburg', 95),
('North Brabant', 95),
('North Holland', 95),
('Overijssel', 95),
('South Holland', 95),
('Utrecht', 95),
('Zeeland', 95),
('Flevoland', 95),

-- Norway (country_ID = 96)
('Agder', 96),
('Innlandet', 96),
('Møre og Romsdal', 96),
('Nordland', 96),
('Oslo', 96),
('Rogaland', 96),
('Troms og Finnmark', 96),
('Trøndelag', 96),
('Vestfold og Telemark', 96),
('Vestland', 96),
('Viken', 96),

-- Poland (country_ID = 97)
('Greater Poland', 97),
('Kuyavian-Pomeranian', 97),
('Lesser Poland', 97),
('Łódź', 97),
('Lower Silesian', 97),
('Lublin', 97),
('Lubusz', 97),
('Masovian', 97),
('Opole', 97),
('Podlaskie', 97),
('Pomeranian', 97),
('Silesian', 97),
('Subcarpathian', 97),
('Świętokrzyskie', 97),
('Warmian-Masurian', 97),
('West Pomeranian', 97),

-- Portugal (country_ID = 98)
('Aveiro', 98),
('Beja', 98),
('Braga', 98),
('Bragança', 98),
('Castelo Branco', 98),
('Coimbra', 98),
('Évora', 98),
('Faro', 98),
('Guarda', 98),
('Leiria', 98),
('Lisbon', 98),
('Portalegre', 98),
('Porto', 98),
('Santarém', 98),
('Setúbal', 98),
('Viana do Castelo', 98),
('Vila Real', 98),
('Viseu', 98);

INSERT INTO Province (province_name, country_ID) VALUES
-- Romania (country_ID = 99)
('Alba', 99),
('Arad', 99),
('Arges', 99),
('Bacău', 99),
('Bihor', 99),
('Bistrița-Năsăud', 99),
('Botoșani', 99),
('Brașov', 99),
('Brăila', 99),
('Buzău', 99),
('Călărași', 99),
('Caraș-Severin', 99),
('Cluj', 99),
('Constanța', 99),
('Covasna', 99),
('Dâmbovița', 99),
('Dolj', 99),
('Galați', 99),
('Giurgiu', 99),
('Gorj', 99),
('Harghita', 99),
('Hunedoara', 99),
('Ialomița', 99),
('Iași', 99),
('Ilfov', 99),
('Maramureș', 99),
('Mehedinți', 99),
('Mureș', 99),
('Neamț', 99),
('Olt', 99),
('Prahova', 99),
('Sălaj', 99),
('Satu Mare', 99),
('Sibiu', 99),
('Suceava', 99),
('Teleorman', 99),
('Timiș', 99),
('Tulcea', 99),
('Vaslui', 99),
('Vâlcea', 99),
('Vrancea', 99),
('Bucharest', 99),

-- Russia (country_ID = 100)
('Adygea', 100),
('Altai Republic', 100),
('Altai Krai', 100),
('Amur Oblast', 100),
('Arkhangelsk Oblast', 100),
('Astrakhan Oblast', 100),
('Bashkortostan', 100),
('Belgorod Oblast', 100),
('Bryansk Oblast', 100),
('Buryatia', 100),
('Chechen Republic', 100),
('Chelyabinsk Oblast', 100),
('Chukotka Autonomous Okrug', 100),
('Chuvashia', 100),
('Dagestan', 100),
('Ingushetia', 100),
('Irkutsk Oblast', 100),
('Ivanovo Oblast', 100),
('Jewish Autonomous Oblast', 100),
('Kabardino-Balkaria', 100),
('Kaliningrad Oblast', 100),
('Kalmykia', 100),
('Kaluga Oblast', 100),
('Kamchatka Krai', 100),
('Karachay-Cherkessia', 100),
('Karelia', 100),
('Kemerovo Oblast', 100),
('Khabarovsk Krai', 100),
('Khakassia', 100),
('Khanty-Mansi Autonomous Okrug', 100),
('Kirov Oblast', 100),
('Komi Republic', 100),
('Kostroma Oblast', 100),
('Krasnodar Krai', 100),
('Krasnoyarsk Krai', 100),
('Kurgan Oblast', 100),
('Kursk Oblast', 100),
('Leningrad Oblast', 100),
('Lipetsk Oblast', 100),
('Magadan Oblast', 100),
('Mari El Republic', 100),
('Mordovia', 100),
('Moscow', 100),
('Moscow Oblast', 100),
('Murmansk Oblast', 100),
('Nenets Autonomous Okrug', 100),
('Nizhny Novgorod Oblast', 100),
('Novgorod Oblast', 100),
('Novosibirsk Oblast', 100),
('Omsk Oblast', 100),
('Orel Oblast', 100),
('Orenburg Oblast', 100),
('Penza Oblast', 100),
('Perm Krai', 100),
('Primorsky Krai', 100),
('Pskov Oblast', 100),
('Rostov Oblast', 100),
('Ryazan Oblast', 100),
('Sakha Republic (Yakutia)', 100),
('Sakhalin Oblast', 100),
('Samara Oblast', 100),
('Saratov Oblast', 100),
('Smolensk Oblast', 100),
('St. Petersburg', 100),
('Stavropol Krai', 100),
('Sverdlovsk Oblast', 100),
('Tambov Oblast', 100),
('Tatarstan', 100),
('Tomsk Oblast', 100),
('Tula Oblast', 100),
('Tver Oblast', 100),
('Tyumen Oblast', 100),
('Udmurtia', 100),
('Ulyanovsk Oblast', 100),
('Vladimir Oblast', 100),
('Volgograd Oblast', 100),
('Vologda Oblast', 100),
('Voronezh Oblast', 100),
('Yamalo-Nenets Autonomous Okrug', 100),
('Yaroslavl Oblast', 100),
('Zabaykalsky Krai', 100),

-- Slovakia (country_ID = 101)
('Bratislava', 101),
('Trnava', 101),
('Trenčín', 101),
('Nitra', 101),
('Žilina', 101),
('Banská Bystrica', 101),
('Prešov', 101),
('Košice', 101),

-- Slovenia (country_ID = 102)
('Ajdovščina', 102),
('Bled', 102),
('Celje', 102),
('Domžale', 102),
('Gorizia', 102),
('Koper', 102),
('Kranj', 102),
('Krško', 102),
('Ljubljana', 102),
('Maribor', 102),
('Murska Sobota', 102),
('Nova Gorica', 102),
('Nova Mesto', 102),
('Ptuj', 102),
('Triglav', 102),
('Velenje', 102),
('Videm', 102),

-- Spain (country_ID = 103)
('Andalusia', 103),
('Aragon', 103),
('Asturias', 103),
('Balearic Islands', 103),
('Basque Country', 103),
('Canary Islands', 103),
('Cantabria', 103),
('Castile and León', 103),
('Castile-La Mancha', 103),
('Catalonia', 103),
('Extremadura', 103),
('Galicia', 103),
('La Rioja', 103),
('Madrid', 103),
('Murcia', 103),
('Navarre', 103),
('Valencian Community', 103),

-- Sweden (country_ID = 104)
('Blekinge', 104),
('Dalarna', 104),
('Gotland', 104),
('Gävleborg', 104),
('Halland', 104),
('Jämtland', 104),
('Jönköping', 104),
('Kalmar', 104),
('Kronoberg', 104),
('Norrbotten', 104),
('Skåne', 104),
('Stockholm', 104),
('Södermanland', 104),
('Uppsala', 104),
('Värmland', 104),
('Västerbotten', 104),
('Västernorrland', 104),
('Västmanland', 104),
('Västra Götaland', 104),
('Örebro', 104),
('Östergötland', 104),

-- Switzerland (country_ID = 105)
('Aargau', 105),
('Appenzell Ausserrhoden', 105),
('Appenzell Innerrhoden', 105),
('Basel-Landschaft', 105),
('Basel-Stadt', 105),
('Bern', 105),
('Fribourg', 105),
('Geneva', 105),
('Glarus', 105),
('Graubünden', 105),
('Jura', 105),
('Lucerne', 105),
('Neuchâtel', 105),
('Nidwalden', 105),
('Obwalden', 105),
('Schaffhausen', 105),
('Schwyz', 105),
('Solothurn', 105),
('St. Gallen', 105),
('Thurgau', 105),
('Ticino', 105),
('Uri', 105),
('Valais', 105),
('Vaud', 105),
('Zug', 105),
('Zürich', 105),

-- United Kingdom (country_ID = 106)
('England', 106),
('Scotland', 106),
('Wales', 106),
('Northern Ireland', 106);


-- =======================================
-- America
-- =======================================

INSERT INTO Province (province_name, country_ID) VALUES
-- United States (country_ID = 107)
('Alabama', 107),
('Alaska', 107),
('Arizona', 107),
('Arkansas', 107),
('California', 107),
('Colorado', 107),
('Connecticut', 107),
('Delaware', 107),
('Florida', 107),
('Georgia', 107),
('Hawaii', 107),
('Idaho', 107),
('Illinois', 107),
('Indiana', 107),
('Iowa', 107),
('Kansas', 107),
('Kentucky', 107),
('Louisiana', 107),
('Maine', 107),
('Maryland', 107),
('Massachusetts', 107),
('Michigan', 107),
('Minnesota', 107),
('Mississippi', 107),
('Missouri', 107),
('Montana', 107),
('Nebraska', 107),
('Nevada', 107),
('New Hampshire', 107),
('New Jersey', 107),
('New Mexico', 107),
('New York', 107),
('North Carolina', 107),
('North Dakota', 107),
('Ohio', 107),
('Oklahoma', 107),
('Oregon', 107),
('Pennsylvania', 107),
('Rhode Island', 107),
('South Carolina', 107),
('South Dakota', 107),
('Tennessee', 107),
('Texas', 107),
('Utah', 107),
('Vermont', 107),
('Virginia', 107),
('Washington', 107),
('West Virginia', 107),
('Wisconsin', 107),
('Wyoming', 107),
('District of Columbia', 107);

-- Canada (country_ID = 108)
INSERT INTO Province (province_name, country_ID) VALUES
('Alberta', 108),
('British Columbia', 108),
('Manitoba', 108),
('New Brunswick', 108),
('Newfoundland and Labrador', 108),
('Nova Scotia', 108),
('Ontario', 108),
('Prince Edward Island', 108),
('Quebec', 108),
('Saskatchewan', 108),
('Northwest Territories', 108),
('Nunavut', 108),
('Yukon', 108);

-- Mexico (country_ID = 109)
INSERT INTO Province (province_name, country_ID) VALUES
('Aguascalientes', 109),
('Baja California', 109),
('Baja California Sur', 109),
('Campeche', 109),
('Chiapas', 109),
('Chihuahua', 109),
('Coahuila', 109),
('Colima', 109),
('Durango', 109),
('Guanajuato', 109),
('Guerrero', 109),
('Hidalgo', 109),
('Jalisco', 109),
('Mexico City', 109),
('Michoacán', 109),
('Morelos', 109),
('Nayarit', 109),
('Nuevo León', 109),
('Oaxaca', 109),
('Puebla', 109),
('Querétaro', 109),
('Quintana Roo', 109),
('San Luis Potosí', 109),
('Sinaloa', 109),
('Sonora', 109),
('Tabasco', 109),
('Tamaulipas', 109),
('Tlaxcala', 109),
('Veracruz', 109),
('Yucatán', 109),
('Zacatecas', 109);

-- Central America & Caribbean
-- Guatemala (country_ID = 110)
INSERT INTO Province (province_name, country_ID) VALUES
('Alta Verapaz', 110),
('Baja Verapaz', 110),
('Chimaltenango', 110),
('Chiquimula', 110),
('El Progreso', 110),
('Escuintla', 110),
('Guatemala', 110),
('Huehuetenango', 110),
('Izabal', 110),
('Jalapa', 110),
('Jutiapa', 110),
('Petén', 110),
('Quetzaltenango', 110),
('Quiché', 110),
('Retalhuleu', 110),
('Sacatepéquez', 110),
('San Marcos', 110),
('Santa Rosa', 110),
('Sololá', 110),
('Suchitepéquez', 110),
('Totonicapán', 110),
('Zacapa', 110);

-- Belize (country_ID = 111)
INSERT INTO Province (province_name, country_ID) VALUES
('Belize', 111),
('Cayo', 111),
('Corozal', 111),
('Orange Walk', 111),
('Stann Creek', 111),
('Toledo', 111);

-- Honduras (country_ID = 112)
INSERT INTO Province (province_name, country_ID) VALUES
('Atlántida', 112),
('Choluteca', 112),
('Colón', 112),
('Comayagua', 112),
('Copán', 112),
('Cortés', 112),
('El Paraíso', 112),
('Francisco Morazán', 112),
('Gracias a Dios', 112),
('Intibucá', 112),
('Islas de la Bahía', 112),
('La Paz', 112),
('Lempira', 112),
('Ocotepeque', 112),
('Olancho', 112),
('Santa Bárbara', 112),
('Valle', 112),
('Yoro', 112);

-- El Salvador (country_ID = 113)
INSERT INTO Province (province_name, country_ID) VALUES
('Ahuachapán', 113),
('Cabañas', 113),
('Chalatenango', 113),
('Cuscatlán', 113),
('La Libertad', 113),
('La Paz', 113),
('La Unión', 113),
('Morazán', 113),
('San Miguel', 113),
('San Salvador', 113),
('San Vicente', 113),
('Santa Ana', 113),
('Sonsonate', 113),
('Usulután', 113);

-- Nicaragua (country_ID = 114)
INSERT INTO Province (province_name, country_ID) VALUES
('Boaco', 114),
('Carazo', 114),
('Chinandega', 114),
('Chontales', 114),
('Estelí', 114),
('Granada', 114),
('Jinotega', 114),
('León', 114),
('Madriz', 114),
('Managua', 114),
('Masaya', 114),
('Matagalpa', 114),
('Nueva Segovia', 114),
('Río San Juan', 114),
('Rivas', 114),
('North Caribbean Coast Autonomous Region', 114),
('South Caribbean Coast Autonomous Region', 114);

-- Costa Rica (country_ID = 115)
INSERT INTO Province (province_name, country_ID) VALUES
('Alajuela', 115),
('Cartago', 115),
('Guanacaste', 115),
('Heredia', 115),
('Limón', 115),
('Puntarenas', 115),
('San José', 115);

-- Argentina (country_ID = 116)
INSERT INTO Province (province_name, country_ID) VALUES
('Buenos Aires', 116),
('Catamarca', 116),
('Chaco', 116),
('Chubut', 116),
('Córdoba', 116),
('Corrientes', 116),
('Entre Ríos', 116),
('Formosa', 116),
('Jujuy', 116),
('La Pampa', 116),
('La Rioja', 116),
('Mendoza', 116),
('Misiones', 116),
('Neuquén', 116),
('Río Negro', 116),
('Salta', 116),
('San Juan', 116),
('San Luis', 116),
('Santa Cruz', 116),
('Santa Fe', 116),
('Santiago del Estero', 116),
('Tierra del Fuego', 116),
('Tucumán', 116);

-- Brazil (country_ID = 117)
INSERT INTO Province (province_name, country_ID) VALUES
('Acre', 117),
('Alagoas', 117),
('Amapá', 117),
('Amazonas', 117),
('Bahia', 117),
('Ceará', 117),
('Distrito Federal', 117),
('Espírito Santo', 117),
('Goiás', 117),
('Maranhão', 117),
('Mato Grosso', 117),
('Mato Grosso do Sul', 117),
('Minas Gerais', 117),
('Pará', 117),
('Paraíba', 117),
('Paraná', 117),
('Pernambuco', 117),
('Piauí', 117),
('Rio de Janeiro', 117),
('Rio Grande do Norte', 117),
('Rio Grande do Sul', 117),
('Rondônia', 117),
('Roraima', 117),
('Santa Catarina', 117),
('São Paulo', 117),
('Sergipe', 117),
('Tocantins', 117);

-- Chile (country_ID = 118)
INSERT INTO Province (province_name, country_ID) VALUES
('Arica y Parinacota', 118),
('Tarapacá', 118),
('Antofagasta', 118),
('Atacama', 118),
('Coquimbo', 118),
('Valparaíso', 118),
('Metropolitana', 118),
('O’Higgins', 118),
('Maule', 118),
('Ñuble', 118),
('Biobío', 118),
('Araucanía', 118),
('Los Ríos', 118),
('Los Lagos', 118),
('Aysén', 118),
('Magallanes', 118);

-- Colombia (country_ID = 119)
INSERT INTO Province (province_name, country_ID) VALUES
('Amazonas', 119),
('Antioquia', 119),
('Arauca', 119),
('Atlántico', 119),
('Bolívar', 119),
('Boyacá', 119),
('Caldas', 119),
('Caquetá', 119),
('Casanare', 119),
('Cauca', 119),
('Cesar', 119),
('Chocó', 119),
('Córdoba', 119),
('Cundinamarca', 119),
('Guainía', 119),
('Guaviare', 119),
('Huila', 119),
('La Guajira', 119),
('Magdalena', 119),
('Meta', 119),
('Nariño', 119),
('Norte de Santander', 119),
('Putumayo', 119),
('Quindío', 119),
('Risaralda', 119),
('San Andrés y Providencia', 119),
('Santander', 119),
('Sucre', 119),
('Tolima', 119),
('Valle del Cauca', 119),
('Vaupés', 119),
('Vichada', 119);

-- Peru (country_ID = 120)
INSERT INTO Province (province_name, country_ID) VALUES
('Amazonas', 120),
('Áncash', 120),
('Apurímac', 120),
('Arequipa', 120),
('Ayacucho', 120),
('Cajamarca', 120),
('Callao', 120),
('Cusco', 120),
('Huancavelica', 120),
('Huánuco', 120),
('Ica', 120),
('Junín', 120),
('La Libertad', 120),
('Lambayeque', 120),
('Lima', 120),
('Loreto', 120),
('Madre de Dios', 120),
('Moquegua', 120),
('Pasco', 120),
('Piura', 120),
('Puno', 120),
('San Martín', 120),
('Tacna', 120),
('Tumbes', 120),
('Ucayali', 120);

-- Venezuela (country_ID = 121)
INSERT INTO Province (province_name, country_ID) VALUES
('Amazonas', 121),
('Anzoátegui', 121),
('Apure', 121),
('Aragua', 121),
('Barinas', 121),
('Bolívar', 121),
('Carabobo', 121),
('Cojedes', 121),
('Delta Amacuro', 121),
('Falcón', 121),
('Guárico', 121),
('Lara', 121),
('Mérida', 121),
('Miranda', 121),
('Monagas', 121),
('Nueva Esparta', 121),
('Portuguesa', 121),
('Sucre', 121),
('Táchira', 121),
('Trujillo', 121),
('Vargas', 121),
('Yaracuy', 121),
('Zulia', 121);

-- =======================================
-- Africa
-- =======================================

-- Nigeria (country_ID = 122)
INSERT INTO Province (province_name, country_ID) VALUES
('Abia', 122),
('Adamawa', 122),
('Akwa Ibom', 122),
('Anambra', 122),
('Bauchi', 122),
('Bayelsa', 122),
('Benue', 122),
('Borno', 122),
('Cross River', 122),
('Delta', 122),
('Ebonyi', 122),
('Edo', 122),
('Ekiti', 122),
('Enugu', 122),
('Gombe', 122),
('Imo', 122),
('Jigawa', 122),
('Kaduna', 122),
('Kano', 122),
('Katsina', 122),
('Kebbi', 122),
('Kogi', 122),
('Kwara', 122),
('Lagos', 122),
('Nasarawa', 122),
('Niger', 122),
('Ogun', 122),
('Ondo', 122),
('Osun', 122),
('Oyo', 122),
('Plateau', 122),
('Rivers', 122),
('Sokoto', 122),
('Taraba', 122),
('Yobe', 122),
('Zamfara', 122),
('Federal Capital Territory', 122);

-- South Africa (country_ID = 123)
INSERT INTO Province (province_name, country_ID) VALUES
('Eastern Cape', 123),
('Free State', 123),
('Gauteng', 123),
('KwaZulu-Natal', 123),
('Limpopo', 123),
('Mpumalanga', 123),
('Northern Cape', 123),
('North West', 123),
('Western Cape', 123);

-- Egypt (country_ID = 124)
INSERT INTO Province (province_name, country_ID) VALUES
('Cairo', 124),
('Alexandria', 124),
('Giza', 124),
('Port Said', 124),
('Suez', 124),
('Aswan', 124),
('Asyut', 124),
('Beheira', 124),
('Beni Suef', 124),
('Dakahlia', 124),
('Damietta', 124),
('Faiyum', 124),
('Gharbia', 124),
('Ismailia', 124),
('Kafr El Sheikh', 124),
('Luxor', 124),
('Matruh', 124),
('Minya', 124),
('Monufia', 124),
('New Valley', 124),
('North Sinai', 124),
('Port Said', 124),
('Qalyubia', 124),
('Qena', 124),
('Red Sea', 124),
('Sharqia', 124),
('Sohag', 124),
('South Sinai', 124),
('Damietta', 124);

-- Kenya (country_ID = 125)
INSERT INTO Province (province_name, country_ID) VALUES
('Baringo', 125),
('Bomet', 125),
('Bungoma', 125),
('Busia', 125),
('Elgeyo-Marakwet', 125),
('Embu', 125),
('Garissa', 125),
('Homa Bay', 125),
('Isiolo', 125),
('Kajiado', 125),
('Kakamega', 125),
('Kericho', 125),
('Kiambu', 125),
('Kilifi', 125),
('Kirinyaga', 125),
('Kisii', 125),
('Kisumu', 125),
('Kitui', 125),
('Kwale', 125),
('Laikipia', 125),
('Lamu', 125),
('Machakos', 125),
('Makueni', 125),
('Mandera', 125),
('Meru', 125),
('Migori', 125),
('Mombasa', 125),
('Murang’a', 125),
('Nairobi', 125),
('Nakuru', 125),
('Nandi', 125),
('Narok', 125),
('Nyamira', 125),
('Nyandarua', 125),
('Nyeri', 125),
('Samburu', 125),
('Siaya', 125),
('Taita-Taveta', 125),
('Tana River', 125),
('Tharaka-Nithi', 125),
('Trans-Nzoia', 125),
('Turkana', 125),
('Uasin Gishu', 125),
('Vihiga', 125),
('Wajir', 125),
('West Pokot', 125);

-- Morocco (country_ID = 126)
INSERT INTO Province (province_name, country_ID) VALUES
('Agadir-Ida Ou Tanane', 126),
('Al Hoceima', 126),
('Aousserd', 126),
('Béni Mellal', 126),
('Berkane', 126),
('Casablanca-Settat', 126),
('Chefchaouen', 126),
('Chtouka-Aït Baha', 126),
('Dakhla-Oued Ed-Dahab', 126),
('El Jadida', 126),
('Errachidia', 126),
('Essaouira', 126),
('Fès-Meknès', 126),
('Figuig', 126),
('Guelmim-Oued Noun', 126),
('Ifrane', 126),
('Kénitra', 126),
('Khemisset', 126),
('Khenifra', 126),
('Khouribga', 126),
('Laâyoune-Sakia El Hamra', 126),
('Marrakech-Safi', 126),
('Meknès', 126),
('Nador', 126),
('Ouarzazate', 126),
('Oujda-Angad', 126),
('Rabat-Salé-Kénitra', 126),
('Safi', 126),
('Settat', 126),
('Sidi Kacem', 126),
('Tangier-Tetouan-Al Hoceima', 126),
('Tanger', 126),
('Taroudant', 126),
('Taza', 126),
('Tétouan', 126),
('Tiznit', 126);

-- South Sudan (country_ID = 127)
INSERT INTO Province (province_name, country_ID) VALUES
('Central Equatoria', 127),
('Eastern Equatoria', 127),
('Jonglei', 127),
('Lakes', 127),
('Northern Bahr el Ghazal', 127),
('Unity', 127),
('Upper Nile', 127),
('Warrap', 127),
('Western Bahr el Ghazal', 127),
('Western Equatoria', 127);

-- Ethiopia (country_ID = 128)
INSERT INTO Province (province_name, country_ID) VALUES
('Addis Ababa', 128),
('Afar', 128),
('Amhara', 128),
('Benishangul-Gumuz', 128),
('Dire Dawa', 128),
('Gambela', 128),
('Harari', 128),
('Oromia', 128),
('Sidama', 128),
('Somali', 128),
('Southern Nations, Nationalities, and Peoples Region', 128),
('Tigray', 128);

-- Algeria (country_ID = 129)
INSERT INTO Province (province_name, country_ID) VALUES
('Adrar', 129),
('Chlef', 129),
('Laghouat', 129),
('Oum El Bouaghi', 129),
('Batna', 129),
('Béjaïa', 129),
('Biskra', 129),
('Béchar', 129),
('Blida', 129),
('Bouira', 129),
('Tamanrasset', 129),
('Tébessa', 129),
('Tlemcen', 129),
('Tiaret', 129),
('Tizi Ouzou', 129),
('Algiers', 129),
('Djelfa', 129),
('Jijel', 129),
('Sétif', 129),
('Saïda', 129),
('Skikda', 129),
('Sidi Bel Abbès', 129),
('Annaba', 129),
('Guelma', 129),
('Constantine', 129),
('Médéa', 129),
('Mostaganem', 129),
('M’Sila', 129),
('Mascara', 129),
('Ouargla', 129),
('Oran', 129),
('El Bayadh', 129),
('Illizi', 129),
('Bordj Bou Arréridj', 129),
('Boumerdès', 129),
('El Tarf', 129),
('Tindouf', 129),
('Tissemsilt', 129),
('El Oued', 129),
('Khenchela', 129),
('Souk Ahras', 129),
('Tipaza', 129),
('Mila', 129),
('Aïn Defla', 129),
('Naama', 129),
('Aïn Témouchent', 129),
('Ghardaïa', 129),
('Relizane', 129);

-- Ghana (country_ID = 130)
INSERT INTO Province (province_name, country_ID) VALUES
('Ahafo', 130),
('Ashanti', 130),
('Bono', 130),
('Bono East', 130),
('Central', 130),
('Eastern', 130),
('Greater Accra', 130),
('North East', 130),
('Northern', 130),
('Oti', 130),
('Savannah', 130),
('Upper East', 130),
('Upper West', 130),
('Volta', 130),
('Western', 130),
('Western North', 130);

-- Morocco already covered earlier

-- Tunisia (country_ID = 131)
INSERT INTO Province (province_name, country_ID) VALUES
('Ariana', 131),
('Béja', 131),
('Ben Arous', 131),
('Bizerte', 131),
('Gabès', 131),
('Gafsa', 131),
('Jendouba', 131),
('Kairouan', 131),
('Kasserine', 131),
('Kebili', 131),
('La Manouba', 131),
('Le Kef', 131),
('Mahdia', 131),
('Médenine', 131),
('Monastir', 131),
('Nabeul', 131),
('Sfax', 131),
('Sidi Bouzid', 131),
('Siliana', 131),
('Sousse', 131),
('Tataouine', 131),
('Tozeur', 131),
('Tunis', 131),
('Zaghouan', 131);

-- Angola (country_ID = 132)
INSERT INTO Province (province_name, country_ID) VALUES
('Bengo', 132),
('Benguela', 132),
('Bié', 132),
('Cabinda', 132),
('Cuando Cubango', 132),
('Cuanza Norte', 132),
('Cuanza Sul', 132),
('Cunene', 132),
('Huambo', 132),
('Huíla', 132),
('Luanda', 132),
('Lunda Norte', 132),
('Lunda Sul', 132),
('Malanje', 132),
('Moxico', 132),
('Namibe', 132),
('Uíge', 132),
('Zaire', 132);

-- Senegal (country_ID = 133)
INSERT INTO Province (province_name, country_ID) VALUES
('Dakar', 133),
('Diourbel', 133),
('Fatick', 133),
('Kaolack', 133),
('Kédougou', 133),
('Kolda', 133),
('Louga', 133),
('Matam', 133),
('Saint-Louis', 133),
('Sédhiou', 133),
('Tambacounda', 133),
('Thiès', 133),
('Ziguinchor', 133);

-- Ivory Coast / Côte d'Ivoire (country_ID = 134)
INSERT INTO Province (province_name, country_ID) VALUES
('Abidjan', 134),
('Bas-Sassandra', 134),
('Comoé', 134),
('Denguélé', 134),
('Gôh-Djiboua', 134),
('Lacs', 134),
('Lagunes', 134),
('Montagnes', 134),
('Sassandra-Marahoué', 134),
('Savanes', 134),
('Vallée du Bandama', 134),
('Woroba', 134),
('Yamoussoukro', 134),
('Zanzan', 134);

-- Democratic Republic of Congo (country_ID = 135)
INSERT INTO Province (province_name, country_ID) VALUES
('Bandundu', 135),
('Bas-Uélé', 135),
('Équateur', 135),
('Haut-Katanga', 135),
('Haut-Uélé', 135),
('Ituri', 135),
('Kasaï', 135),
('Kasaï-Central', 135),
('Kasaï-Oriental', 135),
('Kinshasa', 135),
('Kwango', 135),
('Kwilu', 135),
('Lomami', 135),
('Lualaba', 135),
('Mai-Ndombe', 135),
('Maniema', 135),
('Mongala', 135),
('Nord-Kivu', 135),
('Nord-Ubangi', 135),
('Sankuru', 135),
('Sud-Kivu', 135),
('Sud-Ubangi', 135),
('Tanganyika', 135),
('Tshopo', 135),
('Tshuapa', 135);

-- ======
-- Ocenia
-- ===

-- Australia (country_ID = 136)
INSERT INTO Province (province_name, country_ID) VALUES
('Australian Capital Territory', 136),
('New South Wales', 136),
('Northern Territory', 136),
('Queensland', 136),
('South Australia', 136),
('Tasmania', 136),
('Victoria', 136),
('Western Australia', 136);

-- New Zealand (country_ID = 137)
INSERT INTO Province (province_name, country_ID) VALUES
('Auckland', 137),
('Bay of Plenty', 137),
('Canterbury', 137),
('Gisborne', 137),
('Hawke’s Bay', 137),
('Manawatu-Wanganui', 137),
('Marlborough', 137),
('Nelson', 137),
('Northland', 137),
('Otago', 137),
('Southland', 137),
('Taranaki', 137),
('Tasman', 137),
('Waikato', 137),
('Wellington', 137),
('West Coast', 137);

-- Papua New Guinea (country_ID = 138)
INSERT INTO Province (province_name, country_ID) VALUES
('Central', 138),
('Chimbu', 138),
('Eastern Highlands', 138),
('East New Britain', 138),
('East Sepik', 138),
('Enga', 138),
('Gulf', 138),
('Hela', 138),
('Jiwaka', 138),
('Madang', 138),
('Manus', 138),
('Milne Bay', 138),
('Morobe', 138),
('New Ireland', 138),
('Northern', 138),
('Sandaun (West Sepik)', 138),
('Southern Highlands', 138),
('Western Highlands', 138),
('Western', 138),
('Western New Britain', 138),
('National Capital District', 138),
('Bougainville', 138);

-- Fiji (country_ID = 139)
INSERT INTO Province (province_name, country_ID) VALUES
('Central', 139),
('Eastern', 139),
('Northern', 139),
('Western', 139);

-- Solomon Islands (country_ID = 140)
INSERT INTO Province (province_name, country_ID) VALUES
('Central', 140),
('Choiseul', 140),
('Guadalcanal', 140),
('Honiara', 140),
('Isabel', 140),
('Makira-Ulawa', 140),
('Malaita', 140),
('Rennell and Bellona', 140),
('Temotu', 140),
('Western', 140);

-- Vanuatu (country_ID = 141)
INSERT INTO Province (province_name, country_ID) VALUES
('Malampa', 141),
('Penama', 141),
('Sanma', 141),
('Shefa', 141),
('Tafea', 141),
('Torba', 141);

-- Samoa (country_ID = 142)
INSERT INTO Province (province_name, country_ID) VALUES
('A'ana', 142),
('Aiga-i-le-Tai', 142),
('Atua', 142),
('Fa\'asaleleaga', 142),
('Gaga\'emauga', 142),
('Gagaifomauga', 142),
('Palauli', 142),
('Satupa\'itea', 142),
('Tuamasaga', 142),
('Vaa-o-Fonoti', 142),
('Vaisigano', 142);

-- Tonga (country_ID = 143)
INSERT INTO Province (province_name, country_ID) VALUES
('Eua', 143),
('Ha’apai', 143),
('Niuas', 143),
('Tongatapu', 143),
('Vava’u', 143);

-- =======================================
-- Cities
-- =======================================

-- Philippines (country_ID = 102)
-- Using sample major cities per province
INSERT INTO City (city_name, province_ID) VALUES
('Manila', 1),
('Quezon City', 1),
('Caloocan', 1),
('Makati', 1),
('Davao City', 2),
('Cebu City', 3),
('Zamboanga City', 4),
('Iloilo City', 5),
('Baguio', 6),
('Cagayan de Oro', 7);

-- Japan (country_ID = 103)
INSERT INTO City (city_name, province_ID) VALUES
('Tokyo', 101),
('Osaka', 102),
('Kyoto', 103),
('Nagoya', 104),
('Sapporo', 105),
('Fukuoka', 106),
('Kobe', 107),
('Yokohama', 108),
('Hiroshima', 109),
('Sendai', 110);

-- China (country_ID = 104)
INSERT INTO City (city_name, province_ID) VALUES
('Beijing', 111),
('Shanghai', 112),
('Guangzhou', 113),
('Shenzhen', 114),
('Chengdu', 115),
('Chongqing', 116),
('Hangzhou', 117),
('Wuhan', 118),
('Xi’an', 119),
('Nanjing', 120);

-- India (country_ID = 105)
INSERT INTO City (city_name, province_ID) VALUES
('New Delhi', 121),
('Mumbai', 122),
('Bangalore', 123),
('Kolkata', 124),
('Chennai', 125),
('Hyderabad', 126),
('Pune', 127),
('Ahmedabad', 128),
('Jaipur', 129),
('Lucknow', 130);

-- South Korea (country_ID = 106)
INSERT INTO City (city_name, province_ID) VALUES
('Seoul', 131),
('Busan', 132),
('Incheon', 133),
('Daegu', 134),
('Daejeon', 135),
('Gwangju', 136),
('Suwon', 137),
('Ulsan', 138),
('Changwon', 139),
('Goyang', 140);

-- Indonesia (country_ID = 107)
INSERT INTO City (city_name, province_ID) VALUES
('Jakarta', 141),
('Surabaya', 142),
('Bandung', 143),
('Medan', 144),
('Semarang', 145),
('Makassar', 146),
('Palembang', 147),
('Tangerang', 148),
('Bekasi', 149),
('Depok', 150);

-- Thailand (country_ID = 108)
INSERT INTO City (city_name, province_ID) VALUES
('Bangkok', 151),
('Chiang Mai', 152),
('Phuket', 153),
('Khon Kaen', 154),
('Nakhon Ratchasima', 155),
('Hat Yai', 156),
('Udon Thani', 157),
('Chonburi', 158),
('Chiang Rai', 159),
('Pattaya', 160);

-- Vietnam (country_ID = 109)
INSERT INTO City (city_name, province_ID) VALUES
('Hanoi', 161),
('Ho Chi Minh City', 162),
('Da Nang', 163),
('Hai Phong', 164),
('Can Tho', 165),
('Nha Trang', 166),
('Hue', 167),
('Bien Hoa', 168),
('Vung Tau', 169),
('Hai Duong', 170);

-- Malaysia (country_ID = 110)
INSERT INTO City (city_name, province_ID) VALUES
('Kuala Lumpur', 171),
('George Town', 172),
('Johor Bahru', 173),
('Ipoh', 174),
('Shah Alam', 175),
('Petaling Jaya', 176),
('Kota Kinabalu', 177),
('Kuching', 178),
('Malacca City', 179),
('Alor Setar', 180);

-- Singapore (country_ID = 111)
INSERT INTO City (city_name, province_ID) VALUES
('Singapore', 181);

-- Europe
-- United Kingdom (country_ID = 112)
INSERT INTO City (city_name, province_ID) VALUES
('London', 182),
('Manchester', 183),
('Birmingham', 184),
('Liverpool', 185),
('Leeds', 186),
('Glasgow', 187),
('Edinburgh', 188),
('Bristol', 189),
('Sheffield', 190),
('Cardiff', 191);

-- Germany (country_ID = 113)
INSERT INTO City (city_name, province_ID) VALUES
('Berlin', 192),
('Hamburg', 193),
('Munich', 194),
('Cologne', 195),
('Frankfurt', 196),
('Stuttgart', 197),
('Düsseldorf', 198),
('Dortmund', 199),
('Essen', 200),
('Leipzig', 201);

-- France (country_ID = 114)
INSERT INTO City (city_name, province_ID) VALUES
('Paris', 202),
('Marseille', 203),
('Lyon', 204),
('Toulouse', 205),
('Nice', 206),
('Nantes', 207),
('Strasbourg', 208),
('Montpellier', 209),
('Bordeaux', 210),
('Lille', 211);

-- Italy (country_ID = 115)
INSERT INTO City (city_name, province_ID) VALUES
('Rome', 212),
('Milan', 213),
('Naples', 214),
('Turin', 215),
('Palermo', 216),
('Genoa', 217),
('Bologna', 218),
('Florence', 219),
('Venice', 220),
('Verona', 221);

-- Spain (country_ID = 116)
INSERT INTO City (city_name, province_ID) VALUES
('Madrid', 222),
('Barcelona', 223),
('Valencia', 224),
('Seville', 225),
('Zaragoza', 226),
('Málaga', 227),
('Murcia', 228),
('Palma', 229),
('Bilbao', 230),
('Alicante', 231);

-- Netherlands (country_ID = 117)
INSERT INTO City (city_name, province_ID) VALUES
('Amsterdam', 232),
('Rotterdam', 233),
('The Hague', 234),
('Utrecht', 235),
('Eindhoven', 236),
('Tilburg', 237),
('Groningen', 238),
('Breda', 239),
('Nijmegen', 240),
('Enschede', 241);

-- Sweden (country_ID = 118)
INSERT INTO City (city_name, province_ID) VALUES
('Stockholm', 242),
('Gothenburg', 243),
('Malmö', 244),
('Uppsala', 245),
('Västerås', 246),
('Örebro', 247),
('Linköping', 248),
('Helsingborg', 249),
('Jönköping', 250),
('Norrköping', 251);

-- Norway (country_ID = 119)
INSERT INTO City (city_name, province_ID) VALUES
('Oslo', 252),
('Bergen', 253),
('Stavanger', 254),
('Trondheim', 255),
('Drammen', 256),
('Fredrikstad', 257),
('Kristiansand', 258),
('Tromsø', 259),
('Sandnes', 260),
('Ålesund', 261);

-- Denmark (country_ID = 120)
INSERT INTO City (city_name, province_ID) VALUES
('Copenhagen', 262),
('Aarhus', 263),
('Odense', 264),
('Aalborg', 265),
('Esbjerg', 266),
('Randers', 267),
('Kolding', 268),
('Horsens', 269),
('Vejle', 270),
('Roskilde', 271);

-- Poland (country_ID = 121)
INSERT INTO City (city_name, province_ID) VALUES
('Warsaw', 272),
('Kraków', 273),
('Łódź', 274),
('Wrocław', 275),
('Poznań', 276),
('Gdańsk', 277),
('Szczecin', 278),
('Bydgoszcz', 279),
('Lublin', 280),
('Katowice', 281);

-- Czech Republic (country_ID = 122)
INSERT INTO City (city_name, province_ID) VALUES
('Prague', 282),
('Brno', 283),
('Ostrava', 284),
('Plzeň', 285),
('Liberec', 286),
('Olomouc', 287),
('Ústí nad Labem', 288),
('Hradec Králové', 289),
('Pardubice', 290),
('Zlín', 291);

-- Austria (country_ID = 123)
INSERT INTO City (city_name, province_ID) VALUES
('Vienna', 292),
('Graz', 293),
('Linz', 294),
('Salzburg', 295),
('Innsbruck', 296),
('Klagenfurt', 297),
('Villach', 298),
('Wels', 299),
('Sankt Pölten', 300),
('Dornbirn', 301);

-- Switzerland (country_ID = 124)
INSERT INTO City (city_name, province_ID) VALUES
('Zurich', 302),
('Geneva', 303),
('Basel', 304),
('Bern', 305),
('Lausanne', 306),
('Winterthur', 307),
('Lucerne', 308),
('St. Gallen', 309),
('Lugano', 310),
('Biel/Bienne', 311);

-- =====
-- Africa

-- Nigeria (country_ID = 125)
INSERT INTO City (city_name, province_ID) VALUES
('Lagos', 312),
('Abuja', 313),
('Kano', 314),
('Ibadan', 315),
('Port Harcourt', 316),
('Benin City', 317),
('Maiduguri', 318),
('Zaria', 319),
('Jos', 320),
('Ilorin', 321);

-- South Africa (country_ID = 126)
INSERT INTO City (city_name, province_ID) VALUES
('Johannesburg', 322),
('Cape Town', 323),
('Durban', 324),
('Pretoria', 325),
('Port Elizabeth', 326),
('Bloemfontein', 327),
('Nelspruit', 328),
('Kimberley', 329),
('East London', 330),
('Pietermaritzburg', 331);

-- Kenya (country_ID = 127)
INSERT INTO City (city_name, province_ID) VALUES
('Nairobi', 332),
('Mombasa', 333),
('Kisumu', 334),
('Nakuru', 335),
('Eldoret', 336),
('Thika', 337),
('Malindi', 338),
('Kitale', 339),
('Naivasha', 340),
('Machakos', 341);

-- Egypt (country_ID = 128)
INSERT INTO City (city_name, province_ID) VALUES
('Cairo', 342),
('Alexandria', 343),
('Giza', 344),
('Shubra El Kheima', 345),
('Port Said', 346),
('Suez', 347),
('Luxor', 348),
('Mansoura', 349),
('Tanta', 350),
('Asyut', 351);

-- Ghana (country_ID = 130)
INSERT INTO City (city_name, province_ID) VALUES
('Accra', 352),
('Kumasi', 353),
('Tamale', 354),
('Takoradi', 355),
('Tema', 356),
('Cape Coast', 357),
('Sunyani', 358),
('Koforidua', 359),
('Ho', 360),
('Bolgatanga', 361);

-- Algeria (country_ID = 129)
INSERT INTO City (city_name, province_ID) VALUES
('Algiers', 362),
('Oran', 363),
('Constantine', 364),
('Annaba', 365),
('Blida', 366),
('Batna', 367),
('Setif', 368),
('Sidi Bel Abbes', 369),
('Tlemcen', 370),
('Bejaia', 371);

-- Morocco (country_ID = 131)
INSERT INTO City (city_name, province_ID) VALUES
('Rabat', 372),
('Casablanca', 373),
('Marrakech', 374),
('Fes', 375),
('Tangier', 376),
('Agadir', 377),
('Meknes', 378),
('Oujda', 379),
('Kenitra', 380),
('Tetouan', 381);

-- Tunisia (country_ID = 132)
INSERT INTO City (city_name, province_ID) VALUES
('Tunis', 382),
('Sfax', 383),
('Sousse', 384),
('Kairouan', 385),
('Bizerte', 386),
('Gabes', 387),
('Aryanah', 388),
('Gafsa', 389),
('Monastir', 390),
('Sidi Bouzid', 391);

-- Angola (country_ID = 133)
INSERT INTO City (city_name, province_ID) VALUES
('Luanda', 392),
('Huambo', 393),
('Benguela', 394),
('Lubango', 395),
('Malanje', 396),
('Uíge', 397),
('Namibe', 398),
('Cabinda', 399),
('Kuito', 400),
('Soyo', 401);

-- Senegal (country_ID = 134)
INSERT INTO City (city_name, province_ID) VALUES
('Dakar', 402),
('Thiès', 403),
('Saint-Louis', 404),
('Kaolack', 405),
('Ziguinchor', 406),
('Touba', 407),
('Diourbel', 408),
('Mbour', 409),
('Tambacounda', 410),
('Kolda', 411);

-- Ivory Coast / Cote d'Ivoire (country_ID = 135)
INSERT INTO City (city_name, province_ID) VALUES
('Abidjan', 412),
('Yamoussoukro', 413),
('Bouaké', 414),
('Daloa', 415),
('San Pedro', 416),
('Gagnoa', 417),
('Man', 418),
('Korhogo', 419),
('Divo', 420),
('Anyama', 421);

-- America
-- United States (country_ID = 136)
INSERT INTO City (city_name, province_ID) VALUES
('New York', 422),
('Los Angeles', 423),
('Chicago', 424),
('Houston', 425),
('Phoenix', 426),
('Philadelphia', 427),
('San Antonio', 428),
('San Diego', 429),
('Dallas', 430),
('San Jose', 431);

-- Canada (country_ID = 137)
INSERT INTO City (city_name, province_ID) VALUES
('Toronto', 432),
('Montreal', 433),
('Vancouver', 434),
('Calgary', 435),
('Edmonton', 436),
('Ottawa', 437),
('Winnipeg', 438),
('Quebec City', 439),
('Hamilton', 440),
('Kitchener', 441);

-- Mexico (country_ID = 138)
INSERT INTO City (city_name, province_ID) VALUES
('Mexico City', 442),
('Guadalajara', 443),
('Monterrey', 444),
('Puebla', 445),
('Tijuana', 446),
('León', 447),
('Ciudad Juárez', 448),
('Zapopan', 449),
('Mérida', 450),
('San Luis Potosí', 451);

-- Brazil (country_ID = 139)
INSERT INTO City (city_name, province_ID) VALUES
('São Paulo', 452),
('Rio de Janeiro', 453),
('Brasília', 454),
('Salvador', 455),
('Fortaleza', 456),
('Belo Horizonte', 457),
('Manaus', 458),
('Curitiba', 459),
('Recife', 460),
('Porto Alegre', 461);

-- Argentina (country_ID = 140)
INSERT INTO City (city_name, province_ID) VALUES
('Buenos Aires', 462),
('Córdoba', 463),
('Rosario', 464),
('Mendoza', 465),
('La Plata', 466),
('San Miguel de Tucumán', 467),
('Mar del Plata', 468),
('Salta', 469),
('Santa Fe', 470),
('San Juan', 471);

-- Colombia (country_ID = 141)
INSERT INTO City (city_name, province_ID) VALUES
('Bogotá', 472),
('Medellín', 473),
('Cali', 474),
('Barranquilla', 475),
('Cartagena', 476),
('Cúcuta', 477),
('Bucaramanga', 478),
('Pereira', 479),
('Santa Marta', 480),
('Ibagué', 481);

-- Chile (country_ID = 142)
INSERT INTO City (city_name, province_ID) VALUES
('Santiago', 482),
('Valparaíso', 483),
('Concepción', 484),
('La Serena', 485),
('Antofagasta', 486),
('Temuco', 487),
('Rancagua', 488),
('Iquique', 489),
('Puerto Montt', 490),
('Arica', 491);

-- Peru (country_ID = 143)
INSERT INTO City (city_name, province_ID) VALUES
('Lima', 492),
('Arequipa', 493),
('Trujillo', 494),
('Chiclayo', 495),
('Piura', 496),
('Cusco', 497),
('Iquitos', 498),
('Huancayo', 499),
('Puno', 500),
('Tacna', 501);

-- Venezuela (country_ID = 144)
INSERT INTO City (city_name, province_ID) VALUES
('Caracas', 502),
('Maracaibo', 503),
('Valencia', 504),
('Barquisimeto', 505),
('Ciudad Guayana', 506),
('Puerto La Cruz', 507),
('Maturín', 508),
('Puerto Cabello', 509),
('Barcelona', 510),
('San Cristóbal', 511);

-- Ecuador (country_ID = 145)
INSERT INTO City (city_name, province_ID) VALUES
('Quito', 512),
('Guayaquil', 513),
('Cuenca', 514),
('Santo Domingo', 515),
('Machala', 516),
('Manta', 517),
('Portoviejo', 518),
('Loja', 519),
('Ambato', 520),
('Riobamba', 521);

-- Bolivia (country_ID = 146)
INSERT INTO City (city_name, province_ID) VALUES
('La Paz', 522),
('Santa Cruz de la Sierra', 523),
('Cochabamba', 524),
('Oruro', 525),
('Sucre', 526),
('Potosí', 527),
('Tarija', 528),
('Trinidad', 529),
('Cobija', 530),
('Montero', 531);

-- Paraguay (country_ID = 147)
INSERT INTO City (city_name, province_ID) VALUES
('Asunción', 532),
('Ciudad del Este', 533),
('Encarnación', 534),
('San Lorenzo', 535),
('Luque', 536),
('Capiatá', 537),
('Lambaré', 538),
('Fernando de la Mora', 539),
('Ñemby', 540),
('Caaguazú', 541);

-- Uruguay (country_ID = 148)
INSERT INTO City (city_name, province_ID) VALUES
('Montevideo', 542),
('Salto', 543),
('Paysandú', 544),
('Las Piedras', 545),
('Rivera', 546),
('Maldonado', 547),
('Tacuarembó', 548),
('Melo', 549),
('Mercedes', 550),
('Minas', 551);

-- Central America & Caribbean (sample major cities)
-- Panama (country_ID = 149)
INSERT INTO City (city_name, province_ID) VALUES
('Panama City', 552),
('Colón', 553),
('David', 554),
('Santiago', 555),
('La Chorrera', 556);

-- Costa Rica (country_ID = 150)
INSERT INTO City (city_name, province_ID) VALUES
('San José', 557),
('Alajuela', 558),
('Cartago', 559),
('Heredia', 560),
('Liberia', 561);

-- Jamaica (country_ID = 151)
INSERT INTO City (city_name, province_ID) VALUES
('Kingston', 562),
('Montego Bay', 563),
('Spanish Town', 564),
('Portmore', 565),
('Mandeville', 566);

-- Haiti (country_ID = 152)
INSERT INTO City (city_name, province_ID) VALUES
('Port-au-Prince', 567),
('Cap-Haïtien', 568),
('Gonaïves', 569),
('Les Cayes', 570),
('Jacmel', 571);

-- Oceania

-- Australia (country_ID = 153)
INSERT INTO City (city_name, province_ID) VALUES
('Sydney', 562),
('Melbourne', 563),
('Brisbane', 564),
('Perth', 565),
('Adelaide', 566),
('Gold Coast', 567),
('Canberra', 568),
('Newcastle', 569),
('Wollongong', 570),
('Logan City', 571);

-- New Zealand (country_ID = 154)
INSERT INTO City (city_name, province_ID) VALUES
('Auckland', 572),
('Wellington', 573),
('Christchurch', 574),
('Hamilton', 575),
('Tauranga', 576),
('Napier-Hastings', 577),
('Dunedin', 578),
('Palmerston North', 579),
('Nelson', 580),
('Rotorua', 581);

-- Fiji (country_ID = 155)
INSERT INTO City (city_name, province_ID) VALUES
('Suva', 582),
('Nadi', 583),
('Lautoka', 584),
('Labasa', 585),
('Ba', 586),
('Levuka', 587),
('Sigatoka', 588),
('Savusavu', 589),
('Nausori', 590),
('Rakiraki', 591);

-- Papua New Guinea (country_ID = 156)
INSERT INTO City (city_name, province_ID) VALUES
('Port Moresby', 592),
('Lae', 593),
('Mount Hagen', 594),
('Madang', 595),
('Goroka', 596),
('Kokopo', 597),
('Wewak', 598),
('Vanimo', 599),
('Popondetta', 600),
('Buka', 601);

-- Samoa (country_ID = 157)
INSERT INTO City (city_name, province_ID) VALUES
('Apia', 602),
('Vaitele', 603),
('Faleula', 604),
('Leulumoega', 605),
('Lalomanu', 606);










