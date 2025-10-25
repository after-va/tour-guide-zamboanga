INSERT INTO Region (region_name, country_ID) VALUES
('Region I: Ilocos Region', 161),
('Region II: Cagayan Valley', 161),
('Region III: Central Luzon', 161),
('Region IV-A: CALABARZON', 161),
('Region IV-B: MIMAROPA', 161),
('Region V: Bicol Region', 161),
('Region VI: Western Visayas', 161),
('Region VII: Central Visayas', 161),
('Region VIII: Eastern Visayas', 161),
('Region IX: Zamboanga Peninsula', 161),
('Region X: Northern Mindanao', 161),
('Region XI: Davao Region', 161),
('Region XII: SOCCSKSARGEN', 161),
('Region XIII: Caraga', 161),
('NCR: National Capital Region', 161),
('CAR: Cordillera Administrative Region', 161),
('BARMM: Bangsamoro Autonomous Region in Muslim Mindanao', 161);

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


