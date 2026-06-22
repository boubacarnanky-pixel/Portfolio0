<?php
/* ============================================================
   admin/demandes/voir.php — Détail d'une demande de projet
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'demandes';

require_once __DIR__ . '/../../fonctions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT id, nom, email, type_projet, description, budget, lu, date_demande
        FROM demandes_projet
        WHERE id = :id
    ');
    $stmt->execute([':id' => $id]);
    $dem = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement demande : ' . $e->getMessage());
    $dem = false;
}

if (!$dem) {
    header('Location: liste.php');
    exit;
}

/* ── Marque la demande comme lue ── */
if (!$dem['lu']) {
    try {
        $stmt = $pdo->prepare('UPDATE demandes_projet SET lu = 1 WHERE id = :id');
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log('[PORTFOLIO][ADMIN] Erreur marquage lu (demande) : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Demande de <?php echo htmlspecialchars($dem['nom'], ENT_QUOTES, 'UTF-8'); ?> — Administration</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="admin-body">

  <?php require __DIR__ . '/../composants/admin-nav.php'; ?>

  <main class="admin-main">
    <div class="admin-container admin-container--narrow">

      <div class="admin-page-header">
        <div>
          <h1>Demande de projet</h1>
          <p><?php echo date('d/m/Y à H:i', strtotime($dem['date_demande'])); ?></p>
        </div>
        <a href="liste.php" class="btn btn--outline">
          <i class="fa fa-arrow-left"></i> Retour à la liste
        </a>
      </div>

      <div class="admin-panel admin-detail-card">

        <div class="admin-detail-card__header">
          <div class="admin-detail-card__avatar">
            <?php echo strtoupper(mb_substr($dem['nom'], 0, 1)); ?>
          </div>
          <div>
            <strong><?php echo htmlspecialchars($dem['nom'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <a href="mailto:<?php echo htmlspecialchars($dem['email'], ENT_QUOTES, 'UTF-8'); ?>" class="admin-detail-card__email">
              <?php echo htmlspecialchars($dem['email'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
          </div>
        </div>

        <hr class="admin-form-divider">

        <div class="admin-detail-card__meta">
          <div>
            <span class="admin-detail-card__meta-label">Type de projet</span>
            <span class="tag"><?php echo htmlspecialchars(libelle_type_projet($dem['type_projet']), ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <div>
            <span class="admin-detail-card__meta-label">Budget estimé</span>
            <strong><?php echo $dem['budget'] ? htmlspecialchars(libelle_budget($dem['budget']), ENT_QUOTES, 'UTF-8') : 'Non spécifié'; ?></strong>
          </div>
        </div>

        <hr class="admin-form-divider">

        <div class="admin-detail-card__body">
          <?php echo nl2br(htmlspecialchars($dem['description'], ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <div class="admin-form__actions" style="margin-top: 1.5rem;">
          <a href="mailto:<?php echo htmlspecialchars($dem['email'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn--primary">
            <i class="fa fa-reply"></i> Répondre par email
          </a>
          <a href="supprimer.php?id=<?php echo $id; ?>" class="btn btn--outline">
            <i class="fa fa-trash"></i> Supprimer
          </a>
        </div>

      </div>

    </div>
  </main>

</body>
</html>
