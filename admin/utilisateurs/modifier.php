<?php
/* ============================================================
   admin/utilisateurs/modifier.php — Modifier un administrateur
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'utilisateurs';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, prenom, nom, email FROM administrateurs WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $admin_existant = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement administrateur : ' . $e->getMessage());
    $admin_existant = false;
}

if (!$admin_existant) {
    header('Location: liste.php');
    exit;
}

$erreurs = [];
$donnees = [
    'prenom' => $admin_existant['prenom'],
    'nom'    => $admin_existant['nom'],
    'email'  => $admin_existant['email'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    $donnees['prenom'] = get_post('prenom');
    $donnees['nom']    = get_post('nom');
    $donnees['email']  = get_post('email');
    $mdp               = $_POST['mot_de_passe'] ?? '';
    $mdp_confirmation  = $_POST['mot_de_passe_confirmation'] ?? '';

    if (!champ_requis($donnees['prenom'])) {
        $erreurs['prenom'] = 'Le prénom est obligatoire.';
    }
    if (!champ_requis($donnees['nom'])) {
        $erreurs['nom'] = 'Le nom est obligatoire.';
    }
    if (!champ_requis($donnees['email'])) {
        $erreurs['email'] = 'L\'email est obligatoire.';
    } elseif (!email_valide($donnees['email'])) {
        $erreurs['email'] = 'Cette adresse email n\'est pas valide.';
    }

    /* Unicité de l'email, sauf pour CE même administrateur */
    if (!isset($erreurs['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM administrateurs WHERE email = :email AND id != :id');
        $stmt->execute([':email' => $donnees['email'], ':id' => $id]);
        if ($stmt->fetch()) {
            $erreurs['email'] = 'Cet email est déjà utilisé par un autre administrateur.';
        }
    }

    /*
     * ── Mot de passe OPTIONNEL ──
     * Si les deux champs sont vides, on garde le mot de passe
     * actuel inchangé — l'admin ne doit pas être forcé de le
     * retaper juste pour modifier son prénom, par exemple.
     * S'il est rempli, on applique les mêmes règles qu'à la
     * création.
     */
    $changement_mdp_demande = champ_requis($mdp) || champ_requis($mdp_confirmation);
    $nouveau_hash            = null;

    if ($changement_mdp_demande) {
        if (mb_strlen($mdp) < 8) {
            $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($mdp !== $mdp_confirmation) {
            $erreurs['mot_de_passe'] = 'Les deux mots de passe ne correspondent pas.';
        } else {
            $nouveau_hash = password_hash($mdp, PASSWORD_DEFAULT);
        }
    }

    if (empty($erreurs)) {
        try {
            if ($nouveau_hash !== null) {
                /* Mise à jour AVEC changement de mot de passe */
                $sql  = '
                    UPDATE administrateurs
                    SET prenom = :prenom, nom = :nom, email = :email, mot_de_passe = :mdp
                    WHERE id = :id
                ';
                $params = [
                    ':prenom' => $donnees['prenom'],
                    ':nom'    => $donnees['nom'],
                    ':email'  => $donnees['email'],
                    ':mdp'    => $nouveau_hash,
                    ':id'     => $id,
                ];
            } else {
                /* Mise à jour SANS toucher au mot de passe */
                $sql  = '
                    UPDATE administrateurs
                    SET prenom = :prenom, nom = :nom, email = :email
                    WHERE id = :id
                ';
                $params = [
                    ':prenom' => $donnees['prenom'],
                    ':nom'    => $donnees['nom'],
                    ':email'  => $donnees['email'],
                    ':id'     => $id,
                ];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            /*
             * Si l'admin modifie SON PROPRE compte, on met aussi
             * à jour les infos affichées dans la session (topbar)
             * sans attendre une reconnexion.
             */
            if ($id === (int) $_SESSION['admin_id']) {
                $_SESSION['admin_prenom'] = $donnees['prenom'];
                $_SESSION['admin_nom']    = $donnees['nom'];
                $_SESSION['admin_email']  = $donnees['email'];
            }

            $_SESSION['flash_succes'] = 'Administrateur « ' . $donnees['prenom'] . ' ' . $donnees['nom'] . ' » modifié avec succès.';
            header('Location: liste.php');
            exit;

        } catch (PDOException $e) {
            error_log('[PORTFOLIO][ADMIN] Erreur modification administrateur : ' . $e->getMessage());
            $erreurs['global'] = 'Une erreur technique est survenue. Veuillez réessayer.';
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
  <title>Modifier l'administrateur — Administration</title>
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
          <h1>Modifier l'administrateur</h1>
          <p><?php echo htmlspecialchars($admin_existant['prenom'] . ' ' . $admin_existant['nom'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <a href="liste.php" class="btn btn--outline">
          <i class="fa fa-arrow-left"></i> Retour à la liste
        </a>
      </div>

      <?php if (isset($erreurs['global'])) : ?>
        <div class="alert alert--error">
          <i class="fa fa-circle-xmark"></i>
          <div><strong><?php echo $erreurs['global']; ?></strong></div>
        </div>
      <?php endif; ?>

      <div class="admin-panel">

        <form method="post" action="modifier.php?id=<?php echo $id; ?>" class="admin-form" novalidate>

          <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">

          <div class="form-row">
            <div class="form-group <?php echo isset($erreurs['prenom']) ? 'form-group--error' : ''; ?>">
              <label for="prenom">Prénom <span class="required">*</span></label>
              <input type="text" id="prenom" name="prenom"
                     value="<?php echo htmlspecialchars($donnees['prenom'], ENT_QUOTES, 'UTF-8'); ?>" required>
              <?php if (isset($erreurs['prenom'])) : ?>
                <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['prenom']; ?></span>
              <?php endif; ?>
            </div>

            <div class="form-group <?php echo isset($erreurs['nom']) ? 'form-group--error' : ''; ?>">
              <label for="nom">Nom <span class="required">*</span></label>
              <input type="text" id="nom" name="nom"
                     value="<?php echo htmlspecialchars($donnees['nom'], ENT_QUOTES, 'UTF-8'); ?>" required>
              <?php if (isset($erreurs['nom'])) : ?>
                <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['nom']; ?></span>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-group <?php echo isset($erreurs['email']) ? 'form-group--error' : ''; ?>">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($donnees['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <?php if (isset($erreurs['email'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['email']; ?></span>
            <?php endif; ?>
          </div>

          <hr class="admin-form-divider">

          <p class="form-hint" style="margin-bottom: .5rem;">
            <i class="fa fa-circle-info"></i> Laisse les champs ci-dessous vides pour conserver le mot de passe actuel.
          </p>

          <div class="form-row">
            <div class="form-group <?php echo isset($erreurs['mot_de_passe']) ? 'form-group--error' : ''; ?>">
              <label for="mot_de_passe">Nouveau mot de passe <span class="form-optional">(facultatif)</span></label>
              <input type="password" id="mot_de_passe" name="mot_de_passe" autocomplete="new-password">
            </div>

            <div class="form-group">
              <label for="mot_de_passe_confirmation">Confirmer</label>
              <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" autocomplete="new-password">
            </div>
          </div>
          <?php if (isset($erreurs['mot_de_passe'])) : ?>
            <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['mot_de_passe']; ?></span>
          <?php endif; ?>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">
              <i class="fa fa-floppy-disk"></i> Enregistrer les modifications
            </button>
            <a href="liste.php" class="btn btn--outline">Annuler</a>
          </div>

        </form>
      </div>

    </div>
  </main>

</body>
</html>
