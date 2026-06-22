<?php
/* ============================================================
   index.php — Page d'accueil (Hero)
   Portfolio Boubacar Nanky — Partie 2 PHP
   ============================================================ */
$page_active = 'accueil';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Portfolio de Boubacar Nanky — Développeur Web, Administration Réseaux, Licence 2">
  <title>Boubacar Nanky — Portfolio</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body id="top">

  <?php require 'composants/navigation.php'; ?>

  <main>
    <section class="hero" id="accueil">
      <div class="container hero__inner">

        <!-- Texte -->
        <div class="hero__text">

          <p class="hero__hello">Bonjour, je suis disponible</p>

          <h1 class="hero__title">
            Je suis
            <span class="hero__name">Boubacar Nanky</span>
          </h1>

          <p class="hero__subtitle">
            Développeur Web<span class="cursor">|</span>
          </p>

          <p class="hero__desc">
            Je crée des expériences numériques élégantes et performantes.
            Passionné par le code propre et les interfaces qui donnent envie de rester.
            Actuellement en <strong>Licence 2</strong> de <strong>Génie Logiciel et Administration Réseaux</strong>.
          </p>

          <!-- Réseaux sociaux -->
          <div class="hero__meta">
            <div>
              <p class="hero__meta-label">Me trouver</p>
              <div class="hero__socials">
                <a href="https://www.instagram.com/benz_talent/#" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
                  <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="https://facebook.com" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
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

            <!-- Badges compétences -->
            <div>
              <p class="hero__meta-label">Meilleures compétences</p>
              <div class="hero__badges">
                <span class="skill-badge" title="HTML5"><i class="fa-brands fa-html5"></i></span>
                <span class="skill-badge" title="CSS3"><i class="fa-brands fa-css3-alt"></i></span>
                <span class="skill-badge" title="PHP"><i class="fa-brands fa-php"></i></span>
                <span class="skill-badge" title="JavaScript"><i class="fa-brands fa-js"></i></span>
                <span class="skill-badge" title="Sécurité"><i class="fa fa-shield-halved"></i></span>
              </div>
            </div>
          </div>

          <!-- Boutons CTA -->
          <div class="hero__cta">
            <a href="image/CV.pdf" class="btn" download>
              <i class="fa fa-download"></i> Télécharger mon CV
            </a>
            <a href="contact.php" class="btn btn--outline">Me contacter</a>
          </div>

        </div>

        <!-- Photo de profil -->
        <div class="hero__image-wrap">
          <div class="hero__image-frame">
            <img src="image/profil.jpeg" alt="Photo de profil de Boubacar Nanky">
          </div>
          <div class="hero__badge-float">
            <span class="badge-icon">🎓</span>
            <div class="badge-text">
              <strong>Licence 2</strong>
              <span>Génie Logiciel</span>
            </div>
          </div>
        </div>

      </div>
    </section>
  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
