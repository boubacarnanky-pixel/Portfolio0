<?php
/* ============================================================
   admin/dashboard.php — Tableau de bord administrateur
   ============================================================ */

require __DIR__ . '/auth.php';
/* À partir d'ici : $pdo est disponible, l'admin est connecté. */
$admin_page_active = 'dashboard';

/* ============================================================
   STATISTIQUES — comptages depuis les 5 tables
   ============================================================ */
try {
    $nb_projets   = (int) $pdo->query('SELECT COUNT(*) AS total FROM projets')->fetch()['total'];
    $nb_messages  = (int) $pdo->query('SELECT COUNT(*) AS total FROM messages_contact')->fetch()['total'];
    $nb_non_lus_msg = (int) $pdo->query('SELECT COUNT(*) AS total FROM messages_contact WHERE lu = 0')->fetch()['total'];
    $nb_demandes  = (int) $pdo->query('SELECT COUNT(*) AS total FROM demandes_projet')->fetch()['total'];
    $nb_non_lus_dem = (int) $pdo->query('SELECT COUNT(*) AS total FROM demandes_projet WHERE lu = 0')->fetch()['total'];
    $nb_admins    = (int) $pdo->query('SELECT COUNT(*) AS total FROM administrateurs')->fetch()['total'];
    $nb_visites   = (int) $pdo->query('SELECT COUNT(*) AS total FROM visites')->fetch()['total'];

    /* Visites des 7 derniers jours, par page */
    $sql_visites_recentes = '
        SELECT page, COUNT(*) AS total
        FROM visites
        WHERE date_visite >= (NOW() - INTERVAL 7 DAY)
        GROUP BY page
        ORDER BY total DESC
    ';
    $visites_par_page = $pdo->query($sql_visites_recentes)->fetchAll();

    /* Les 5 derniers messages reçus */
    $derniers_messages = $pdo->query('
        SELECT id, nom, email, message, lu, date_envoi
        FROM messages_contact
        ORDER BY date_envoi DESC
        LIMIT 5
    ')->fetchAll();

} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur dashboard : ' . $e->getMessage());
    $nb_projets = $nb_messages = $nb_non_lus_msg = $nb_demandes = $nb_non_lus_dem = $nb_admins = $nb_visites = 0;
    $visites_par_page  = [];
    $derniers_messages = [];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Dashboard — Administration</title>
  <link rel="stylesheet" href="../css/global.css">
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="admin-body">

  <?php require __DIR__ . '/composants/admin-nav.php'; ?>

  <main class="admin-main">
    <div class="admin-container">

      <div class="admin-page-header">
        <div>
          <h1>Tableau de bord</h1>
          <p>Bonjour <?php echo htmlspecialchars($_SESSION['admin_prenom'], ENT_QUOTES, 'UTF-8'); ?>, voici un aperçu de votre portfolio.</p>
        </div>
      </div>

      <!-- ════════ CARTES STATISTIQUES ════════ -->
      <div class="stats-grid">

        <a href="projets/liste.php" class="stat-card">
          <div class="stat-card__icon stat-card__icon--blue"><i class="fa fa-diagram-project"></i></div>
          <div class="stat-card__value"><?php echo $nb_projets; ?></div>
          <div class="stat-card__label">Projets publiés</div>
        </a>

        <a href="messages/liste.php" class="stat-card">
          <div class="stat-card__icon stat-card__icon--red"><i class="fa fa-envelope"></i></div>
          <div class="stat-card__value">
            <?php echo $nb_messages; ?>
            <?php if ($nb_non_lus_msg > 0) : ?>
              <span class="stat-card__badge"><?php echo $nb_non_lus_msg; ?> non lu<?php echo $nb_non_lus_msg > 1 ? 's' : ''; ?></span>
            <?php endif; ?>
          </div>
          <div class="stat-card__label">Messages de contact</div>
        </a>

        <a href="demandes/liste.php" class="stat-card">
          <div class="stat-card__icon stat-card__icon--orange"><i class="fa fa-rocket"></i></div>
          <div class="stat-card__value">
            <?php echo $nb_demandes; ?>
            <?php if ($nb_non_lus_dem > 0) : ?>
              <span class="stat-card__badge"><?php echo $nb_non_lus_dem; ?> non lue<?php echo $nb_non_lus_dem > 1 ? 's' : ''; ?></span>
            <?php endif; ?>
          </div>
          <div class="stat-card__label">Demandes de projet</div>
        </a>

        <a href="utilisateurs/liste.php" class="stat-card">
          <div class="stat-card__icon stat-card__icon--green"><i class="fa fa-user-shield"></i></div>
          <div class="stat-card__value"><?php echo $nb_admins; ?></div>
          <div class="stat-card__label">Administrateurs</div>
        </a>

        <div class="stat-card stat-card--static">
          <div class="stat-card__icon stat-card__icon--purple"><i class="fa fa-eye"></i></div>
          <div class="stat-card__value"><?php echo $nb_visites; ?></div>
          <div class="stat-card__label">Visites totales</div>
        </div>

      </div>

      <div class="admin-grid-2">

        <!-- ════════ VISITES PAR PAGE (7 derniers jours) ════════ -->
        <div class="admin-panel">
          <h2 class="admin-panel__title"><i class="fa fa-chart-bar"></i> Visites — 7 derniers jours</h2>

          <?php if (empty($visites_par_page)) : ?>
            <p class="admin-empty">Aucune visite enregistrée sur cette période.</p>
          <?php else : ?>
            <?php
            $max = max(array_column($visites_par_page, 'total'));
            ?>
            <div class="visites-bars">
              <?php foreach ($visites_par_page as $ligne) : ?>
                <?php $pct = $max > 0 ? round(($ligne['total'] / $max) * 100) : 0; ?>
                <div class="visites-bar-row">
                  <span class="visites-bar-label"><?php echo htmlspecialchars($ligne['page'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <div class="visites-bar-track">
                    <div class="visites-bar-fill" style="width:<?php echo $pct; ?>%"></div>
                  </div>
                  <span class="visites-bar-value"><?php echo $ligne['total']; ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- ════════ DERNIERS MESSAGES ════════ -->
        <div class="admin-panel">
          <h2 class="admin-panel__title"><i class="fa fa-envelope-open-text"></i> Derniers messages</h2>

          <?php if (empty($derniers_messages)) : ?>
            <p class="admin-empty">Aucun message reçu pour le moment.</p>
          <?php else : ?>
            <ul class="admin-list">
              <?php foreach ($derniers_messages as $msg) : ?>
                <li class="admin-list__item <?php echo !$msg['lu'] ? 'admin-list__item--unread' : ''; ?>">
                  <div class="admin-list__main">
                    <strong><?php echo htmlspecialchars($msg['nom'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span class="admin-list__date"><?php echo date('d/m/Y H:i', strtotime($msg['date_envoi'])); ?></span>
                  </div>
                  <p class="admin-list__excerpt">
                    <?php echo htmlspecialchars(mb_substr($msg['message'], 0, 80), ENT_QUOTES, 'UTF-8'); ?>…
                  </p>
                </li>
              <?php endforeach; ?>
            </ul>
            <a href="messages/liste.php" class="admin-link-more">Voir tous les messages →</a>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </main>

</body>
</html>
