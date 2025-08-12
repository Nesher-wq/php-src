-- Simple migration script to add soort_lid_id column
USE ledenadministratie;

-- First, ensure soortlid table exists with data
DROP TABLE IF EXISTS soortlid;
CREATE TABLE soortlid (
    id INT NOT NULL AUTO_INCREMENT,
    omschrijving VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO soortlid (omschrijving) VALUES
('standaard lid'),
('student-lid'),
('erelid'),
('familielid');

-- Add the soort_lid_id column to familielid table
ALTER TABLE familielid ADD COLUMN soort_lid_id INT NOT NULL DEFAULT 1;

-- Add the foreign key constraint
ALTER TABLE familielid ADD CONSTRAINT fk_familielid_soortlid 
FOREIGN KEY (soort_lid_id) REFERENCES soortlid(id);
