<?php
/* ============================================================
   admin/demandes/liste.php — Liste des demandes de projet
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'demandes';

require_once __DIR__ . '/../../fonctions.php'; // libelle_type_projet(), libelle_budget()

$message_succes = '';
if (isset($_SESSION['flash_succes'])) {
    $message_succes = $_SESSION['flash_succes'];
    unset($_SESSION['flash_succes']);
}

try {
    $demandes = $pdo->query('
        SELECT id, nom, email, type_projet, description, budget, lu, date_demande
        FROM demandes_projet
        ORDER BY date_demande DESC
    ')->fetchAll();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur liste demandes : ' . $e->getMessage());
    $demandes = [];
}

$nb_non_lues = count(array_filter($demandes, fn($d) => !$d['lu']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Demandes de projet — Administration</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="admin-body">

  <?php require __DIR__ . '/../composants/admin-nav.php'; ?>

  <main class="admin-main">
    <div class="admin-container">

      <div class="admin-page-header">
        <div>
          <h1>Demandes de projet</h1>
          <p>
            <?php echo count($demandes); ?> demande<?php echo count($demandes) > 1 ? 's' : ''; ?>
            <?php if ($nb_non_lues > 0) : ?>
              — <strong style="color: var(--clr-primary);"><?php echo $nb_non_lues; ?> non lue<?php echo $nb_non_lues > 1 ? 's' : ''; ?></strong>
            <?php endif; ?>
          </p>
        </div>
      </div>

      <?php if ($message_succes !== '') : ?>
        <div class="alert alert--success">
          <i class="fa fa-circle-check"></i>
          <div><strong><?php echo htmlspecialchars($message_succes, ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>
      <?php endif; ?>

      <?php if (empty($demandes)) : ?>

        <div class="admin-panel admin-empty-block">
          <i class="fa fa-rocket"></i>
          <h3>Aucune demande pour le moment</h3>
          <p>Les demandes envoyées depuis la page Contact apparaîtront ici.</p>
        </div>

      <?php else : ?>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th></th>
                <th>De</th>
                <th>Type de projet</th>
                <th>Budget</th>
                <th>Date</th>
                <th class="admin-table__actions-col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($demandes as $dem) : ?>
              <tr class="<?php echo !$dem['lu'] ? 'admin-row--unread' : ''; ?>">
                <td>
                  <?php if (!$dem['lu']) : ?>
                    <span class="admin-dot-unread" title="Non lue"></span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?php echo htmlspecialchars($dem['nom'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                  <span class="admin-table__muted"><?php echo htmlspecialchars($dem['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td data-label="Type"><span class="tag"><?php echo htmlspecialchars(libelle_type_projet($dem['type_projet']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td data-label="Budget">
                  <span class="admin-table__muted">
                    <?php echo $dem['budget'] ? htmlspecialchars(libelle_budget($dem['budget']), ENT_QUOTES, 'UTF-8') : 'Non spécifié'; ?>
                  </span>
                </td>
                <td data-label="Date"><span class="admin-table__muted"><?php echo date('d/m/Y H:i', strtotime($dem['date_demande'])); ?></span></td>
                <td>
                  <div class="admin-table__actions">
                    <a href="voir.php?id=<?php echo (int) $dem['id']; ?>"
                       class="admin-action-btn admin-action-btn--view" title="Voir la demande">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="supprimer.php?id=<?php echo (int) $dem['id']; ?>"
                       class="admin-action-btn admin-action-btn--delete" title="Supprimer">
                      <i class="fa fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      <?php endif; ?>

    </div>
  </main>

</body>
</html>
