<?php
/* ============================================================
   composants/navigation.php
   Barre de navigation — réutilisable sur toutes les pages
   Usage : <?php require 'composants/navigation.php'; ?>
   Variable attendue : $page_active (ex: 'accueil', 'services'…)
   ============================================================ */

// Fonction pour générer la classe active sur le lien courant
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
