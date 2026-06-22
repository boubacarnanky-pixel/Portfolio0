<?php
/* ============================================================
   services.php — Page Services
   Portfolio Boubacar Nanky — Partie 2 PHP
   ============================================================ */
$page_active = 'services';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Services proposés par Boubacar Nanky — Développement Web, Réseaux, IoT">
  <title>Services — Boubacar Nanky</title>
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
          <span>Services</span>
        </nav>
        <div class="section-tag">Ce que je fais</div>
        <h1 class="page-hero__title">Mes Services</h1>
        <p class="page-hero__desc">
          Du développement web à l'administration réseau, je propose des solutions numériques adaptées à vos besoins.
        </p>
      </div>
    </section>

    <section class="services">
      <div class="container">
        <div class="services__grid">

          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <polyline points="16 18 22 12 16 6"/>
                <polyline points="8 6 2 12 8 18"/>
              </svg>
            </div>
            <h3 class="service-card__title">Développement Front-end</h3>
            <p class="service-card__desc">
              Création d'interfaces modernes, réactives et accessibles avec HTML, CSS et JavaScript.
              Design responsive et expériences utilisateur soignées.
            </p>
          </div>

          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
              </svg>
            </div>
            <h3 class="service-card__title">Développement Back-end</h3>
            <p class="service-card__desc">
              Architecture serveur robuste avec PHP et MySQL. Gestion de bases de données,
              API REST et logique applicative sécurisée.
            </p>
          </div>

          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="4" width="18" height="6" rx="1"/>
                <rect x="3" y="14" width="18" height="6" rx="1"/>
                <line x1="7" y1="7" x2="7.01" y2="7"/>
                <line x1="7" y1="17" x2="7.01" y2="17"/>
              </svg>
            </div>
            <h3 class="service-card__title">Administration Réseaux</h3>
            <p class="service-card__desc">
              Configuration et gestion des réseaux informatiques, maintenance des serveurs
              et sécurisation des infrastructures.
            </p>
          </div>

          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="4" y="4" width="16" height="16" rx="2"/>
                <circle cx="9" cy="9" r="1"/><circle cx="15" cy="9" r="1"/>
                <circle cx="9" cy="15" r="1"/><circle cx="15" cy="15" r="1"/>
              </svg>
            </div>
            <h3 class="service-card__title">IoT avec Arduino</h3>
            <p class="service-card__desc">
              Conception de projets connectés avec Arduino : capteurs, automatisation
              et systèmes intelligents embarqués.
            </p>
          </div>

          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="5" y="11" width="14" height="10" rx="2"/>
                <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                <circle cx="12" cy="16" r="1"/>
              </svg>
            </div>
            <h3 class="service-card__title">Sécurité &amp; Cryptographie</h3>
            <p class="service-card__desc">
              Protection des données, chiffrement des communications et mise en place
              de solutions de cybersécurité fiables.
            </p>
          </div>

          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
              </svg>
            </div>
            <h3 class="service-card__title">Base de Données</h3>
            <p class="service-card__desc">
              Conception et optimisation de schémas MySQL. Requêtes performantes,
              sécurisées et bien structurées.
            </p>
          </div>

        </div>
      </div>
    </section>

    <section class="cta-banner">
      <div class="container cta-banner__inner">
        <p class="cta-banner__tag">Visitez mon portfolio</p>
        <h2 class="cta-banner__title">Mes Projets</h2>
        <a href="portfolio.php" class="btn btn--outline-white">Voir tous les projets</a>
      </div>
    </section>

  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
