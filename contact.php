<?php
/* ============================================================
   contact.php — Page Contact
   Portfolio Boubacar Nanky

   PARTIE 3 : ajout CSRF + insertion MySQL via PDO
   Formulaire 1 : "Me contacter"      → INSERT messages_contact
   Formulaire 2 : "Demande de projet" → INSERT demandes_projet
   ============================================================ */
$page_active = 'contact';

/*
 * ⚠️ CORRECTION CRITIQUE — ordre d'exécution
 *
 * BUG initial : session_start() n'était appelé que dans
 * composants/navigation.php, qui était require() APRÈS le
 * traitement des formulaires. Résultat : verifier_token_csrf()
 * et generer_token_csrf() s'exécutaient alors que $_SESSION
 * n'existait pas encore → le token ne correspondait jamais
 * → rejet CSRF systématique, même pour une soumission légitime
 * → Warning PHP "session_destroy() sur session non initialisée".
 *
 * RÈGLE D'OR PHP : session_start() doit être la toute première
 * instruction exécutée sur une page qui lit ou écrit $_SESSION,
 * et toujours avant le moindre caractère de sortie HTML.
 *
 * On l'appelle donc ICI, en tout premier, indépendamment de
 * navigation.php. navigation.php contient aussi une vérification
 * session_status() === PHP_SESSION_NONE, donc l'appeler une
 * seconde fois plus bas ne provoque aucune erreur.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'fonctions.php';
require 'config/connexion.php';

$pdo = get_pdo();

/* ── État initial des formulaires ──────────────────────── */
$erreurs_contact = [];
$donnees_contact = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
$succes_contact  = false;

$erreurs_projet  = [];
$donnees_projet  = ['p_nom' => '', 'p_email' => '', 'p_type' => '', 'p_budget' => '', 'p_desc' => ''];
$succes_projet   = false;
$recap_projet    = [];

$onglet_actif = 'contact';

/* ============================================================
   TRAITEMENT FORMULAIRE 1 — Me contacter
   ============================================================ */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['formulaire'])
    && $_POST['formulaire'] === 'contact'
) {
    $onglet_actif = 'contact';

    /* ÉTAPE 1 : Vérification CSRF
       Si le token est invalide → verifier_token_csrf() arrête
       tout avec http_response_code(403). On n'arrive pas à
       l'étape 2 dans ce cas. */
    verifier_token_csrf();

    /* ÉTAPE 2 : Validation des champs */
    $resultat        = valider_formulaire_contact($_POST);
    $erreurs_contact = $resultat['erreurs'];
    $donnees_contact = $resultat['donnees'];

    /* ÉTAPE 3 : Insertion en base si aucune erreur */
    if (empty($erreurs_contact)) {

        /*
         * Requête préparée avec marqueurs nommés (:nom, :email…)
         *
         * Pourquoi des requêtes préparées ?
         * Sans elles, un utilisateur malveillant pourrait entrer
         * dans le champ "nom" quelque chose comme :
         *   ' OR '1'='1
         * … et modifier notre requête SQL.
         * Avec les requêtes préparées, PDO sépare le code SQL
         * des données → les données ne peuvent jamais devenir
         * du code SQL, quelle que soit leur contenu.
         */
        try {
            $sql = '
                INSERT INTO messages_contact (nom, email, message)
                VALUES (:nom, :email, :message)
            ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'     => $donnees_contact['nom'],
                ':email'   => $donnees_contact['email'],
                /*
                 * On concatène sujet + message car la table
                 * messages_contact a une seule colonne "message".
                 * Le sujet est intégré en préfixe pour ne pas
                 * perdre l'information.
                 */
                ':message' => '[' . $donnees_contact['sujet'] . '] '
                              . $donnees_contact['message'],
            ]);

            $succes_contact  = true;
            $donnees_contact = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];

        } catch (PDOException $e) {
            error_log('[PORTFOLIO] Erreur INSERT messages_contact : ' . $e->getMessage());
            $erreurs_contact['global'] = 'Une erreur technique est survenue. Veuillez réessayer.';
        }
    }
}

