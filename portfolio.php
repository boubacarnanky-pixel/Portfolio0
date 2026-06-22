<?php
/* ============================================================
   portfolio.php — Page Portfolio / Projets
   Portfolio Boubacar Nanky

   PARTIE 3 : les projets viennent maintenant de MySQL
   via PDO, avec une recherche LIKE sur titre/technologies.
   ============================================================ */
$page_active = 'portfolio';

require 'fonctions.php';
require 'config/connexion.php';

$pdo = get_pdo();

require 'composants/navigation.php'; // démarre la session
enregistrer_visite($pdo, 'portfolio');

/* ============================================================
   RECHERCHE — récupération du terme tapé par l'utilisateur
   ============================================================
   On utilise $_GET car la recherche doit pouvoir être partagée
   par URL (ex: portfolio.php?recherche=php) et fonctionner
   avec le bouton "Précédent" du navigateur. $_POST ne permet
   pas ça : on perdrait la recherche en rechargeant la page.
   ============================================================ */
$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';

/*
 * Limite de longueur pour éviter qu'un champ de recherche
 * trop long ne soit utilisé pour une attaque par déni de
 * service (DoS) basique sur la requête SQL.
 */
if (mb_strlen($recherche) > 100) {
    $recherche = mb_substr($recherche, 0, 100);
}

/* ============================================================
   REQUÊTE PDO — lecture des projets avec recherche optionnelle
   ============================================================ */
$projets = [];

try {
    if ($recherche !== '') {

        /*
         * Requête préparée avec LIKE.
         *
         * ⚠️ Point de sécurité important :
         * On NE concatène JAMAIS le texte de recherche
         * directement dans la chaîne SQL. À la place, on
         * construit le motif "%terme%" en PHP, puis on le
         * passe comme paramètre lié (:recherche).
         *
         * Mauvais (injection SQL possible) :
         *   "WHERE titre LIKE '%$recherche%'"
         *
         * Bon (sécurisé) :
         *   "WHERE titre LIKE :recherche"  + bindValue("%...%")
         */
        $motif = '%' . $recherche . '%';

        /*
         * ⚠️ CORRECTION IMPORTANTE — paramètres nommés dupliqués
         *
         * BUG initial : le même paramètre :motif était utilisé
         * deux fois dans la requête (titre LIKE :motif OR
         * technologies LIKE :motif).
         *
         * Avec PDO::ATTR_EMULATE_PREPARES = false (vraies requêtes
         * préparées MySQL natives), le pilote ne permet PAS de
         * réutiliser un même paramètre nommé plusieurs fois dans
         * une requête. bindValue() ne liait alors que la première
         * occurrence → la seconde restait vide → la recherche ne
         * retournait jamais aucun résultat.
         *
         * Avec l'émulation activée (comportement par défaut de
         * PDO, qu'on a désactivé pour la sécurité), ce genre de
         * réutilisation fonctionne silencieusement — c'est ce qui
         * rend ce bug particulièrement piégeux : le même code peut
         * marcher chez quelqu'un d'autre et pas chez nous.
         *
         * Solution : un nom de paramètre distinct par occurrence.
         */
        $sql = '
            SELECT id, titre, description, technologies, image, lien, date_creation
            FROM projets
            WHERE titre LIKE :motif_titre
               OR technologies LIKE :motif_tech
            ORDER BY date_creation DESC
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':motif_titre', $motif, PDO::PARAM_STR);
        $stmt->bindValue(':motif_tech',  $motif, PDO::PARAM_STR);
        $stmt->execute();

    } else {

        /* Pas de recherche → on affiche tous les projets */
        $sql = '
            SELECT id, titre, description, technologies, image, lien, date_creation
            FROM projets
            ORDER BY date_creation DESC
        ';
        $stmt = $pdo->query($sql);
    }

    $projets = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('[PORTFOLIO] Erreur lecture projets : ' . $e->getMessage());
    $projets = [];
}

