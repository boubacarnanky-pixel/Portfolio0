<?php
/* ============================================================
   composants/navigation.php
   Barre de navigation — réutilisable sur toutes les pages
   Usage : <?php require 'composants/navigation.php'; ?>
   Variable attendue : $page_active (ex: 'accueil', 'services'…)

   PARTIE 3 : session_start() ajouté ici.
   Toutes les pages publiques incluent ce fichier en premier
   via require → la session est automatiquement démarrée
   sur chaque page sans toucher aux fichiers individuels.
   ============================================================ */

/*
 * session_start() doit être appelé AVANT tout output HTML.
 * C'est pour ça qu'il est placé en tout début de ce fichier PHP,
 * avant la fermeture du bloc <?php et avant tout echo/HTML.
 *
 * session_status() === PHP_SESSION_NONE vérifie qu'une session
 * n'est pas déjà active — évite l'erreur si session_start()
 * est appelé deux fois (ex: dans une page admin qui inclut
 * aussi ce composant).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ── Fonction utilitaire : lien actif dans la navbar ───── */
function nav_active(string $page, string $cible): string {
    return ($page === $cible) ? ' class="active"' : '';
}
?>

<!-- Checkbox cachée — menu burger CSS uniquement, sans JavaScript -->
<input type="checkbox" id="menu-toggle" class="menu-toggle">

<header class="navbar">
  <div class="container navbar__inner">

    <!-- Logo -->
    <a href="index.php" class="logo-link">
      <span class="logo-circle">NB</span>
      <span class="logo-text">Nanky_B</span>
    </a>

    <!-- Burger label (mobile) -->
    <label for="menu-toggle" class="burger-label" aria-label="Ouvrir le menu">
      <span></span>
      <span></span>
      <span></span>
    </label>

    <!-- Navigation principale -->
    <nav class="nav-menu" aria-label="Navigation principale">
      <ul class="nav-links">
        <li><a href="index.php"<?php echo nav_active($page_active, 'accueil'); ?>>Accueil</a></li>
        <li><a href="services.php"<?php echo nav_active($page_active, 'services'); ?>>Services</a></li>
        <li><a href="portfolio.php"<?php echo nav_active($page_active, 'portfolio'); ?>>Portfolio</a></li>
        <li><a href="competences.php"<?php echo nav_active($page_active, 'competences'); ?>>Compétences</a></li>
        <li><a href="experience.php"<?php echo nav_active($page_active, 'experience'); ?>>Expérience</a></li>
        <li><a href="contact.php"<?php echo nav_active($page_active, 'contact'); ?>>Contact</a></li>
      </ul>
    </nav>

    <!-- Toggle Dark / Light Mode -->
    <div class="toggle-wrap">
      <input type="checkbox" id="theme-toggle" class="theme-toggle">
      <label for="theme-toggle" class="toggle-label" aria-label="Basculer le thème">
        <span class="toggle-icon toggle-icon--sun">☀</span>
        <span class="toggle-icon toggle-icon--moon">☽</span>
        <span class="toggle-thumb"></span>
      </label>
    </div>

  </div>
</header>
