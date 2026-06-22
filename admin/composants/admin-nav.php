<?php
/* ============================================================
   admin/composants/admin-nav.php
   Barre de navigation latérale — espace admin
   Usage : <?php require __DIR__ . '/composants/admin-nav.php'; ?>
   (appelé APRÈS auth.php, donc ADMIN_BASE_URL est déjà défini)

   Variable optionnelle : $admin_page_active (ex: 'dashboard',
   'projets', 'utilisateurs', 'messages', 'demandes')

   ⚠️ CORRECTION NAVIGATION : tous les liens utilisent désormais
   ADMIN_BASE_URL (chemin absolu calculé dans auth.php) au lieu
   de chemins relatifs. Un lien relatif comme "demandes/liste.php"
   se résout par rapport au DOSSIER DE LA PAGE COURANTE — ce qui
   cassait la navigation dès qu'on cliquait d'un sous-dossier
   (ex: admin/messages/) vers un autre (admin/demandes/).
   Avec un chemin absolu, le lien fonctionne IDENTIQUEMENT depuis
   n'importe quelle page admin, à n'importe quelle profondeur.
   ============================================================ */

$admin_page_active = $admin_page_active ?? '';

function admin_nav_active(string $page, string $cible): string {
    return ($page === $cible) ? ' admin-nav__link--active' : '';
}
?>
<!-- Checkbox cachée — menu mobile en CSS pur, sans JavaScript.
     Placée AVANT le header pour que le sélecteur CSS
     ":checked ~ .admin-topbar" puisse cibler la nav à l'intérieur. -->
<input type="checkbox" id="admin-menu-toggle" class="admin-menu-toggle">

<header class="admin-topbar">
  <div class="admin-topbar__inner">

    <a href="<?php echo ADMIN_BASE_URL; ?>/dashboard.php" class="admin-topbar__logo">
      <span class="logo-circle">NB</span>
      <span class="logo-text">Nanky_B <small>Admin</small></span>
    </a>

    <label for="admin-menu-toggle" class="admin-burger-label" aria-label="Ouvrir le menu">
      <span></span><span></span><span></span>
    </label>

    <nav class="admin-nav">
      <a href="<?php echo ADMIN_BASE_URL; ?>/dashboard.php" class="admin-nav__link<?php echo admin_nav_active($admin_page_active, 'dashboard'); ?>">
        <i class="fa fa-gauge"></i> Dashboard
      </a>
      <a href="<?php echo ADMIN_BASE_URL; ?>/projets/liste.php" class="admin-nav__link<?php echo admin_nav_active($admin_page_active, 'projets'); ?>">
        <i class="fa fa-diagram-project"></i> Projets
      </a>
      <a href="<?php echo ADMIN_BASE_URL; ?>/messages/liste.php" class="admin-nav__link<?php echo admin_nav_active($admin_page_active, 'messages'); ?>">
        <i class="fa fa-envelope"></i> Messages
      </a>
      <a href="<?php echo ADMIN_BASE_URL; ?>/demandes/liste.php" class="admin-nav__link<?php echo admin_nav_active($admin_page_active, 'demandes'); ?>">
        <i class="fa fa-rocket"></i> Demandes
      </a>
      <a href="<?php echo ADMIN_BASE_URL; ?>/utilisateurs/liste.php" class="admin-nav__link<?php echo admin_nav_active($admin_page_active, 'utilisateurs'); ?>">
        <i class="fa fa-user-shield"></i> Administrateurs
      </a>
    </nav>

    <div class="admin-topbar__user">
      <span class="admin-topbar__username">
        <?php echo htmlspecialchars($_SESSION['admin_prenom'] . ' ' . $_SESSION['admin_nom'], ENT_QUOTES, 'UTF-8'); ?>
      </span>
      <a href="<?php echo ADMIN_BASE_URL; ?>/deconnexion.php" class="admin-topbar__logout" title="Se déconnecter">
        <i class="fa fa-right-from-bracket"></i>
      </a>
    </div>

  </div>
</header>


