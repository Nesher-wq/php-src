-- Eenvoudige migratie om stalling kolom toe te voegen
-- Deze versie gebruikt ALTER TABLE IF NOT EXISTS syntax (MySQL 5.7+)

USE `ledenadministratie`;

-- Voeg stalling kolom toe (alleen als deze nog niet bestaat)
-- Voor oudere MySQL versies: voer deze query handmatig uit en negeer de error als de kolom al bestaat
ALTER TABLE familielid 
ADD COLUMN IF NOT EXISTS stalling INT NOT NULL DEFAULT 0;

-- Controleer of de kolom succesvol is toegevoegd
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'ledenadministratie' 
AND TABLE_NAME = 'familielid' 
AND COLUMN_NAME = 'stalling';
