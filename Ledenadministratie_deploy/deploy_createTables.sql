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

CREATE TABLE IF NOT EXISTS users (
    id int NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS boekjaar (
    id INT NOT NULL AUTO_INCREMENT,
    jaar INT NOT NULL UNIQUE,
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

-- Voeg standaard admin-gebruiker toe (wachtwoord: password123, hash hieronder gegenereerd)
INSERT INTO users (username, password, role, description)
SELECT * FROM (SELECT
    'admin' AS username,
    '$2y$10$wH6QwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw' AS password,
    'admin' AS role,
    'hoofdadmin' AS description
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin'
);

-- Voeg het huidige jaar en vorige jaar toe aan boekjaar tabel
INSERT INTO boekjaar (jaar) 
SELECT YEAR(CURDATE()) AS jaar
WHERE NOT EXISTS (SELECT 1 FROM boekjaar WHERE jaar = YEAR(CURDATE()));

INSERT INTO boekjaar (jaar) 
SELECT YEAR(CURDATE()) - 1 AS jaar
WHERE NOT EXISTS (SELECT 1 FROM boekjaar WHERE jaar = YEAR(CURDATE()) - 1);
