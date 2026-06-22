<?php
/* ============================================================
   portfolio.php — Page Portfolio / Projets
   Portfolio Boubacar Nanky — Partie 2 PHP
   ============================================================ */
$page_active = 'portfolio';

/* Données des projets dans un tableau PHP */
$projets = [
    [
        'num'   => '01',
        'style' => 'project-card__img--1',
        'titre' => 'Site Vitrine RO-BOOK',
        'desc'  => 'Site responsive pour un restaurant local. Design chaleureux, menu interactif et présentation des plats.',
        'tags'  => ['HTML', 'CSS', 'Responsive'],
        'lien'  => 'Projets/Site Vitrine Restaurant.html',
    ],
    [
        'num'   => '02',
        'style' => 'project-card__img--2',
        'titre' => 'Gestionnaire de Tâches',
        'desc'  => 'Application CRUD complète avec authentification PHP/MySQL et gestion des priorités.',
        'tags'  => ['PHP', 'MySQL', 'CRUD'],
        'lien'  => 'Projets/Gestionnaire de Tâches.html',
    ],
    [
        'num'   => '03',
        'style' => 'project-card__img--3',
        'titre' => 'Landing Page Animée',
        'desc'  => 'Page d\'accueil avec animations CSS avancées et interactions fluides.',
        'tags'  => ['CSS', 'Animation', 'UI/UX'],
        'lien'  => 'Projets/Landing-de-page-animé/Baterrie.html',
    ],
    [
        'num'   => '04',
        'style' => 'project-card__img--4',
        'titre' => 'Projets Classe',
        'desc'  => 'Interface d\'administration claire et intuitive avec tableaux de bord en CSS Grid.',
        'tags'  => ['HTML', 'CSS', 'Grid'],
        'lien'  => 'Projets/Mes-projets-classes.html',
    ],
    [
        'num'   => '05',
        'style' => 'project-card__img--5',
        'titre' => 'Blog Personnel',
        'desc'  => 'Moteur de blog développé from scratch avec PHP et base de données MySQL.',
        'tags'  => ['PHP', 'MySQL', 'Blog'],
        'lien'  => 'Projets/Blog Personnel.html',
    ],
    [
        'num'   => '06',
        'style' => 'project-card__img--6',
        'titre' => 'Portfolio Robbie Lens',
        'desc'  => 'Portfolio réalisé dans le cadre du cours OpenClassrooms Les bases de HTML5 et CSS3.',
        'tags'  => ['HTML', 'CSS', 'Portfolio'],
        'lien'  => 'Projets/les bases de html5/index.html',
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Portfolio de projets de Boubacar Nanky — HTML, CSS, PHP, MySQL">
  <title>Portfolio — Boubacar Nanky</title>
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
          <span>Portfolio</span>
        </nav>
        <div class="section-tag">Réalisations</div>
        <h1 class="page-hero__title">Mes Projets</h1>
        <p class="page-hero__desc">Une sélection de projets réalisés durant ma formation en Génie Logiciel.</p>
      </div>
    </section>

    <section class="portfolio">
      <div class="container">
        <div class="portfolio__grid">

          <?php foreach ($projets as $projet) : ?>
          <div class="project-card">
            <div class="project-card__img <?php echo $projet['style']; ?>" data-num="<?php echo $projet['num']; ?>">
              <div class="project-card__overlay">
                <a href="<?php echo $projet['lien']; ?>" class="btn btn--sm btn--outline-white">Voir le projet</a>
              </div>
            </div>
            <div class="project-card__body">
              <h3 class="project-card__title"><?php echo $projet['titre']; ?></h3>
              <p class="project-card__desc"><?php echo $projet['desc']; ?></p>
              <div class="project-card__tags">
                <?php foreach ($projet['tags'] as $tag) : ?>
                  <span class="tag"><?php echo $tag; ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>
    </section>

  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
