<?php
/* ============================================================
   competences.php — Page Compétences
   Portfolio Boubacar Nanky — Partie 2 PHP
   ============================================================ */
$page_active = 'competences';

/* Données des compétences */
$competences = [
    ['icone' => 'fa-brands fa-html5',    'couleur' => '#e34c26', 'nom' => 'HTML5',              'pct' => 90, 'classe' => '90'],
    ['icone' => 'fa-brands fa-css3-alt', 'couleur' => '#264de4', 'nom' => 'CSS3 / Flexbox / Grid', 'pct' => 85, 'classe' => '85'],
    ['icone' => 'fa-brands fa-git-alt',  'couleur' => '#f05032', 'nom' => 'Git / GitHub',       'pct' => 75, 'classe' => '75'],
    ['icone' => 'fa-brands fa-php',      'couleur' => '#777bb4', 'nom' => 'PHP',                'pct' => 70, 'classe' => '70'],
    ['icone' => 'fa fa-database',        'couleur' => '#4479a1', 'nom' => 'MySQL',              'pct' => 65, 'classe' => '65'],
    ['icone' => 'fa-brands fa-js',       'couleur' => '#f7df1e', 'nom' => 'JavaScript',         'pct' => 55, 'classe' => '55'],
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Compétences de Boubacar Nanky — HTML, CSS, PHP, MySQL, JavaScript">
  <title>Compétences — Boubacar Nanky</title>
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
          <span>Compétences</span>
        </nav>
        <div class="section-tag">Expertise</div>
        <h1 class="page-hero__title">Mes Compétences</h1>
        <p class="page-hero__desc">Un aperçu de mes niveaux de maîtrise techniques, acquis en formation et par la pratique.</p>
      </div>
    </section>

    <section class="skills-section" id="competences">
      <div class="container">
        <div class="skills-section__inner">

          <!-- Colonne gauche : stats -->
          <div class="skills-section__stats">
            <div class="section-header section-header--left">
              <div class="section-tag">Chiffres</div>
              <h2 class="section-title">En quelques chiffres</h2>
            </div>

            <div class="stats-grid">
              <div class="stat-item">
                <span class="stat-num">6+</span>
                <span class="stat-label">Projets réalisés</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">6</span>
                <span class="stat-label">Technologies</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">2</span>
                <span class="stat-label">Années d'étude</span>
              </div>
              <div class="stat-item">
                <span class="stat-num">100%</span>
                <span class="stat-label">Passion</span>
              </div>
            </div>
          </div>

          <!-- Colonne droite : barres de progression (CSS uniquement) -->
          <div class="skills-section__bars">
            <div class="section-header section-header--left">
              <div class="section-tag">Niveaux</div>
              <h2 class="section-title">Barres de progression</h2>
            </div>

            <?php foreach ($competences as $comp) : ?>
            <div class="skill-bar">
              <div class="skill-bar__header">
                <span class="skill-bar__name">
                  <i class="<?php echo $comp['icone']; ?>" style="color:<?php echo $comp['couleur']; ?>;"></i>
                  <?php echo $comp['nom']; ?>
                </span>
                <span class="skill-bar__pct"><?php echo $comp['pct']; ?>%</span>
              </div>
              <div class="skill-bar__track">
                <div class="skill-bar__fill skill-bar__fill--<?php echo $comp['classe']; ?>"></div>
              </div>
            </div>
            <?php endforeach; ?>

          </div>

        </div>
      </div>
    </section>

  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
