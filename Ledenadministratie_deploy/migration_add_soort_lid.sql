-- Migration script to add soort_lid_id column to existing familielid table
USE `ledenadministratie`;

-- First, create the soortlid table if it doesn't exist (with fresh data)
DROP TABLE IF EXISTS soortlid;
CREATE TABLE soortlid (
    id INT NOT NULL AUTO_INCREMENT,
    omschrijving VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
);

-- Insert the standard values
INSERT INTO soortlid (omschrijving)
VALUES
('standaard lid'),
('student-lid'),
('erelid'),
('familielid');

-- Add the soort_lid_id column to familielid table if it doesn't exist
ALTER TABLE familielid 
ADD COLUMN IF NOT EXISTS soort_lid_id INT NOT NULL DEFAULT 1;

-- Add the foreign key constraint if it doesn't exist
-- First check if constraint exists, if not add it
SET @constraint_exists = (SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'ledenadministratie' 
    AND TABLE_NAME = 'familielid' 
    AND CONSTRAINT_NAME = 'familielid_ibfk_2');

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE familielid ADD CONSTRAINT familielid_ibfk_2 FOREIGN KEY (soort_lid_id) REFERENCES soortlid(id)', 
    'SELECT "Foreign key constraint already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
