<?php
/* ============================================================
   experience.php — Page Expérience
   Portfolio Boubacar Nanky — Partie 2 PHP
   ============================================================ */
$page_active = 'experience';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Parcours et expérience de Boubacar Nanky — Développeur Web">
  <title>Expérience — Boubacar Nanky</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/pages.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body id="top">

  <?php require 'composants/navigation.php'; ?>

  <main>

    <section class="page-hero">
      <div class="container page-hero__inner">
        <nav class="page-hero__breadcrumb" aria-label="Fil d'Ariane">
          <a href="index.php">Accueil</a>
          <span>›</span>
          <span>Expérience</span>
        </nav>
        <div class="section-tag">Parcours</div>
        <h1 class="page-hero__title">Mon Expérience</h1>
        <p class="page-hero__desc">De mes premiers pas en algorithmique à mes projets web actuels.</p>
      </div>
    </section>

    <section class="experience-section" id="experience">
      <div class="container">
        <div class="timeline">

          <div class="timeline__item timeline__item--left">
            <div class="timeline__card">
              <span class="timeline__date">2025 / 2026 — En cours</span>
              <h3 class="timeline__role">Étudiant en Développement Web — Licence 2</h3>
              <p class="timeline__org">Cours PHP &amp; MySQL — M. Diouf</p>
              <p class="timeline__desc">
                Apprentissage de PHP, MySQL, architecture MVC et création de ce portfolio.
                Développement de projets concrets : gestionnaire de tâches, blog personnel, site vitrine.
              </p>
            </div>
            <div class="timeline__dot"></div>
          </div>

          <div class="timeline__item timeline__item--right">
            <div class="timeline__card">
              <span class="timeline__date">2024 / 2025</span>
              <h3 class="timeline__role">Bases du Développement Web — Licence 1</h3>
              <p class="timeline__org">Auto-formation &amp; MOOCs OpenClassrooms</p>
              <p class="timeline__desc">
                Maîtrise de HTML5 et CSS3. Création des premiers projets statiques : portfolio photographe,
                pages responsives, et composants UI.
              </p>
            </div>
            <div class="timeline__dot"></div>
          </div>

          <div class="timeline__item timeline__item--left">
            <div class="timeline__card">
              <span class="timeline__date">2023 / 2024</span>
              <h3 class="timeline__role">Rencontre avec l'Informatique</h3>
              <p class="timeline__org">Lycée Seydina Issa Roho Lahi (Ex LPA) — Bac série S</p>
              <p class="timeline__desc">
                Premiers pas en algorithmique et logique de programmation au laboratoire du lycée.
                Développement de la curiosité pour les sciences du numérique.
              </p>
            </div>
            <div class="timeline__dot"></div>
          </div>

        </div>
      </div>
    </section>

    <!-- TÉMOIGNAGES -->
    <section class="testimonials" id="temoignages">
      <div class="container">
        <div class="section-header">
          <div class="section-tag">Témoignages</div>
          <h2 class="section-title">Ce qu'on dit de moi</h2>
        </div>

        <div class="testimonials__grid">

          <div class="testi-card">
            <div class="testi-card__quote">"</div>
            <p class="testi-card__text">
              Boubacar est rigoureux, curieux et toujours prêt à aller plus loin.
              Son portfolio reflète parfaitement son niveau et sa progression rapide.
            </p>
            <div class="testi-card__author">
              <div class="testi-card__avatar">MD</div>
              <div>
                <strong>M. Diouf</strong>
                <span>Professeur associé</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-card__quote">"</div>
            <p class="testi-card__text">
              Un camarade fiable et créatif. Les projets réalisés ensemble étaient
              toujours bien structurés et visuellement soignés.
            </p>
            <div class="testi-card__author">
              <div class="testi-card__avatar">MC</div>
              <div>
                <strong>Mariama Camara</strong>
                <span>Camarade de classe</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-card__quote">"</div>
            <p class="testi-card__text">
              Je lui ai confié la création d'une page pour mon association. Le
              rendu était professionnel et livré dans les délais.
            </p>
            <div class="testi-card__author">
              <div class="testi-card__avatar">OS</div>
              <div>
                <strong>Ousseynatou S.</strong>
                <span>Cliente — Association locale</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
