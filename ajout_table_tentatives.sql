-- ============================================================
-- ajout_table_tentatives.sql
-- Table additionnelle pour le délai progressif anti-brute-force
-- À exécuter dans phpMyAdmin, onglet SQL, sur la base "portfolio"
-- ============================================================

USE portfolio;

-- ============================================================
-- TABLE : tentatives_connexion
-- Journalise chaque tentative de connexion échouée par IP,
-- pour calculer un délai d'attente progressif.
-- ============================================================
CREATE TABLE IF NOT EXISTS tentatives_connexion (
    id            INT           NOT NULL AUTO_INCREMENT,
    adresse_ip    VARCHAR(45)   NOT NULL,
    date_tentative DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ip (adresse_ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SHOW TABLES;
