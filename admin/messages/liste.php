<?php
/* ============================================================
   admin/messages/liste.php — Liste des messages de contact
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'messages';

$message_succes = '';
if (isset($_SESSION['flash_succes'])) {
    $message_succes = $_SESSION['flash_succes'];
    unset($_SESSION['flash_succes']);
}

try {
    $messages = $pdo->query('
        SELECT id, nom, email, message, lu, date_envoi
        FROM messages_contact
        ORDER BY date_envoi DESC
    ')->fetchAll();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur liste messages : ' . $e->getMessage());
    $messages = [];
}

$nb_non_lus = count(array_filter($messages, fn($m) => !$m['lu']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Messages de contact — Administration</title>
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
          <h1>Messages de contact</h1>
          <p>
            <?php echo count($messages); ?> message<?php echo count($messages) > 1 ? 's' : ''; ?>
            <?php if ($nb_non_lus > 0) : ?>
              — <strong style="color: var(--clr-primary);"><?php echo $nb_non_lus; ?> non lu<?php echo $nb_non_lus > 1 ? 's' : ''; ?></strong>
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

      <?php if (empty($messages)) : ?>

        <div class="admin-panel admin-empty-block">
          <i class="fa fa-envelope-open"></i>
          <h3>Aucun message pour le moment</h3>
          <p>Les messages envoyés depuis la page Contact apparaîtront ici.</p>
        </div>

      <?php else : ?>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th></th>
                <th>De</th>
                <th>Message</th>
                <th>Date</th>
                <th class="admin-table__actions-col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($messages as $msg) : ?>
              <tr class="<?php echo !$msg['lu'] ? 'admin-row--unread' : ''; ?>">
                <td>
                  <?php if (!$msg['lu']) : ?>
                    <span class="admin-dot-unread" title="Non lu"></span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?php echo htmlspecialchars($msg['nom'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                  <span class="admin-table__muted"><?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td>
                  <span class="admin-table__excerpt">
                    <?php echo htmlspecialchars(mb_substr($msg['message'], 0, 70), ENT_QUOTES, 'UTF-8'); ?><?php echo mb_strlen($msg['message']) > 70 ? '…' : ''; ?>
                  </span>
                </td>
                <td data-label="Date"><span class="admin-table__muted"><?php echo date('d/m/Y H:i', strtotime($msg['date_envoi'])); ?></span></td>
                <td>
                  <div class="admin-table__actions">
                    <a href="voir.php?id=<?php echo (int) $msg['id']; ?>"
                       class="admin-action-btn admin-action-btn--view" title="Voir le message">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="supprimer.php?id=<?php echo (int) $msg['id']; ?>"
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