/* ============================================================
   TRAITEMENT FORMULAIRE 2 — Demande de projet
   ============================================================ */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['formulaire'])
    && $_POST['formulaire'] === 'projet'
) {
    $onglet_actif = 'projet';

    /* ÉTAPE 1 : Vérification CSRF */
    verifier_token_csrf();

    /* ÉTAPE 2 : Validation */
    $resultat       = valider_formulaire_projet($_POST);
    $erreurs_projet = $resultat['erreurs'];
    $donnees_projet = $resultat['donnees'];

    /* ÉTAPE 3 : Insertion en base */
    if (empty($erreurs_projet)) {

        try {
            $sql = '
                INSERT INTO demandes_projet (nom, email, type_projet, description, budget)
                VALUES (:nom, :email, :type_projet, :description, :budget)
            ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'         => $donnees_projet['p_nom'],
                ':email'       => $donnees_projet['p_email'],
                ':type_projet' => $donnees_projet['p_type'],
                ':description' => $donnees_projet['p_desc'],
                ':budget'      => champ_requis($donnees_projet['p_budget'])
                                    ? $donnees_projet['p_budget']
                                    : null,
            ]);

            $succes_projet = true;
            $recap_projet  = [
                'Nom / Entreprise' => $donnees_projet['p_nom'],
                'Email'            => $donnees_projet['p_email'],
                'Type de projet'   => libelle_type_projet($donnees_projet['p_type']),
                'Budget estimé'    => champ_requis($donnees_projet['p_budget'])
                                        ? libelle_budget($donnees_projet['p_budget'])
                                        : 'Non spécifié',
                'Description'      => $donnees_projet['p_desc'],
            ];
            $donnees_projet = ['p_nom' => '', 'p_email' => '', 'p_type' => '', 'p_budget' => '', 'p_desc' => ''];

        } catch (PDOException $e) {
            error_log('[PORTFOLIO] Erreur INSERT demandes_projet : ' . $e->getMessage());
            $erreurs_projet['global'] = 'Une erreur technique est survenue. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Contactez Boubacar Nanky — Développeur Web à Dakar">
  <title>Contact — Boubacar Nanky</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/pages.css">
  <link rel="stylesheet" href="css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body id="top">

  <?php
  /* navigation.php démarre session_start() + enregistre la visite */
  require 'composants/navigation.php';
  enregistrer_visite($pdo, 'contact');
  ?>

  <main>

    <section class="page-hero">
      <div class="container page-hero__inner">
        <nav class="page-hero__breadcrumb" aria-label="Fil d'Ariane">
          <a href="index.php">Accueil</a>
          <span>›</span>
          <span>Contact</span>
        </nav>
        <div class="section-tag">Contact</div>
        <h1 class="page-hero__title">Travaillons Ensemble</h1>
        <p class="page-hero__desc">Un projet, une question ? Je suis disponible et réponds sous 24h.</p>
      </div>
    </section>

    <section class="contact-section" id="contact">
      <div class="container contact-section__inner">

        <!-- ===== INFOS DE CONTACT ===== -->
        <div class="contact-info">
          <div class="section-header section-header--left">
            <div class="section-tag">Me joindre</div>
            <h2 class="section-title">Coordonnées</h2>
          </div>

          <p class="contact-info__text">
            Vous avez un projet à réaliser ou souhaitez juste discuter ?
            N'hésitez pas, je réponds généralement sous 24h.
          </p>

          <ul class="contact-info__list">
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <span>boubacarnanky@gmail.com</span>
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <span>Dakar, Sénégal</span>
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 8.33a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 15.16z"/>
              </svg>
              <span>+221 77 091 46 23</span>
            </li>
          </ul>

          <div class="contact-info__social">
            <a href="https://github.com/boubacarnanky-pixel" target="_blank" rel="noopener" class="social-icon" aria-label="GitHub">
              <i class="fa-brands fa-github"></i>
            </a>
            <a href="https://linkedin.com/in/scanf-boubacar" target="_blank" rel="noopener" class="social-icon" aria-label="LinkedIn">
              <i class="fa-brands fa-linkedin"></i>
            </a>
            <a href="https://www.instagram.com/benz_talent/#" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
              <i class="fa-brands fa-instagram"></i>
            </a>
          </div>
        </div>

        <!-- ===== FORMULAIRES AVEC ONGLETS CSS ===== -->
        <div class="forms-wrapper">

          <input type="radio" name="tab-ctrl" id="tab-contact"
                 class="tab-radio"
                 <?php echo ($onglet_actif === 'contact') ? 'checked' : ''; ?>>
          <input type="radio" name="tab-ctrl" id="tab-projet"
                 class="tab-radio"
                 <?php echo ($onglet_actif === 'projet') ? 'checked' : ''; ?>>

          <div class="form-tabs">
            <label for="tab-contact" class="tab-label tab-label--contact">
              <i class="fa fa-envelope"></i> Me contacter
            </label>
            <label for="tab-projet" class="tab-label tab-label--projet">
              <i class="fa fa-rocket"></i> Demande de projet
            </label>
          </div>

          <!-- ===== PANNEAU 1 : Me contacter ===== -->
          <div class="form-panel form-panel--contact">

            <?php if ($succes_contact) : ?>
            <div class="alert alert--success">
              <i class="fa fa-circle-check"></i>
              <div>
                <strong>Message envoyé avec succès !</strong>
                <p>Merci pour votre message. Je vous répondrai dans les plus brefs délais.</p>
              </div>
            </div>
            <?php endif; ?>

            <?php if (isset($erreurs_contact['global'])) : ?>
            <div class="alert alert--error">
              <i class="fa fa-circle-xmark"></i>
              <div><strong><?php echo $erreurs_contact['global']; ?></strong></div>
            </div>
            <?php endif; ?>

            <form class="contact-form" action="contact.php#contact" method="post" novalidate>
              <input type="hidden" name="formulaire" value="contact">

              <!--
                TOKEN CSRF — champ caché obligatoire
                generer_token_csrf() :
                  1. Crée un token aléatoire (si pas déjà en session)
                  2. Le stocke dans $_SESSION['csrf_token']
                  3. Le retourne pour l'afficher dans le <input>
                À la soumission, verifier_token_csrf() compare
                ce token avec celui de la session.
              -->
              <input type="hidden" name="csrf_token"
                     value="<?php echo generer_token_csrf(); ?>">

              <div class="form-row">
                <div class="form-group <?php echo isset($erreurs_contact['nom']) ? 'form-group--error' : ''; ?>">
                  <label for="nom">Nom complet <span class="required">*</span></label>
                  <input type="text" id="nom" name="nom"
                         placeholder="Votre nom"
                         value="<?php echo htmlspecialchars($donnees_contact['nom'], ENT_QUOTES, 'UTF-8'); ?>"
                         required autocomplete="name">
                  <?php if (isset($erreurs_contact['nom'])) : ?>
                    <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_contact['nom']; ?></span>
                  <?php endif; ?>
                </div>

                <div class="form-group <?php echo isset($erreurs_contact['email']) ? 'form-group--error' : ''; ?>">
                  <label for="email">Email <span class="required">*</span></label>
                  <input type="email" id="email" name="email"
                         placeholder="votre@email.com"
                         value="<?php echo htmlspecialchars($donnees_contact['email'], ENT_QUOTES, 'UTF-8'); ?>"
                         required autocomplete="email">
                  <?php if (isset($erreurs_contact['email'])) : ?>
                    <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_contact['email']; ?></span>
                  <?php endif; ?>
                </div>
              </div>

              <div class="form-group <?php echo isset($erreurs_contact['sujet']) ? 'form-group--error' : ''; ?>">
                <label for="sujet">Sujet <span class="required">*</span></label>
                <input type="text" id="sujet" name="sujet"
                       placeholder="Objet du message"
                       value="<?php echo htmlspecialchars($donnees_contact['sujet'], ENT_QUOTES, 'UTF-8'); ?>"
                       required>
                <?php if (isset($erreurs_contact['sujet'])) : ?>
                  <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_contact['sujet']; ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php echo isset($erreurs_contact['message']) ? 'form-group--error' : ''; ?>">
                <label for="message">Message <span class="required">*</span></label>
                <textarea id="message" name="message" rows="5"  
                          placeholder="Votre message..."
                          required><?php echo htmlspecialchars($donnees_contact['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($erreurs_contact['message'])) : ?>
                  <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_contact['message']; ?></span>
                <?php endif; ?>
              </div>

              <p class="form-required-note"><span class="required">*</span> Champs obligatoires</p>

              <button type="submit" class="btn btn--primary btn--full">
                <i class="fa fa-paper-plane"></i> Envoyer le message
              </button>
            </form>
          </div>

          <!-- ===== PANNEAU 2 : Demande de projet ===== -->
          <div class="form-panel form-panel--projet">

            <?php if ($succes_projet) : ?>
            <div class="alert alert--success">
              <i class="fa fa-circle-check"></i>
              <div>
                <strong>Demande envoyée avec succès !</strong>
                <p>Je vous recontacte rapidement pour discuter de votre projet.</p>
              </div>
            </div>

            <div class="recap-card">
              <h3 class="recap-card__title">
                <i class="fa fa-clipboard-list"></i> Récapitulatif de votre demande
              </h3>
              <dl class="recap-card__list">
                <?php foreach ($recap_projet as $label => $valeur) : ?>
                <div class="recap-card__item">
                  <dt><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></dt>
                  <dd><?php echo nl2br(htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8')); ?></dd>
                </div>
                <?php endforeach; ?>
              </dl>
            </div>

            <?php else : ?>

            <?php if (isset($erreurs_projet['global'])) : ?>
            <div class="alert alert--error">
              <i class="fa fa-circle-xmark"></i>
              <div><strong><?php echo $erreurs_projet['global']; ?></strong></div>
            </div>
            <?php endif; ?>

            <form class="contact-form" action="contact.php#contact" method="post" novalidate>
              <input type="hidden" name="formulaire" value="projet">

              <!-- TOKEN CSRF — identique au formulaire 1 -->
              <input type="hidden" name="csrf_token"
                     value="<?php echo generer_token_csrf(); ?>">

              <div class="form-row">
                <div class="form-group <?php echo isset($erreurs_projet['p_nom']) ? 'form-group--error' : ''; ?>">
                  <label for="p_nom">Nom / Entreprise <span class="required">*</span></label>
                  <input type="text" id="p_nom" name="p_nom"
                         placeholder="Nom ou raison sociale"
                         value="<?php echo htmlspecialchars($donnees_projet['p_nom'], ENT_QUOTES, 'UTF-8'); ?>"
                         required autocomplete="organization">
                  <?php if (isset($erreurs_projet['p_nom'])) : ?>
                    <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_projet['p_nom']; ?></span>
                  <?php endif; ?>
                </div>

                <div class="form-group <?php echo isset($erreurs_projet['p_email']) ? 'form-group--error' : ''; ?>">
                  <label for="p_email">Email <span class="required">*</span></label>
                  <input type="email" id="p_email" name="p_email"
                         placeholder="votre@email.com"
                         value="<?php echo htmlspecialchars($donnees_projet['p_email'], ENT_QUOTES, 'UTF-8'); ?>"
                         required autocomplete="email">
                  <?php if (isset($erreurs_projet['p_email'])) : ?>
                    <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_projet['p_email']; ?></span>
                  <?php endif; ?>
                </div>
              </div>

              <div class="form-group <?php echo isset($erreurs_projet['p_type']) ? 'form-group--error' : ''; ?>">
                <label for="p_type">Type de projet <span class="required">*</span></label>
                <select id="p_type" name="p_type" required>
                  <option value="">— Sélectionnez un type —</option>
                  <?php
                  $types = [
                      'site-vitrine'    => 'Site vitrine',
                      'e-commerce'      => 'E-commerce',
                      'application-web' => 'Application web',
                      'autre'           => 'Autre',
                  ];
                  foreach ($types as $val => $label) :
                      $selected = ($donnees_projet['p_type'] === $val) ? ' selected' : '';
                  ?>
                  <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $label; ?></option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($erreurs_projet['p_type'])) : ?>
                  <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_projet['p_type']; ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php echo isset($erreurs_projet['p_budget']) ? 'form-group--error' : ''; ?>">
                <label for="p_budget">Budget estimé <span class="form-optional">(facultatif)</span></label>
                <select id="p_budget" name="p_budget">
                  <option value="">— Sélectionnez un budget —</option>
                  <?php
                  $budgets = [
                      'moins-100k' => 'Moins de 100 000 FCFA',
                      '100-300k'   => '100 000 — 300 000 FCFA',
                      'plus-300k'  => 'Plus de 300 000 FCFA',
                  ];
                  foreach ($budgets as $val => $label) :
                      $selected = ($donnees_projet['p_budget'] === $val) ? ' selected' : '';
                  ?>
                  <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $label; ?></option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($erreurs_projet['p_budget'])) : ?>
                  <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_projet['p_budget']; ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php echo isset($erreurs_projet['p_desc']) ? 'form-group--error' : ''; ?>">
                <label for="p_desc">Description du projet <span class="required">*</span></label>
                <textarea id="p_desc" name="p_desc" rows="5"
                          placeholder="Décrivez votre projet en détail (objectifs, fonctionnalités souhaitées, délai…)"
                          required><?php echo htmlspecialchars($donnees_projet['p_desc'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($erreurs_projet['p_desc'])) : ?>
                  <span class="form-error"><i class="fa fa-triangle-exclamation"></i> <?php echo $erreurs_projet['p_desc']; ?></span>
                <?php endif; ?>
              </div>

              <p class="form-required-note"><span class="required">*</span> Champs obligatoires</p>

              <button type="submit" class="btn btn--primary btn--full">
                <i class="fa fa-rocket"></i> Soumettre ma demande
              </button>
            </form>

            <?php endif; ?>
          </div>

        </div><!-- /.forms-wrapper -->

      </div>
    </section>

  </main>

  <?php require 'composants/footer.php'; ?>

</body>
</html>
