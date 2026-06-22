<?php
/* ============================================================
   admin/auth.php — Garde d'accès à l'espace d'administration
   ============================================================

   RÔLE : ce fichier est inclus en haut de TOUTE page admin
   protégée (dashboard, CRUD projets, CRUD administrateurs,
   messages, demandes...). Il fait 3 choses :

     1. Démarre la session (si pas déjà active)
     2. Vérifie que l'utilisateur est connecté en tant qu'admin
     3. Si NON connecté → redirection immédiate vers connexion.php

   Usage dans une page admin :
     <?php
     require __DIR__ . '/auth.php';
     // ... à partir d'ici, on sait que l'admin est connecté
     ?>

   Pourquoi centraliser cette logique dans un seul fichier ?
   Si on devait copier-coller cette vérification dans chaque
   page admin (dashboard, projets/liste.php, projets/creer.php,
   utilisateurs/liste.php...), une seule page oubliée serait
   une faille de sécurité béante. Avec un seul fichier inclus
   partout, corriger ou renforcer la sécurité se fait UNE fois,
   pour TOUTES les pages.
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../fonctions.php';
require_once __DIR__ . '/../config/connexion.php';

/*
 * est_connecte() vérifie la présence de $_SESSION['admin_id'].
 * Si absent → l'utilisateur n'est pas authentifié → on le
 * redirige immédiatement vers la page de connexion.
 *
 * exit après header('Location: ...') est OBLIGATOIRE :
 * sans lui, le reste du script continuerait à s'exécuter
 * même après l'instruction de redirection, ce qui pourrait
 * exposer du contenu protégé avant que le navigateur n'ait
 * traité la redirection.
 */
if (!est_connecte()) {
    header('Location: connexion.php');
    exit;
}

/*
 * À ce stade, on sait que l'admin est connecté.
 * On expose une connexion PDO prête à l'emploi pour
 * que chaque page admin n'ait pas à la recréer.
 */
$pdo = get_pdo();

/* ============================================================
   CORRECTION NAVIGATION — chemins absolus calculés dynamiquement
   ============================================================

   PROBLÈME RÉSOLU : les liens du menu admin (admin-nav.php)
   étaient écrits en chemins RELATIFS (ex: "demandes/liste.php").
   Un navigateur résout un lien relatif par rapport au DOSSIER
   DE LA PAGE ACTUELLE, pas par rapport à une racine "admin/"
   logique. Résultat : depuis admin/messages/liste.php, le lien
   "demandes/liste.php" était interprété comme
   admin/messages/demandes/liste.php → 404.

   SOLUTION : on calcule ICI, une seule fois, le chemin absolu
   (depuis la racine du site web) du dossier admin/, à partir
   de $_SERVER['SCRIPT_NAME']. Ce chemin absolu fonctionne
   IDENTIQUEMENT depuis n'importe quelle page admin, quelle
   que soit sa profondeur dans l'arborescence de dossiers.

   Exemple de résultat obtenu :
     SCRIPT_NAME = /PHP/portfolio_php/admin/messages/liste.php
     ADMIN_BASE_URL = /PHP/portfolio_php/admin

   Comme ce calcul se base sur l'URL réelle de la requête (et
   non une valeur codée en dur), il continue de fonctionner
   automatiquement si le projet est déplacé dans un autre
   sous-dossier (ex: chez ton professeur, en production...).
   ============================================================ */
if (!defined('ADMIN_BASE_URL')) {
    /*
     * $_SERVER['SCRIPT_NAME'] donne le chemin de la page
     * actuellement exécutée, ex: /PHP/portfolio_php/admin/messages/liste.php
     *
     * strstr(..., '/admin') coupe la chaîne pour ne garder
     * que la partie à partir de "/admin" (inclus), peu importe
     * la profondeur du sous-dossier courant (messages/, projets/...).
     */
    $script   = $_SERVER['SCRIPT_NAME'];
    $position = strpos($script, '/admin');

    define('ADMIN_BASE_URL', $position !== false ? substr($script, 0, $position) . '/admin' : '/admin');
}

/*
 * PUBLIC_BASE_URL : même logique, mais pour remonter vers les
 * pages publiques du portfolio (index.php, contact.php...)
 * depuis n'importe quelle profondeur de l'espace admin.
 */
if (!defined('PUBLIC_BASE_URL')) {
    define('PUBLIC_BASE_URL', dirname(ADMIN_BASE_URL));
}
