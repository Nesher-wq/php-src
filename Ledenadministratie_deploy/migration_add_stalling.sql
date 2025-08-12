-- Migratie script om stalling kolom toe te voegen aan bestaande familielid tabel
-- Datum: 2025-08-12
-- Doel: Voeg stalling kolom toe voor bestaande installaties

USE `ledenadministratie`;

-- Controleer of stalling kolom al bestaat, zo niet, voeg deze toe
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'ledenadministratie' 
    AND TABLE_NAME = 'familielid' 
    AND COLUMN_NAME = 'stalling'
);

-- Voeg stalling kolom toe als deze nog niet bestaat
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE familielid ADD COLUMN stalling INT NOT NULL DEFAULT 0;',
    'SELECT "Stalling kolom bestaat al" AS message;'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Toon resultaat
SELECT 
    CASE 
        WHEN @column_exists = 0 THEN 'Stalling kolom succesvol toegevoegd aan familielid tabel'
        ELSE 'Stalling kolom bestond al in familielid tabel'
    END AS result;
