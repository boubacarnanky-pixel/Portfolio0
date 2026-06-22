-- ============================================================
-- correctif_double_encodage.sql
-- À exécuter UNE FOIS dans phpMyAdmin pour réparer les
-- descriptions déjà corrompues par le bug de double encodage.
-- ============================================================
USE portfolio;

UPDATE projets
SET description = REPLACE(description, '&amp;#039;', '''');

SELECT id, titre, description FROM projets;
