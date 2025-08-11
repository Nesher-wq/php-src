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

CREATE TABLE IF NOT EXISTS familielid (
    id INT NOT NULL AUTO_INCREMENT,
    familie_id INT NOT NULL,
    naam VARCHAR(100) NOT NULL,
    geboortedatum DATE,
    omschrijving VARCHAR(100),
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

-- Voeg standaard admin-gebruiker toe (wachtwoord: password123, hash hieronder gegenereerd)
INSERT INTO users (username, password, role, description)
SELECT * FROM (SELECT
    'admin' AS username,
    '$2y$10$wH6QwQwQwQwQwQwQwQwQwOQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw' AS password,
    'admin' AS role,
    'hoofdadmin' AS description
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin'
);