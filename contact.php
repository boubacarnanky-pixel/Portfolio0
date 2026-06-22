<?php
/* ============================================================
   contact.php — Page Contact avec deux formulaires PHP
   Portfolio Boubacar Nanky — Partie 2 PHP

   Formulaire 1 : "Me contacter"      → validation + message succès
   Formulaire 2 : "Demande de projet" → validation + récapitulatif
   ============================================================ */
$page_active = 'contact';

require 'fonctions.php';

/* ---- État des formulaires ---- */
$erreurs_contact = [];
$donnees_contact = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
$succes_contact  = false;

$erreurs_projet  = [];
$donnees_projet  = ['p_nom' => '', 'p_email' => '', 'p_type' => '', 'p_budget' => '', 'p_desc' => ''];
$succes_projet   = false;
$recap_projet    = [];

/* Quel onglet afficher par défaut ou après soumission */
$onglet_actif = 'contact'; // 'contact' ou 'projet'

/* ============================================================
   TRAITEMENT FORMULAIRE 1 — Me contacter
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formulaire']) && $_POST['formulaire'] === 'contact') {

    $onglet_actif = 'contact';
    $resultat = valider_formulaire_contact($_POST);
    $erreurs_contact = $resultat['erreurs'];
    $donnees_contact = $resultat['donnees'];

    if (empty($erreurs_contact)) {
        /*
         * Ici, en production, on enverrait l'email avec mail()
         * Pour le projet universitaire, on affiche le message de succès
         */
        $succes_contact = true;
        /* Réinitialiser les champs après succès */
        $donnees_contact = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
    }
}

/* ============================================================
   TRAITEMENT FORMULAIRE 2 — Demande de projet
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formulaire']) && $_POST['formulaire'] === 'projet') {

    $onglet_actif = 'projet';
    $resultat = valider_formulaire_projet($_POST);
    $erreurs_projet  = $resultat['erreurs'];
    $donnees_projet  = $resultat['donnees'];

    if (empty($erreurs_projet)) {
        $succes_projet = true;
        /* Récapitulatif dans un tableau associatif */
        $recap_projet = [
            'Nom / Entreprise'   => $donnees_projet['p_nom'],
            'Email'              => $donnees_projet['p_email'],
            'Type de projet'     => libelle_type_projet($donnees_projet['p_type']),
            'Budget estimé'      => champ_requis($donnees_projet['p_budget'])
                                     ? libelle_budget($donnees_projet['p_budget'])
                                     : 'Non spécifié',
            'Description'        => $donnees_projet['p_desc'],
        ];
        /* Réinitialiser les champs après succès */
        $donnees_projet = ['p_nom' => '', 'p_email' => '', 'p_type' => '', 'p_budget' => '', 'p_desc' => ''];
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

  <?php require 'composants/navigation.php'; ?>

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
            <a href="https://linkedin.com" target="_blank" rel="noopener" class="social-icon" aria-label="LinkedIn">
              <i class="fa-brands fa-linkedin"></i>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
              <i class="fa-brands fa-instagram"></i>
            </a>
          </div>
        </div>

        <!-- ===== FORMULAIRES AVEC ONGLETS CSS (radio buttons) ===== -->
        <div class="forms-wrapper">

          <!-- Radios de contrôle des onglets (CSS uniquement, sans JavaScript) -->
          <input type="radio" name="tab-ctrl" id="tab-contact"
                 class="tab-radio"
                 <?php echo ($onglet_actif === 'contact') ? 'checked' : ''; ?>>
          <input type="radio" name="tab-ctrl" id="tab-projet"
                 class="tab-radio"
                 <?php echo ($onglet_actif === 'projet') ? 'checked' : ''; ?>>

          <!-- Onglets -->
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
            <!-- Message de succès -->
            <div class="alert alert--success">
              <i class="fa fa-circle-check"></i>
              <div>
                <strong>Message envoyé avec succès !</strong>
                <p>Merci pour votre message. Je vous répondrai dans les plus brefs délais.</p>
              </div>
            </div>
            <?php endif; ?>

            <form class="contact-form" action="contact.php#contact" method="post" novalidate>
              <input type="hidden" name="formulaire" value="contact">

              <div class="form-row">
                <!-- Nom -->
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

                <!-- Email -->
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

              <!-- Sujet -->
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

              <!-- Message -->
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
            <!-- Récapitulatif de la demande -->
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

            <form class="contact-form" action="contact.php#contact" method="post" novalidate>
              <input type="hidden" name="formulaire" value="projet">

              <div class="form-row">
                <!-- Nom / Entreprise -->
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

                <!-- Email -->
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

              <!-- Type de projet -->
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

              <!-- Budget -->
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

              <!-- Description -->
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
