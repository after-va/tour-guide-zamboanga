-- Add package recommendations table
CREATE TABLE IF NOT EXISTS Package_Recommendations (
    recommendation_ID INT AUTO_INCREMENT PRIMARY KEY,
    tourPackage_ID INT NOT NULL,
    is_recommended TINYINT(1) DEFAULT 1,
    recommendation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE
);

-- Add guide package adoption table to track which guides have adopted which recommended packages
CREATE TABLE IF NOT EXISTS Guide_Package_Adoption (
    adoption_ID INT AUTO_INCREMENT PRIMARY KEY,
    guide_ID INT NOT NULL,
    tourPackage_ID INT NOT NULL,
    adoption_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (guide_ID) REFERENCES Person(person_ID) ON DELETE CASCADE,
    FOREIGN KEY (tourPackage_ID) REFERENCES Tour_Package(tourPackage_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_guide_package (guide_ID, tourPackage_ID)
);

-- Add some indexes for performance
CREATE INDEX idx_package_recommendations ON Package_Recommendations(tourPackage_ID, is_recommended);
CREATE INDEX idx_guide_package_adoption ON Guide_Package_Adoption(guide_ID, is_active);