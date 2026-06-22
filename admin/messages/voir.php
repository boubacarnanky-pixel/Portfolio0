<?php
/* ============================================================
   admin/messages/voir.php — Détail d'un message de contact
   ============================================================

   Cette page fait 2 choses dans l'ordre :
     1. Charge le message demandé
     2. Le marque comme LU (UPDATE lu = 1), AVANT même l'affichage,
        exactement comme l'ouverture d'un email dans une boîte mail
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'messages';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, nom, email, message, lu, date_envoi FROM messages_contact WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $msg = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement message : ' . $e->getMessage());
    $msg = false;
}

if (!$msg) {
    header('Location: liste.php');
    exit;
}

/* ── Marque le message comme lu, s'il ne l'était pas déjà ── */
if (!$msg['lu']) {
    try {
        $stmt = $pdo->prepare('UPDATE messages_contact SET lu = 1 WHERE id = :id');
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log('[PORTFOLIO][ADMIN] Erreur marquage lu (message) : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Message de <?php echo htmlspecialchars($msg['nom'], ENT_QUOTES, 'UTF-8'); ?> — Administration</title>
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
          <h1>Message reçu</h1>
          <p><?php echo date('d/m/Y à H:i', strtotime($msg['date_envoi'])); ?></p>
        </div>
        <a href="liste.php" class="btn btn--outline">
          <i class="fa fa-arrow-left"></i> Retour à la liste
        </a>
      </div>

      <div class="admin-panel admin-detail-card">

        <div class="admin-detail-card__header">
          <div class="admin-detail-card__avatar">
            <?php echo strtoupper(mb_substr($msg['nom'], 0, 1)); ?>
          </div>
          <div>
            <strong><?php echo htmlspecialchars($msg['nom'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <a href="mailto:<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>" class="admin-detail-card__email">
              <?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
          </div>
        </div>

        <hr class="admin-form-divider">

        <div class="admin-detail-card__body">
          <?php echo nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <div class="admin-form__actions" style="margin-top: 1.5rem;">
          <a href="mailto:<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn--primary">
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
