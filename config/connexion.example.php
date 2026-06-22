<?php
// ============================================================
// FICHIER DE CONNEXION - MODÈLE À COMPLÉTER
// Copiez ce fichier en 'connexion.php' et remplissez vos identifiants
// NE JAMAIS committer connexion.php sur GitHub
// ============================================================

\System.Management.Automation.Internal.Host.InternalHost     = 'votre_host';         // Ex: localhost ou sql.infinityfree.com
\   = 'votre_nom_de_bdd';   // Ex: if0_12345678_portfolio
\ = 'votre_utilisateur';  // Ex: if0_12345678
\ = 'votre_mot_de_passe';
\  = 'utf8mb4';

try {
    \ = new PDO(
        "mysql:host=\System.Management.Automation.Internal.Host.InternalHost;dbname=\;charset=\",
        \,
        \
    );
    \->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \) {
    die('Erreur de connexion : ' . \->getMessage());
}
