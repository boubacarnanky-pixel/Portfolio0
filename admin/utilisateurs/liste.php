<?php
/* ============================================================
   admin/utilisateurs/liste.php — Liste des administrateurs
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'utilisateurs';

$message_succes = '';
if (isset($_SESSION['flash_succes'])) {
    $message_succes = $_SESSION['flash_succes'];
    unset($_SESSION['flash_succes']);
}

try {
    $admins = $pdo->query('
        SELECT id, prenom, nom, email, date_creation
        FROM administrateurs
        ORDER BY date_creation ASC
    ')->fetchAll();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur liste administrateurs : ' . $e->getMessage());
    $admins = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Administrateurs — Administration</title>
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
          <h1>Administrateurs</h1>
          <p><?php echo count($admins); ?> compte<?php echo count($admins) > 1 ? 's' : ''; ?> administrateur<?php echo count($admins) > 1 ? 's' : ''; ?></p>
        </div>
        <a href="creer.php" class="btn btn--primary">
          <i class="fa fa-user-plus"></i> Nouvel administrateur
        </a>
      </div>

      <?php if ($message_succes !== '') : ?>
        <div class="alert alert--success">
          <i class="fa fa-circle-check"></i>
          <div><strong><?php echo htmlspecialchars($message_succes, ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>
      <?php endif; ?>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Créé le</th>
              <th class="admin-table__actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $admin) : ?>
            <?php $est_soi_meme = ((int) $admin['id'] === (int) $_SESSION['admin_id']); ?>
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <?php if ($est_soi_meme) : ?>
                  <span class="admin-badge-you">Vous</span>
                <?php endif; ?>
              </td>
              <td><span class="admin-table__muted"><?php echo htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8'); ?></span></td>
              <td><span class="admin-table__muted"><?php echo date('d/m/Y', strtotime($admin['date_creation'])); ?></span></td>
              <td>
                <div class="admin-table__actions">
                  <a href="modifier.php?id=<?php echo (int) $admin['id']; ?>"
                     class="admin-action-btn admin-action-btn--edit" title="Modifier">
                    <i class="fa fa-pen"></i>
                  </a>

                  <?php if ($est_soi_meme) : ?>
                    <!--
                      ⚠️ Règle de sécurité : un admin ne peut jamais
                      supprimer SON PROPRE compte — il risquerait de
                      se retrouver enfermé hors du système.
                      Le bouton est désactivé visuellement plutôt
                      que simplement caché, pour que ce soit explicite.
                    -->
                    <span class="admin-action-btn admin-action-btn--disabled" title="Vous ne pouvez pas supprimer votre propre compte">
                      <i class="fa fa-trash"></i>
                    </span>
                  <?php elseif (count($admins) <= 1) : ?>
                    <span class="admin-action-btn admin-action-btn--disabled" title="Impossible de supprimer le dernier administrateur">
                      <i class="fa fa-trash"></i>
                    </span>
                  <?php else : ?>
                    <a href="supprimer.php?id=<?php echo (int) $admin['id']; ?>"
                       class="admin-action-btn admin-action-btn--delete" title="Supprimer">
                      <i class="fa fa-trash"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

</body>
</html>
