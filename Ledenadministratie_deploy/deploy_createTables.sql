CREATE DATABASE IF NOT EXISTS `ledenadministratie`;
USE `ledenadministratie`;

CREATE TABLE IF NOT EXISTS familie (
    id INT NOT NULL AUTO_INCREMENT,
    naam VARCHAR(100) NOT NULL,
    straat VARCHAR(100) NOT NULL,
    huisnummer VARCHAR(10) NOT NULL,
    postcode VARCHAR(20) NOT NULL,
    woonplaats VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS soortlid;
CREATE TABLE soortlid (
    id INT NOT NULL AUTO_INCREMENT,
    omschrijving VARCHAR(100) NOT NULL,
    minimum_leeftijd INT NOT NULL,
    maximum_leeftijd INT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS familielid (
    id INT NOT NULL AUTO_INCREMENT,
    familie_id INT NOT NULL,
    naam VARCHAR(100) NOT NULL,
    geboortedatum DATE NOT NULL,
    soort_familielid VARCHAR(100),
    soort_lid_id INT NOT NULL DEFAULT 1,
    stalling INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (familie_id) REFERENCES familie(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    first_login BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id)
);

-- Hermaak boekjaar tabel met nieuwe structuur
DROP TABLE IF EXISTS boekjaar;
CREATE TABLE boekjaar (
    id INT NOT NULL AUTO_INCREMENT,
    jaar INT NOT NULL UNIQUE,
    basiscontributie DECIMAL(10,2) NOT NULL DEFAULT 100.00,
    stallingskosten DECIMAL(10,2) NOT NULL DEFAULT 50.00,
    PRIMARY KEY (id)
);

-- Voeg standaard soorten leden toe met leeftijdsbereiken
INSERT INTO soortlid (omschrijving, minimum_leeftijd, maximum_leeftijd)
VALUES
('Jeugd', 0, 7),
('Aspirant', 8, 12),
('Junior', 13, 17),
('Senior', 18, 50),
('Oudere', 51, NULL)
ON DUPLICATE KEY UPDATE 
    omschrijving = VALUES(omschrijving),
    minimum_leeftijd = VALUES(minimum_leeftijd),
    maximum_leeftijd = VALUES(maximum_leeftijd);

-- Voeg standaard gebruikers toe met plain text wachtwoorden voor eerste login
INSERT INTO users (username, password, role, description, first_login)
VALUES 
('admin', 'password123', 'admin', 'hoofdadmin', TRUE),
('kees_penningmeester', 'kees123', 'treasurer', 'Penningmeester', TRUE),
('jan_secretaris', 'jan123', 'secretary', 'Secretaris', TRUE)
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    first_login = VALUES(first_login);

-- Voeg het huidige jaar en vorige jaar toe aan boekjaar tabel met specifieke tarieven
INSERT INTO boekjaar (jaar, basiscontributie, stallingskosten) 
VALUES (2025, 100.00, 50.00)
ON DUPLICATE KEY UPDATE 
    basiscontributie = VALUES(basiscontributie),
    stallingskosten = VALUES(stallingskosten);

INSERT INTO boekjaar (jaar, basiscontributie, stallingskosten) 
VALUES (2024, 90.00, 50.00)
ON DUPLICATE KEY UPDATE 
    basiscontributie = VALUES(basiscontributie),
    stallingskosten = VALUES(stallingskosten);