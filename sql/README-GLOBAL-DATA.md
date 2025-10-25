# Global Location Data - README

## Overview
This file contains comprehensive geographical data for the tour guide system, covering countries, provinces/states, cities, and districts from around the world.

## File: global-location-data.sql

### Contents

#### 50+ Countries Included:
- **North America**: United States, Canada, Mexico
- **Europe**: UK, France, Germany, Italy, Spain, Netherlands, Switzerland, Austria, Belgium, Sweden, Norway, Denmark, Finland, Poland, Greece, Portugal, Czech Republic, Ireland, Romania, Hungary
- **Asia**: Japan, China, South Korea, India, Thailand, Singapore, Malaysia, Indonesia, Philippines, Vietnam, UAE, Saudi Arabia, Israel, Turkey, Taiwan, Hong Kong
- **Oceania**: Australia, New Zealand
- **South America**: Brazil, Argentina, Chile, Colombia, Peru
- **Africa**: South Africa, Egypt, Morocco, Kenya, Nigeria

### Data Structure

1. **Countries** (50+ entries)
   - Country name
   - ISO country code (2-letter)

2. **Provinces/States** (80+ entries)
   - Province/State/Region name
   - Linked to parent country

3. **Cities** (100+ entries)
   - Major cities worldwide
   - Linked to parent province/state

4. **Barangays/Districts** (500+ entries)
   - Neighborhoods and districts
   - Linked to parent city

5. **Sample Addresses** (5 entries)
   - Example address records
   - Demonstrates the full hierarchy

### Major Cities Included

**United States:**
- Los Angeles, San Francisco, San Diego (California)
- New York City (New York)
- Houston, Austin (Texas)
- Miami (Florida)

**Europe:**
- London, Manchester (UK)
- Paris, Nice (France)
- Berlin, Munich (Germany)
- Rome, Milan, Venice (Italy)
- Madrid, Barcelona (Spain)
- Amsterdam (Netherlands)
- Zurich (Switzerland)
- Vienna (Austria)
- Brussels (Belgium)
- Stockholm (Sweden)
- Oslo (Norway)
- Copenhagen (Denmark)
- Helsinki (Finland)
- Warsaw (Poland)
- Athens (Greece)
- Lisbon (Portugal)
- Dublin (Ireland)

**Asia:**
- Tokyo, Osaka, Kyoto (Japan)
- Beijing, Shanghai (China)
- Seoul, Busan (South Korea)
- Bangkok, Phuket (Thailand)
- Singapore
- Mumbai, New Delhi (India)
- Dubai, Abu Dhabi (UAE)
- Jakarta, Denpasar/Bali (Indonesia)
- Kuala Lumpur (Malaysia)
- Ho Chi Minh City, Hanoi (Vietnam)
- Manila, Quezon City, Makati, Zamboanga City (Philippines)
- Taipei (Taiwan)
- Hong Kong
- Tel Aviv (Israel)
- Riyadh (Saudi Arabia)
- Istanbul (Turkey)

**Oceania:**
- Sydney, Melbourne (Australia)
- Auckland (New Zealand)

**South America:**
- São Paulo, Rio de Janeiro (Brazil)
- Buenos Aires (Argentina)
- Mexico City, Cancún (Mexico)
- Santiago (Chile)
- Bogotá (Colombia)
- Lima (Peru)

**Africa:**
- Cape Town (South Africa)
- Cairo (Egypt)
- Casablanca (Morocco)
- Nairobi (Kenya)
- Lagos (Nigeria)

### Usage Instructions

1. **Import Order**: Run this file AFTER creating the table structure
   ```bash
   mysql -u username -p database_name < global-location-data.sql
   ```

2. **Prerequisites**: Ensure these tables exist:
   - Country
   - Province
   - City
   - Barangay
   - Address_Info

3. **Data Integrity**: The file uses:
   - SELECT statements with WHERE clauses to maintain referential integrity
   - CROSS JOIN for inserting multiple districts per city
   - Country codes for reliable country identification

### Features

- **Hierarchical Structure**: Country → Province → City → Barangay → Address
- **Global Coverage**: Represents all major continents
- **Tourist Destinations**: Focuses on popular tourist cities
- **Scalable**: Easy to add more locations following the same pattern
- **Safe Inserts**: Uses SELECT statements to avoid foreign key errors

### Notes

- District names use local terminology (e.g., "Arrondissement" in Paris, "Ward" in Tokyo)
- Includes both administrative and popular neighborhood names
- Philippines data includes Zamboanga City with actual barangays
- Sample addresses demonstrate the complete address hierarchy

### Extending the Data

To add more locations, follow this pattern:

```sql
-- Add Province
INSERT INTO Province (province_name, country_ID) 
SELECT 'Province Name', country_ID FROM Country WHERE country_code = 'XX';

-- Add City
INSERT INTO City (city_name, province_ID)
SELECT 'City Name', province_ID FROM Province WHERE province_name = 'Province Name';

-- Add Districts
INSERT INTO Barangay (barangay_name, city_ID)
SELECT barangay_name, city_ID FROM (
    SELECT 'District 1' AS barangay_name UNION ALL
    SELECT 'District 2' UNION ALL
    SELECT 'District 3'
) AS districts
CROSS JOIN City WHERE city_name = 'City Name';
```

### Statistics

- **Countries**: 50+
- **Provinces/States**: 80+
- **Cities**: 100+
- **Districts/Barangays**: 500+
- **Sample Addresses**: 5

---

**Created**: October 25, 2025
**Purpose**: Global location data for tour guide system
**Compatibility**: MySQL/MariaDB
