-- ============================================================
-- seed_projets_test.sql
-- Script OPTIONNEL — données de test pour valider l'Étape 3
--
-- À exécuter dans phpMyAdmin → onglet SQL, UNIQUEMENT pour
-- vérifier que portfolio.php affiche bien les projets et que
-- la recherche LIKE fonctionne.
--
-- Tu pourras supprimer ces lignes plus tard une fois le CRUD
-- admin construit (Étape 6), ou les garder si elles te
-- conviennent.
-- ============================================================

USE portfolio;

INSERT INTO projets (titre, description, technologies, image, lien) VALUES
('Site Vitrine RO-BOOK',
 'Site responsive pour un restaurant local. Design chaleureux, menu interactif et présentation des plats.',
 'HTML, CSS, Responsive',
 NULL,
 'Projets/Site Vitrine Restaurant.html'),

('Gestionnaire de Tâches',
 'Application CRUD complète avec authentification PHP/MySQL et gestion des priorités.',
 'PHP, MySQL, CRUD',
 NULL,
 'Projets/Gestionnaire de Tâches.html'),

('Blog Personnel',
 'Moteur de blog développé from scratch avec PHP et base de données MySQL.',
 'PHP, MySQL, Blog',
 NULL,
 'Projets/Blog Personnel.html');

-- Vérification
SELECT id, titre, technologies, date_creation FROM projets;
