<?php
/* ============================================================
   admin/demandes/supprimer.php — Supprimer une demande de projet
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'demandes';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, nom, email FROM demandes_projet WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $dem = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement demande (suppression) : ' . $e->getMessage());
    $dem = false;
}

if (!$dem) {
    header('Location: liste.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    try {
        $stmt = $pdo->prepare('DELETE FROM demandes_projet WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $_SESSION['flash_succes'] = 'Demande de « ' . $dem['nom'] . ' » supprimée.';
    } catch (PDOException $e) {
        error_log('[PORTFOLIO][ADMIN] Erreur suppression demande : ' . $e->getMessage());
        $_SESSION['flash_succes'] = '';
    }

    header('Location: liste.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Confirmer la suppression — Administration</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="admin-body">

  <?php require __DIR__ . '/../composants/admin-nav.php'; ?>

  <main class="admin-main">
    <div class="admin-container admin-container--narrow">

      <div class="admin-confirm-card">
        <div class="admin-confirm-card__icon">
          <i class="fa fa-triangle-exclamation"></i>
        </div>

        <h2>Supprimer cette demande ?</h2>
        <p>
          La demande de « <strong><?php echo htmlspecialchars($dem['nom'], ENT_QUOTES, 'UTF-8'); ?></strong> »
          sera définitivement supprimée.
        </p>

        <form method="post" action="supprimer.php?id=<?php echo $id; ?>" class="admin-confirm-card__actions">
          <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">
          <a href="liste.php" class="btn btn--outline">Annuler</a>
          <button type="submit" class="btn btn--danger">
            <i class="fa fa-trash"></i> Confirmer la suppression
          </button>
        </form>
      </div>

    </div>
  </main>

</body>
</html>
