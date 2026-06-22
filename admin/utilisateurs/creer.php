<?php
/* ============================================================
   admin/utilisateurs/creer.php — Créer un administrateur
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'utilisateurs';

$erreurs = [];
$donnees = ['prenom' => '', 'nom' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    $donnees['prenom'] = get_post('prenom');
    $donnees['nom']    = get_post('nom');
    $donnees['email']  = get_post('email');
    $mdp               = $_POST['mot_de_passe'] ?? '';
    $mdp_confirmation  = $_POST['mot_de_passe_confirmation'] ?? '';

    /* ── Validation ── */
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
    if (!champ_requis($mdp) || mb_strlen($mdp) < 8) {
        $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($mdp !== $mdp_confirmation) {
        $erreurs['mot_de_passe'] = 'Les deux mots de passe ne correspondent pas.';
    }

    /* Unicité de l'email */
    if (!isset($erreurs['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM administrateurs WHERE email = :email');
        $stmt->execute([':email' => $donnees['email']]);
        if ($stmt->fetch()) {
            $erreurs['email'] = 'Cet email est déjà utilisé par un autre administrateur.';
        }
    }

    if (empty($erreurs)) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);

        try {
            $sql  = '
                INSERT INTO administrateurs (prenom, nom, email, mot_de_passe)
                VALUES (:prenom, :nom, :email, :mdp)
            ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':prenom' => $donnees['prenom'],
                ':nom'    => $donnees['nom'],
                ':email'  => $donnees['email'],
                ':mdp'    => $hash,
            ]);

            $_SESSION['flash_succes'] = 'Administrateur « ' . $donnees['prenom'] . ' ' . $donnees['nom'] . ' » créé avec succès.';
            header('Location: liste.php');
            exit;

        } catch (PDOException $e) {
            error_log('[PORTFOLIO][ADMIN] Erreur création administrateur : ' . $e->getMessage());
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
  <title>Nouvel administrateur — Administration</title>
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
          <h1>Nouvel administrateur</h1>
          <p>Crée un accès à l'espace d'administration.</p>
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

        <form method="post" action="creer.php" class="admin-form" novalidate>

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

          <div class="form-row">
            <div class="form-group <?php echo isset($erreurs['mot_de_passe']) ? 'form-group--error' : ''; ?>">
              <label for="mot_de_passe">Mot de passe <span class="required">*</span></label>
              <input type="password" id="mot_de_passe" name="mot_de_passe" autocomplete="new-password" required>
              <span class="form-hint">8 caractères minimum.</span>
            </div>

            <div class="form-group">
              <label for="mot_de_passe_confirmation">Confirmer <span class="required">*</span></label>
              <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" autocomplete="new-password" required>
            </div>
          </div>
          <?php if (isset($erreurs['mot_de_passe'])) : ?>
            <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['mot_de_passe']; ?></span>
          <?php endif; ?>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">
              <i class="fa fa-floppy-disk"></i> Créer le compte
            </button>
            <a href="liste.php" class="btn btn--outline">Annuler</a>
          </div>

        </form>
      </div>

    </div>
  </main>

</body>
</html>
