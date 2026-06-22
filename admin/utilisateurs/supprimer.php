<?php
/* ============================================================
   admin/utilisateurs/supprimer.php — Supprimer un administrateur
   ============================================================

   3 COUCHES DE SÉCURITÉ CUMULÉES :

   1. Impossible de supprimer SON PROPRE compte
      → éviterait de s'enfermer hors du système
   2. Impossible de supprimer le DERNIER administrateur restant
      → le système doit toujours garder au moins un accès
   3. RE-AUTHENTIFICATION : même connecté, l'admin doit retaper
      SON PROPRE mot de passe pour confirmer la suppression
      d'un autre compte — une session active ne suffit pas
      pour une action aussi destructrice.
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'utilisateurs';

$id          = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$mon_id      = (int) $_SESSION['admin_id'];
$erreur_mdp  = '';

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

/* ── RÈGLE 1 : impossible de se supprimer soi-même ── */
if ($id === $mon_id) {
    $_SESSION['flash_succes'] = '';
    header('Location: liste.php');
    exit;
}

/* ── Chargement de l'administrateur cible ── */
try {
    $stmt = $pdo->prepare('SELECT id, prenom, nom, email FROM administrateurs WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $admin_cible = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement administrateur (suppression) : ' . $e->getMessage());
    $admin_cible = false;
}

if (!$admin_cible) {
    header('Location: liste.php');
    exit;
}

/* ── RÈGLE 2 : impossible de supprimer le dernier admin ── */
if (compter_administrateurs($pdo) <= 1) {
    $_SESSION['flash_succes'] = '';
    header('Location: liste.php');
    exit;
}

/* ============================================================
   TRAITEMENT DE LA CONFIRMATION (POST)
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    $mdp_confirmation = $_POST['mot_de_passe_actuel'] ?? '';

    /* ── RÈGLE 3 : re-authentification par mot de passe ── */
    try {
        $stmt = $pdo->prepare('SELECT mot_de_passe FROM administrateurs WHERE id = :id');
        $stmt->execute([':id' => $mon_id]);
        $mon_compte = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('[PORTFOLIO][ADMIN] Erreur vérification mot de passe : ' . $e->getMessage());
        $mon_compte = false;
    }

    if (!$mon_compte || !password_verify($mdp_confirmation, $mon_compte['mot_de_passe'])) {
        $erreur_mdp = 'Mot de passe incorrect. Suppression annulée.';

    } else {
        /* Mot de passe confirmé → suppression réelle */
        try {
            $stmt = $pdo->prepare('DELETE FROM administrateurs WHERE id = :id');
            $stmt->execute([':id' => $id]);

            $_SESSION['flash_succes'] = 'Administrateur « ' . $admin_cible['prenom'] . ' ' . $admin_cible['nom'] . ' » supprimé.';
            header('Location: liste.php');
            exit;

        } catch (PDOException $e) {
            error_log('[PORTFOLIO][ADMIN] Erreur suppression administrateur : ' . $e->getMessage());
            $erreur_mdp = 'Une erreur technique est survenue. Veuillez réessayer.';
        }
    }
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

        <h2>Supprimer cet administrateur ?</h2>
        <p>
          Tu es sur le point de supprimer définitivement le compte de
          « <strong><?php echo htmlspecialchars($admin_cible['prenom'] . ' ' . $admin_cible['nom'], ENT_QUOTES, 'UTF-8'); ?></strong> »
          (<?php echo htmlspecialchars($admin_cible['email'], ENT_QUOTES, 'UTF-8'); ?>).
          Cette action est <strong>irréversible</strong>.
        </p>

        <?php if ($erreur_mdp !== '') : ?>
          <div class="alert alert--error" style="text-align:left;">
            <i class="fa fa-circle-xmark"></i>
            <div><strong><?php echo htmlspecialchars($erreur_mdp, ENT_QUOTES, 'UTF-8'); ?></strong></div>
          </div>
        <?php endif; ?>

        <!--
          RE-AUTHENTIFICATION : on redemande le mot de passe de
          l'admin CONNECTÉ (pas celui de la cible) pour confirmer
          qu'il s'agit bien d'une action volontaire et autorisée.
        -->
        <form method="post" action="supprimer.php?id=<?php echo $id; ?>" class="admin-form" style="text-align:left;">
          <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">

          <div class="form-group">
            <label for="mot_de_passe_actuel">Confirme ton mot de passe pour continuer</label>
            <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel"
                   autocomplete="current-password" required autofocus>
          </div>

          <div class="admin-confirm-card__actions">
            <a href="liste.php" class="btn btn--outline">Annuler</a>
            <button type="submit" class="btn btn--danger">
              <i class="fa fa-trash"></i> Confirmer la suppression
            </button>
          </div>
        </form>
      </div>

    </div>
  </main>

</body>
</html>
