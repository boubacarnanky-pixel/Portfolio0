-- ============================================================
-- PORTFOLIO — Script de création de la base de données
-- Partie 3 : MySQL, PDO, Sessions et Administration
-- Professeur : M. Diouf
-- À exécuter dans phpMyAdmin sur la base "portfolio"
-- ============================================================

-- Sécurité : on s'assure de travailler dans la bonne base
USE portfolio;

-- ============================================================
-- TABLE 1 : projets
-- Stocke les projets affichés sur le portfolio public
-- ============================================================
CREATE TABLE IF NOT EXISTS projets (
    id            INT           NOT NULL AUTO_INCREMENT,
    titre         VARCHAR(150)  NOT NULL,
    description   TEXT          NOT NULL,
    technologies  VARCHAR(255)  NOT NULL,
    image         VARCHAR(255)  NULL DEFAULT NULL,
    lien          VARCHAR(255)  NULL DEFAULT NULL,
    date_creation DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 2 : messages_contact
-- Stocke les messages du formulaire de contact public
-- ============================================================
CREATE TABLE IF NOT EXISTS messages_contact (
    id          INT           NOT NULL AUTO_INCREMENT,
    nom         VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL,
    message     TEXT          NOT NULL,
    lu          TINYINT(1)    NOT NULL DEFAULT 0,
    date_envoi  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 3 : demandes_projet
-- Stocke les demandes du formulaire "Demande de projet"
-- ============================================================
CREATE TABLE IF NOT EXISTS demandes_projet (
    id           INT           NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL,
    type_projet  VARCHAR(100)  NOT NULL,
    description  TEXT          NOT NULL,
    budget       VARCHAR(50)   NULL DEFAULT NULL,
    lu           TINYINT(1)    NOT NULL DEFAULT 0,
    date_demande DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 4 : administrateurs
-- Comptes autorisés à accéder à l'espace d'administration
-- ⚠️ mot_de_passe toujours haché avec password_hash()
-- ============================================================
CREATE TABLE IF NOT EXISTS administrateurs (
    id            INT           NOT NULL AUTO_INCREMENT,
    prenom        VARCHAR(100)  NOT NULL,
    nom           VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL,
    mot_de_passe  VARCHAR(255)  NOT NULL,
    date_creation DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE 5 : visites
-- Journalise chaque visite sur les pages publiques
-- ============================================================
CREATE TABLE IF NOT EXISTS visites (
    id          INT           NOT NULL AUTO_INCREMENT,
    adresse_ip  VARCHAR(45)   NOT NULL,
    page        VARCHAR(255)  NOT NULL,
    date_visite DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES
-- Un compte administrateur par défaut pour toi
-- ⚠️ Remplace ce hash par le résultat de password_hash()
--    ou utilise setup_prof.php pour créer les comptes
-- ============================================================
-- (aucune donnée en dur — les comptes sont créés via setup_prof.php)

-- ============================================================
-- VÉRIFICATION — affiche les 5 tables créées
-- ============================================================
SHOW TABLES;
