<?php
/* ============================================================
   admin/projets/liste.php — Liste des projets (CRUD : Read)
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'projets';

/* Message flash après une action (créé / modifié / supprimé) */
$message_succes = '';
if (isset($_SESSION['flash_succes'])) {
    $message_succes = $_SESSION['flash_succes'];
    unset($_SESSION['flash_succes']);
}

try {
    $projets = $pdo->query('
        SELECT id, titre, technologies, image, date_creation
        FROM projets
        ORDER BY date_creation DESC
    ')->fetchAll();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur liste projets : ' . $e->getMessage());
    $projets = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Gestion des projets — Administration</title>
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
          <h1>Gestion des projets</h1>
          <p><?php echo count($projets); ?> projet<?php echo count($projets) > 1 ? 's' : ''; ?> publié<?php echo count($projets) > 1 ? 's' : ''; ?></p>
        </div>
        <a href="creer.php" class="btn btn--primary">
          <i class="fa fa-plus"></i> Nouveau projet
        </a>
      </div>

      <?php if ($message_succes !== '') : ?>
        <div class="alert alert--success">
          <i class="fa fa-circle-check"></i>
          <div><strong><?php echo htmlspecialchars($message_succes, ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>
      <?php endif; ?>

      <?php if (empty($projets)) : ?>

        <div class="admin-panel admin-empty-block">
          <i class="fa fa-folder-open"></i>
          <h3>Aucun projet pour le moment</h3>
          <p>Commence par ajouter ton premier projet.</p>
          <a href="creer.php" class="btn btn--primary">Ajouter un projet</a>
        </div>

      <?php else : ?>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Technologies</th>
                <th>Date</th>
                <th class="admin-table__actions-col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($projets as $projet) : ?>
              <tr>
                <td>
                  <?php if (!empty($projet['image'])) : ?>
                    <img src="../../<?php echo htmlspecialchars($projet['image'], ENT_QUOTES, 'UTF-8'); ?>"
                         alt="" class="admin-table__thumb">
                  <?php else : ?>
                    <div class="admin-table__thumb admin-table__thumb--empty">
                      <i class="fa fa-image"></i>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?php echo htmlspecialchars($projet['titre'], ENT_QUOTES, 'UTF-8'); ?></strong>
                </td>
                <td data-label="Technologies">
                  <span class="admin-table__muted"><?php echo htmlspecialchars($projet['technologies'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td data-label="Date">
                  <span class="admin-table__muted"><?php echo date('d/m/Y', strtotime($projet['date_creation'])); ?></span>
                </td>
                <td>
                  <div class="admin-table__actions">
                    <a href="modifier.php?id=<?php echo (int) $projet['id']; ?>"
                       class="admin-action-btn admin-action-btn--edit" title="Modifier">
                      <i class="fa fa-pen"></i>
                    </a>
                    <a href="supprimer.php?id=<?php echo (int) $projet['id']; ?>"
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
