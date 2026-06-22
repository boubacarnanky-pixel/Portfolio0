<?php
/* ============================================================
   admin/projets/creer.php — Créer un projet (CRUD : Create)
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'projets';

$erreurs = [];
$donnees = ['titre' => '', 'description' => '', 'technologies' => '', 'lien' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    $donnees['titre']        = get_post('titre');
    $donnees['description']  = get_post_raw('description'); // brut, nettoyé à l'affichage
    $donnees['technologies'] = get_post('technologies');
    $donnees['lien']         = get_post('lien');

    /* ── Validation ── */
    if (!champ_requis($donnees['titre'])) {
        $erreurs['titre'] = 'Le titre est obligatoire.';
    } elseif (!longueur_max($donnees['titre'], 150)) {
        $erreurs['titre'] = 'Le titre ne doit pas dépasser 150 caractères.';
    }

    if (!champ_requis($donnees['description'])) {
        $erreurs['description'] = 'La description est obligatoire.';
    } elseif (!longueur_min($donnees['description'], 10)) {
        $erreurs['description'] = 'La description doit contenir au moins 10 caractères.';
    }

    if (!champ_requis($donnees['technologies'])) {
        $erreurs['technologies'] = 'Indique au moins une technologie (ex: HTML, CSS, PHP).';
    }

    /* Le lien est optionnel, mais s'il est rempli, on vérifie un minimum de format */
    if (champ_requis($donnees['lien']) && !longueur_max($donnees['lien'], 255)) {
        $erreurs['lien'] = 'Le lien est trop long (255 caractères max).';
    }

    /* ── Upload de l'image (optionnel à la création) ── */
    $chemin_image = null;
    if (empty($erreurs)) {
        $resultat_upload = traiter_upload_image($_FILES['image']);
        if (!$resultat_upload['succes']) {
            $erreurs['image'] = $resultat_upload['erreur'];
        } else {
            $chemin_image = $resultat_upload['chemin'];
        }
    }

    /* ── Insertion en base ── */
    if (empty($erreurs)) {
        try {
            $sql  = '
                INSERT INTO projets (titre, description, technologies, image, lien)
                VALUES (:titre, :description, :technologies, :image, :lien)
            ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre'        => $donnees['titre'],
                ':description'  => trim($donnees['description']),
                ':technologies' => $donnees['technologies'],
                ':image'        => $chemin_image,
                ':lien'         => champ_requis($donnees['lien']) ? $donnees['lien'] : null,
            ]);

            $_SESSION['flash_succes'] = 'Projet « ' . $donnees['titre'] . ' » créé avec succès.';
            header('Location: liste.php');
            exit;

        } catch (PDOException $e) {
            error_log('[PORTFOLIO][ADMIN] Erreur création projet : ' . $e->getMessage());
            /* Si l'insertion échoue après un upload réussi, on nettoie le fichier orphelin */
            if ($chemin_image !== null) {
                supprimer_fichier_image($chemin_image);
            }
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
  <title>Nouveau projet — Administration</title>
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
          <h1>Nouveau projet</h1>
          <p>Ajoute un projet à ton portfolio public.</p>
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

        <!--
          ⚠️ enctype="multipart/form-data" est OBLIGATOIRE
          dès qu'un formulaire contient un <input type="file">.
          Sans cet attribut, le fichier ne serait JAMAIS transmis
          au serveur — $_FILES resterait vide.
        -->
        <form method="post" action="creer.php" enctype="multipart/form-data" class="admin-form" novalidate>

          <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">

          <div class="form-group <?php echo isset($erreurs['titre']) ? 'form-group--error' : ''; ?>">
            <label for="titre">Titre du projet <span class="required">*</span></label>
            <input type="text" id="titre" name="titre"
                   placeholder="Ex : Gestionnaire de Tâches"
                   value="<?php echo htmlspecialchars($donnees['titre'], ENT_QUOTES, 'UTF-8'); ?>"
                   maxlength="150" required>
            <?php if (isset($erreurs['titre'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['titre']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['description']) ? 'form-group--error' : ''; ?>">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Décris le projet en quelques phrases..."
                      required><?php echo htmlspecialchars($donnees['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <?php if (isset($erreurs['description'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['description']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['technologies']) ? 'form-group--error' : ''; ?>">
            <label for="technologies">Technologies <span class="required">*</span></label>
            <input type="text" id="technologies" name="technologies"
                   placeholder="Ex : HTML, CSS, PHP, MySQL"
                   value="<?php echo htmlspecialchars($donnees['technologies'], ENT_QUOTES, 'UTF-8'); ?>"
                   required>
            <span class="form-hint">Sépare chaque technologie par une virgule.</span>
            <?php if (isset($erreurs['technologies'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['technologies']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['lien']) ? 'form-group--error' : ''; ?>">
            <label for="lien">Lien du projet <span class="form-optional">(facultatif)</span></label>
            <input type="text" id="lien" name="lien"
                   placeholder="Ex : Projets/Mon-Projet.html"
                   value="<?php echo htmlspecialchars($donnees['lien'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php if (isset($erreurs['lien'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['lien']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['image']) ? 'form-group--error' : ''; ?>">
            <label for="image">Image du projet <span class="form-optional">(JPG, PNG ou WEBP — 3 Mo max)</span></label>
            <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/webp">
            <?php if (isset($erreurs['image'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['image']; ?></span>
            <?php endif; ?>
          </div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">
              <i class="fa fa-floppy-disk"></i> Enregistrer le projet
            </button>
            <a href="liste.php" class="btn btn--outline">Annuler</a>
          </div>

        </form>
      </div>

    </div>
  </main>

</body>
</html>