/*
 * Fonction utilitaire : attribue une classe de couleur de fond
 * en fonction de l'id du projet, pour garder la même variété
 * visuelle qu'avant (project-card__img--1 à --6), même si
 * le nombre de projets en base change dynamiquement.
 */
function classe_couleur_projet(int $id): string {
    /* Modulo 6 : boucle entre 1 et 6 quel que soit l'id */
    $num = ($id % 6) + 1;
    return 'project-card__img--' . $num;
}
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
  <link rel="stylesheet" href="css/portfolio-extra.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body id="top">

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

    <!-- ============================================================
         BARRE DE RECHERCHE
         method="get" → la recherche apparaît dans l'URL
         ============================================================ -->
    <section class="portfolio-search">
      <div class="container">
        <form action="portfolio.php" method="get" class="search-form" role="search">
          <div class="search-form__field">
            <i class="fa fa-magnifying-glass search-form__icon"></i>
            <input
              type="text"
              name="recherche"
              placeholder="Rechercher un projet (titre ou technologie)..."
              value="<?php echo htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8'); ?>"
              maxlength="100"
            >
          </div>
          <button type="submit" class="btn btn--primary">Rechercher</button>
          <?php if ($recherche !== '') : ?>
            <a href="portfolio.php" class="btn btn--outline">Réinitialiser</a>
          <?php endif; ?>
        </form>

        <?php if ($recherche !== '') : ?>
          <p class="search-result-count">
            <?php
            $nb = count($projets);
            if ($nb === 0) {
                echo 'Aucun projet trouvé pour "' . htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') . '"';
            } elseif ($nb === 1) {
                echo '1 projet trouvé pour "' . htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') . '"';
            } else {
                echo $nb . ' projets trouvés pour "' . htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') . '"';
            }
            ?>
          </p>
        <?php endif; ?>
      </div>
    </section>

    <section class="portfolio">
      <div class="container">

        <?php if (empty($projets)) : ?>

          <!-- ════════════════════════════════════════════════
               ÉTAT VIDE — aucun projet en base, ou aucun résultat
               ════════════════════════════════════════════════ -->
          <div class="empty-state">
            <div class="empty-state__icon">
              <i class="fa fa-folder-open"></i>
            </div>
            <?php if ($recherche !== '') : ?>
              <h3>Aucun résultat</h3>
              <p>Aucun projet ne correspond à "<?php echo htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8'); ?>".</p>
              <a href="portfolio.php" class="btn btn--outline">Voir tous les projets</a>
            <?php else : ?>
              <h3>Aucun projet pour le moment</h3>
              <p>Les projets sont en cours d'ajout. Revenez bientôt !</p>
            <?php endif; ?>
          </div>

        <?php else : ?>

          <div class="portfolio__grid">

            <?php foreach ($projets as $projet) : ?>
            <div class="project-card">
              <div class="project-card__img <?php echo classe_couleur_projet((int) $projet['id']); ?>"
                   data-num="<?php echo str_pad((string) $projet['id'], 2, '0', STR_PAD_LEFT); ?>"
                   <?php if (!empty($projet['image'])) : ?>
                   style="background-image:url('<?php echo htmlspecialchars($projet['image'], ENT_QUOTES, 'UTF-8'); ?>');"
                   <?php endif; ?>
              >
                <div class="project-card__overlay">
                  <?php if (!empty($projet['lien'])) : ?>
                    <a href="<?php echo htmlspecialchars($projet['lien'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn--sm btn--outline-white">Voir le projet</a>
                  <?php endif; ?>
                </div>
              </div>
              <div class="project-card__body">
                <h3 class="project-card__title"><?php echo htmlspecialchars($projet['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="project-card__desc"><?php echo htmlspecialchars($projet['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="project-card__tags">
                  <?php
                  /* technologies est stocké en base comme "HTML, CSS, PHP"
                     → on l'éclate en tags individuels */
                  $tags = array_map('trim', explode(',', $projet['technologies']));
                  foreach ($tags as $tag) :
                      if ($tag === '') continue;
                  ?>
                    <span class="tag"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

          </div>

        <?php endif; ?>

      </div>
    </section>

  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
