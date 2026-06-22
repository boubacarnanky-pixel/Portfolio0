<?php
/* ============================================================
   admin/deconnexion.php — Déconnexion de l'espace admin
   ============================================================

   On ne se contente pas de unset($_SESSION['admin_id']) :
   on détruit complètement la session pour ne laisser AUCUNE
   trace exploitable (bonnes pratiques OWASP).
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 1. On vide toutes les variables de session */
$_SESSION = [];

/* 2. On supprime le cookie de session côté navigateur */
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/* 3. On détruit la session côté serveur */
session_destroy();

/* 4. Redirection vers la page de connexion */
header('Location: connexion.php');
exit;
