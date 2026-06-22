<?php
/* ============================================================
   composants/footer.php
   Pied de page — réutilisable sur toutes les pages
   Usage : <?php require 'composants/footer.php'; ?>
   ============================================================ */
?>

<footer class="footer">
  <div class="container">
    <div class="footer__inner">

      <!-- Marque -->
      <div class="footer__brand">
        <a href="index.php" class="logo-link">
          <span class="logo-circle">NB</span>
          <span class="logo-text">Nanky_B</span>
        </a>
        <p class="footer__brand-desc">
          Développeur Web passionné. Je transforme vos idées en expériences numériques mémorables.
        </p>
        <a href="image/CV.pdf" class="btn btn--sm btn--outline-white" download>↓ Télécharger mon CV</a>
      </div>

      <!-- Navigation -->
      <div class="footer__links">
        <h4>Navigation</h4>
        <ul>
          <li><a href="index.php">Accueil</a></li>
          <li><a href="services.php">Services</a></li>
          <li><a href="portfolio.php">Portfolio</a></li>
          <li><a href="competences.php">Compétences</a></li>
          <li><a href="experience.php">Expérience</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Projets récents -->
      <div class="footer__links">
        <h4>Projets récents</h4>
        <ul>
          <li><a href="Projets/Site Vitrine Restaurant.html">Site Vitrine Restaurant</a></li>
          <li><a href="Projets/Gestionnaire de Tâches.html">Gestionnaire de Tâches</a></li>
          <li><a href="Projets/Blog Personnel.html">Blog Personnel</a></li>
          <li><a href="Projets/Landing-de-page-animé/Baterrie.html">Landing Page Animée</a></li>
        </ul>
      </div>

      <!-- Réseaux sociaux -->
      <div class="footer__social">
        <h4>Me trouver</h4>
        <div class="footer__social-icons">
          <a href="https://www.instagram.com/benz_talent/#" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
            <i class="fa-brands fa-instagram"></i>
          </a>
          <a href="https://facebook.com/Boubacar Nanky" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
            <i class="fa-brands fa-facebook"></i>
          </a>
          <a href="https://github.com/boubacarnanky-pixel" target="_blank" rel="noopener" class="social-icon" aria-label="GitHub">
            <i class="fa-brands fa-github"></i>
          </a>
          <a href="https://linkedin.com/in/scanf-boubacar" target="_blank" rel="noopener" class="social-icon" aria-label="LinkedIn">
            <i class="fa-brands fa-linkedin"></i>
          </a>
        </div>
      </div>

    </div>
  </div>

  <div class="footer__bottom">
    <div class="container">
      <p>© <?php echo date('Y'); ?> Boubacar Nanky — Développé avec ❤️ en PHP &amp; CSS</p>
      <p class="footer__admin-link">
  <a href="admin/connexion.php">Espace Administration</a>
      </p>
      <p>Cours PHP &amp; MySQL — Professeur : M. Diouf</p>
    </div>
  </div>
</footer>

<!-- Bouton retour en haut -->
<a href="#top" class="scroll-top" aria-label="Retour en haut">↑</a>
