<?php
/* ============================================================
   admin/connexion.php — Connexion à l'espace d'administration
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../fonctions.php';
require_once __DIR__ . '/../config/connexion.php';

$pdo = get_pdo();

/*
 * Si déjà connecté, inutile de revoir le formulaire de connexion.
 * Redirection directe vers le dashboard.
 */
if (est_connecte()) {
    header('Location: dashboard.php');
    exit;
}

$erreur     = '';
$email_saisi = '';
$ip         = obtenir_ip_visiteur();

/* ============================================================
   TRAITEMENT DU FORMULAIRE DE CONNEXION
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ÉTAPE 1 : Vérification CSRF */
    verifier_token_csrf();

    $email_saisi      = get_post('email');
    $mot_de_passe_brut = $_POST['mot_de_passe'] ?? '';

    /* ÉTAPE 2 : Anti-brute-force — calcul du délai d'attente */
    $nb_echecs = compter_tentatives_echouees($pdo, $ip);
    $delai     = calculer_delai_attente($nb_echecs);

    if ($delai > 0) {
        /*
         * On informe l'utilisateur du temps d'attente sans
         * révéler le nombre exact de tentatives passées —
         * cette info pourrait aider un attaquant à calibrer
         * sa stratégie.
         */
        $erreur = "Trop de tentatives échouées. Veuillez réessayer dans {$delai} secondes.";

    } elseif (!champ_requis($email_saisi) || !champ_requis($mot_de_passe_brut)) {
        $erreur = 'Veuillez renseigner votre email et votre mot de passe.';

    } else {

        /*
         * Recherche de l'administrateur par email.
         * Requête préparée → protection injection SQL.
         */
        try {
            $sql  = 'SELECT id, prenom, nom, email, mot_de_passe FROM administrateurs WHERE email = :email';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email_saisi]);
            $admin = $stmt->fetch();

        } catch (PDOException $e) {
            error_log('[PORTFOLIO] Erreur recherche admin : ' . $e->getMessage());
            $admin = false;
        }

        /*
         * ⚠️ POINT DE SÉCURITÉ CRITIQUE — password_verify()
         *
         * On ne compare JAMAIS directement deux chaînes de
         * mots de passe (=== ou ==). password_verify() prend
         * le mot de passe en clair saisi par l'utilisateur et
         * le hash stocké en base, et vérifie leur correspondance
         * sans jamais "dé-hacher" le hash (ce qui est de toute
         * façon mathématiquement impossible avec bcrypt).
         *
         * On structure le test ainsi :
         *   $admin existe ET password_verify() retourne true
         * pour éviter une erreur PHP si aucun admin n'a été
         * trouvé avec cet email (évite un avertissement sur
         * un index de tableau inexistant).
         */
        if ($admin && password_verify($mot_de_passe_brut, $admin['mot_de_passe'])) {

            /* ✅ Connexion réussie */

            /*
             * session_regenerate_id(true) — protection contre
             * la fixation de session. Le 'true' supprime aussi
             * l'ancien fichier de session côté serveur.
             */
            session_regenerate_id(true);

            $_SESSION['admin_id']     = $admin['id'];
            $_SESSION['admin_email']  = $admin['email'];
            $_SESSION['admin_prenom'] = $admin['prenom'];
            $_SESSION['admin_nom']    = $admin['nom'];

            /* On efface l'historique de tentatives échouées pour cette IP */
            reinitialiser_tentatives($pdo, $ip);

            header('Location: dashboard.php');
            exit;

        } else {

            /* ❌ Échec — email inconnu OU mot de passe incorrect */

            /*
             * ⚠️ Message d'erreur volontairement générique.
             * On ne dit JAMAIS "email inconnu" vs "mot de passe
             * incorrect" séparément. Si on le faisait, un
             * attaquant pourrait déduire quels emails existent
             * en base (énumération de comptes), même sans
             * connaître le mot de passe.
             */
            $erreur = 'Email ou mot de passe incorrect.';

            enregistrer_tentative_echouee($pdo, $ip);
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
  <title>Connexion Admin — Portfolio Boubacar Nanky</title>
  <link rel="stylesheet" href="../css/global.css">
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="admin-auth-body">

  <div class="admin-auth-wrap">
    <div class="admin-auth-card">

      <div class="admin-auth-logo">
        <span class="logo-circle">NB</span>
        <span class="logo-text">Nanky_B</span>
      </div>

      <h1 class="admin-auth-title">Espace Administration</h1>
      <p class="admin-auth-subtitle">Connectez-vous pour gérer le portfolio</p>

      <?php if ($erreur !== '') : ?>
        <div class="alert alert--error">
          <i class="fa fa-circle-xmark"></i>
          <div><strong><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>
      <?php endif; ?>

      <form method="post" action="connexion.php" class="admin-auth-form" novalidate>

        <input type="hidden" name="csrf_token" value="<?php echo generer_token_csrf(); ?>">

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email"
                 placeholder="admin@nankyb.com"
                 value="<?php echo htmlspecialchars($email_saisi, ENT_QUOTES, 'UTF-8'); ?>"
                 required autocomplete="username" autofocus>
        </div>

        <div class="form-group">
          <label for="mot_de_passe">Mot de passe</label>
          <input type="password" id="mot_de_passe" name="mot_de_passe"
                 placeholder="••••••••"
                 required autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn--primary btn--full">
          <i class="fa fa-right-to-bracket"></i> Se connecter
        </button>

      </form>

      <a href="../index.php" class="admin-auth-back">
        <i class="fa fa-arrow-left"></i> Retour au portfolio
      </a>

    </div>
  </div>

</body>
</html>
