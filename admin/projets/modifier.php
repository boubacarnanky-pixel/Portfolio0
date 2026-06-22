<?php
/* ============================================================
   admin/projets/modifier.php — Modifier un projet (CRUD : Update)
   ============================================================ */

require __DIR__ . '/../auth.php';
$admin_page_active = 'projets';

/* ── Récupération de l'id du projet à modifier ── */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

/* ── Chargement du projet existant ── */
try {
    $stmt = $pdo->prepare('SELECT * FROM projets WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $projet_existant = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[PORTFOLIO][ADMIN] Erreur chargement projet : ' . $e->getMessage());
    $projet_existant = false;
}

/* Le projet n'existe pas (id invalide, déjà supprimé...) */
if (!$projet_existant) {
    $_SESSION['flash_succes'] = '';
    header('Location: liste.php');
    exit;
}

$erreurs = [];
$donnees = [
    'titre'        => $projet_existant['titre'],
    'description'  => $projet_existant['description'],
    'technologies' => $projet_existant['technologies'],
    'lien'         => $projet_existant['lien'] ?? '',
];
$image_actuelle = $projet_existant['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifier_token_csrf();

    $donnees['titre']        = get_post('titre');
    $donnees['description']  = get_post_raw('description');
    $donnees['technologies'] = get_post('technologies');
    $donnees['lien']         = get_post('lien');

    /* ── Validation (identique à creer.php) ── */
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
        $erreurs['technologies'] = 'Indique au moins une technologie.';
    }

    if (champ_requis($donnees['lien']) && !longueur_max($donnees['lien'], 255)) {
        $erreurs['lien'] = 'Le lien est trop long (255 caractères max).';
    }

    /*
     * ── Gestion de l'image ──
     * Trois cas possibles :
     *  1. Aucun nouveau fichier envoyé → on garde l'image actuelle
     *  2. Nouveau fichier valide envoyé → on remplace
     *     (et on supprime l'ancienne image du disque)
     *  3. Nouveau fichier envoyé mais invalide → erreur
     */
    $nouveau_chemin_image = $image_actuelle; // valeur par défaut : on garde l'ancienne

    if (empty($erreurs) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultat_upload = traiter_upload_image($_FILES['image']);
        if (!$resultat_upload['succes']) {
            $erreurs['image'] = $resultat_upload['erreur'];
        } else {
            /* Upload réussi : on supprimera l'ancienne image après le succès de l'UPDATE */
            $nouveau_chemin_image = $resultat_upload['chemin'];
        }
    }

    /* ── Mise à jour en base ── */
    if (empty($erreurs)) {
        try {
            $sql  = '
                UPDATE projets
                SET titre = :titre,
                    description = :description,
                    technologies = :technologies,
                    image = :image,
                    lien = :lien
                WHERE id = :id
            ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre'        => $donnees['titre'],
                ':description'  => trim($donnees['description']),
                ':technologies' => $donnees['technologies'],
                ':image'        => $nouveau_chemin_image,
                ':lien'         => champ_requis($donnees['lien']) ? $donnees['lien'] : null,
                ':id'           => $id,
            ]);

            /* Si une nouvelle image a remplacé l'ancienne, on supprime l'ancien fichier */
            if ($nouveau_chemin_image !== $image_actuelle && !empty($image_actuelle)) {
                supprimer_fichier_image($image_actuelle);
            }

            $_SESSION['flash_succes'] = 'Projet « ' . $donnees['titre'] . ' » modifié avec succès.';
            header('Location: liste.php');
            exit;

        } catch (PDOException $e) {
            error_log('[PORTFOLIO][ADMIN] Erreur modification projet : ' . $e->getMessage());
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
  <title>Modifier le projet — Administration</title>
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
          <h1>Modifier le projet</h1>
          <p><?php echo htmlspecialchars($projet_existant['titre'], ENT_QUOTES, 'UTF-8'); ?></p>
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

        <form method="post" action="modifier.php?id=<?php echo $id; ?>" enctype="multipart/form-data" class="admin-form" novalidate>

          <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">

          <div class="form-group <?php echo isset($erreurs['titre']) ? 'form-group--error' : ''; ?>">
            <label for="titre">Titre du projet <span class="required">*</span></label>
            <input type="text" id="titre" name="titre"
                   value="<?php echo htmlspecialchars($donnees['titre'], ENT_QUOTES, 'UTF-8'); ?>"
                   maxlength="150" required>
            <?php if (isset($erreurs['titre'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['titre']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['description']) ? 'form-group--error' : ''; ?>">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" rows="4"
                      required><?php echo htmlspecialchars($donnees['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <?php if (isset($erreurs['description'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['description']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['technologies']) ? 'form-group--error' : ''; ?>">
            <label for="technologies">Technologies <span class="required">*</span></label>
            <input type="text" id="technologies" name="technologies"
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
                   value="<?php echo htmlspecialchars($donnees['lien'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php if (isset($erreurs['lien'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['lien']; ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php echo isset($erreurs['image']) ? 'form-group--error' : ''; ?>">
            <label for="image">Image du projet <span class="form-optional">(laisser vide pour conserver l'image actuelle)</span></label>

            <?php if (!empty($image_actuelle)) : ?>
              <div class="admin-current-image">
                <img src="../../<?php echo htmlspecialchars($image_actuelle, ENT_QUOTES, 'UTF-8'); ?>" alt="Image actuelle">
                <span>Image actuelle</span>
              </div>
            <?php endif; ?>

            <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/webp">
            <?php if (isset($erreurs['image'])) : ?>
              <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs['image']; ?></span>
            <?php endif; ?>
          </div>

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
