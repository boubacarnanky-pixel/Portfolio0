<?php
/* ============================================================
   admin/projets/supprimer.php — Supprimer un projet (CRUD : Delete)
   ============================================================

   FONCTIONNEMENT EN 2 ÉTAPES (sans JavaScript) :

   ÉTAPE 1 (GET) : on affiche une page de CONFIRMATION avec le
   titre du projet concerné, et un formulaire POST caché contenant
   le token CSRF. Rien n'est supprimé à ce stade — une requête
   GET ne doit jamais avoir d'effet de bord (règle HTTP).

   ÉTAPE 2 (POST) : l'admin clique sur "Confirmer la suppression"
   → le formulaire envoie une requête POST avec le token CSRF
   → on vérifie le token, puis on supprime réellement.

   Ce flux empêche un lien malveillant (ex: une image cachée
   sur un autre site pointant vers cette URL) de déclencher
   une suppression à l'insu de l'administrateur connecté.
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'projets';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

/* ── Chargement du projet concerné ── */
try {
    $stmt = $pdo->prepare('SELECT id, titre, image FROM projets WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $projet = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement projet (suppression) : ' . $e->getMessage());
    $projet = false;
}

if (!$projet) {
    header('Location: liste.php');
    exit;
}

/* ============================================================
   ÉTAPE 2 — Traitement de la confirmation (POST)
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    try {
        $stmt = $pdo->prepare('DELETE FROM projets WHERE id = :id');
        $stmt->execute([':id' => $id]);

        /* Supprime aussi le fichier image physique du disque */
        if (!empty($projet['image'])) {
            supprimer_fichier_image($projet['image']);
        }

        $_SESSION['flash_succes'] = 'Projet « ' . $projet['titre'] . ' » supprimé.';

    } catch (PDOException $e) {
        error_log('[PORTFOLIO][ADMIN] Erreur suppression projet : ' . $e->getMessage());
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

        <h2>Supprimer ce projet ?</h2>
        <p>
          Tu es sur le point de supprimer définitivement le projet
          « <strong><?php echo htmlspecialchars($projet['titre'], ENT_QUOTES, 'UTF-8'); ?></strong> ».
          Cette action est <strong>irréversible</strong>, y compris l'image associée.
        </p>

        <!-- ÉTAPE 1 → ÉTAPE 2 : formulaire POST avec token CSRF -->
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
