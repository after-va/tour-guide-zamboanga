
-- Insert default roles
INSERT INTO Role(role_name) VALUES 
('Admin'),
('Tour Guide'),
('Tourist')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- Insert default payment methods
INSERT INTO Method_Category (methodcategory_name, methodcategory_type, methodcategory_processing_fee) VALUES
('Credit Card', 'card', 2.50),
('Debit Card', 'card', 2.50),
('GCash', 'ewallet', 1.00),
('PayMaya', 'ewallet', 1.00),
('Bank Transfer', 'bank', 0.00),
('Cash', 'cash', 0.00)
ON DUPLICATE KEY UPDATE methodcategory_name = VALUES(methodcategory_name);

INSERT IGNORE INTO Tour_Spots(spots_name, spots_description, spots_category, spots_address, spots_googlelink) VALUES 
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

INSERT INTO Languages (language_name) VALUES
('English'),
('Filipino'),
('Chavacano');

