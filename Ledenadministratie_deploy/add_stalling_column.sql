-- Universele migratie om stalling kolom toe te voegen
-- Deze versie werkt voor zowel nieuwe als bestaande installaties

USE ledenadministratie;

-- Methode 1: Voor MySQL 5.7+ met IF NOT EXISTS ondersteuning
-- ALTER TABLE familielid ADD COLUMN IF NOT EXISTS stalling INT NOT NULL DEFAULT 0;

-- Methode 2: Voor oudere MySQL versies - voer uit en negeer error als kolom al bestaat
-- Kopieer en plak deze regel in je MySQL console:
ALTER TABLE familielid ADD COLUMN stalling INT NOT NULL DEFAULT 0;

-- Als je een error krijgt "Duplicate column name 'stalling'" dan bestaat de kolom al en kun je doorgaan.

-- Verificatie: Controleer of de kolom bestaat
SELECT 
    COLUMN_NAME as Kolom_Naam, 
    DATA_TYPE as Data_Type, 
    IS_NULLABLE as Nullable, 
    COLUMN_DEFAULT as Default_Waarde
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'ledenadministratie' 
AND TABLE_NAME = 'familielid' 
AND COLUMN_NAME = 'stalling';

-- Als de query hierboven een rij teruggeeft, dan bestaat de stalling kolom
-- Als er geen rij wordt getoond, dan moet je de ALTER TABLE statement hierboven uitvoeren
