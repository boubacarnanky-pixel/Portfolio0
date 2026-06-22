<?php
/* ============================================================
   FONCTIONS.PHP — Fonctions réutilisables
   Portfolio Boubacar Nanky
   Partie 2 → Partie 3 : ajout CSRF + journalisation visites
   ============================================================ */

/* ============================================================
   PARTIE 2 — Validation des formulaires (inchangée)
   ============================================================ */

/**
 * Nettoie une valeur entrée par l'utilisateur.
 * Supprime les espaces et échappe les caractères HTML.
 * → Protection XSS
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie qu'un champ requis n'est pas vide.
 */
function champ_requis(string $valeur): bool {
    return trim($valeur) !== '';
}

/**
 * Valide une adresse email avec le filtre natif PHP.
 */
function email_valide(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Vérifie la longueur minimale d'une chaîne (en caractères Unicode).
 */
function longueur_min(string $valeur, int $min): bool {
    return mb_strlen(trim($valeur)) >= $min;
}

/**
 * Vérifie la longueur maximale d'une chaîne (en caractères Unicode).
 */
function longueur_max(string $valeur, int $max): bool {
    return mb_strlen(trim($valeur)) <= $max;
}

/**
 * Récupère et nettoie une valeur $_POST.
 * Retourne une chaîne vide si la clé est absente.
 */
function get_post(string $cle): string {
    return isset($_POST[$cle]) ? nettoyer($_POST[$cle]) : '';
}

/**
 * Récupère une valeur $_POST brute (avant affichage dans textarea).
 * Retourne une chaîne vide si la clé est absente.
 */
function get_post_raw(string $cle): string {
    return isset($_POST[$cle]) ? trim($_POST[$cle]) : '';
}

/**
 * Valide le formulaire de contact simple.
 * Retourne ['erreurs' => [...], 'donnees' => [...]]
 */
function valider_formulaire_contact(array $post): array {
    $erreurs = [];
    $donnees = [];

    $nom = get_post('nom');
    if (!champ_requis($nom)) {
        $erreurs['nom'] = 'Le nom est obligatoire.';
    } elseif (!longueur_min($nom, 2)) {
        $erreurs['nom'] = 'Le nom doit contenir au moins 2 caractères.';
    } elseif (!longueur_max($nom, 80)) {
        $erreurs['nom'] = 'Le nom ne doit pas dépasser 80 caractères.';
    }
    $donnees['nom'] = $nom;

    $email = get_post('email');
    if (!champ_requis($email)) {
        $erreurs['email'] = 'L\'email est obligatoire.';
    } elseif (!email_valide($email)) {
        $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
    }
    $donnees['email'] = $email;

    $sujet = get_post('sujet');
    if (!champ_requis($sujet)) {
        $erreurs['sujet'] = 'Le sujet est obligatoire.';
    } elseif (!longueur_min($sujet, 3)) {
        $erreurs['sujet'] = 'Le sujet doit contenir au moins 3 caractères.';
    }
    $donnees['sujet'] = $sujet;

    $message = get_post('message');
    if (!champ_requis($message)) {
        $erreurs['message'] = 'Le message est obligatoire.';
    } elseif (!longueur_min($message, 10)) {
        $erreurs['message'] = 'Le message doit contenir au moins 10 caractères.';
    } elseif (!longueur_max($message, 2000)) {
        $erreurs['message'] = 'Le message ne doit pas dépasser 2000 caractères.';
    }
    $donnees['message'] = $message;

    return ['erreurs' => $erreurs, 'donnees' => $donnees];
}

/**
 * Valide le formulaire de demande de projet.
 * Retourne ['erreurs' => [...], 'donnees' => [...]]
 */
function valider_formulaire_projet(array $post): array {
    $erreurs = [];
    $donnees = [];

    $types_valides   = ['site-vitrine', 'e-commerce', 'application-web', 'autre'];
    $budgets_valides = ['moins-100k', '100-300k', 'plus-300k'];

    $p_nom = get_post('p_nom');
    if (!champ_requis($p_nom)) {
        $erreurs['p_nom'] = 'Le nom ou l\'entreprise est obligatoire.';
    } elseif (!longueur_min($p_nom, 2)) {
        $erreurs['p_nom'] = 'Le nom doit contenir au moins 2 caractères.';
    }
    $donnees['p_nom'] = $p_nom;

    $p_email = get_post('p_email');
    if (!champ_requis($p_email)) {
        $erreurs['p_email'] = 'L\'email est obligatoire.';
    } elseif (!email_valide($p_email)) {
        $erreurs['p_email'] = 'L\'adresse email n\'est pas valide.';
    }
    $donnees['p_email'] = $p_email;

    $p_type = get_post('p_type');
    if (!champ_requis($p_type) || !in_array($p_type, $types_valides)) {
        $erreurs['p_type'] = 'Veuillez sélectionner un type de projet valide.';
    }
    $donnees['p_type'] = $p_type;

    $p_budget = get_post('p_budget');
    if (champ_requis($p_budget) && !in_array($p_budget, $budgets_valides)) {
        $erreurs['p_budget'] = 'Veuillez sélectionner un budget valide.';
    }
    $donnees['p_budget'] = $p_budget;

    $p_desc = get_post('p_desc');
    if (!champ_requis($p_desc)) {
        $erreurs['p_desc'] = 'La description du projet est obligatoire.';
    } elseif (!longueur_min($p_desc, 20)) {
        $erreurs['p_desc'] = 'La description doit contenir au moins 20 caractères.';
    } elseif (!longueur_max($p_desc, 3000)) {
        $erreurs['p_desc'] = 'La description ne doit pas dépasser 3000 caractères.';
    }
    $donnees['p_desc'] = $p_desc;

    return ['erreurs' => $erreurs, 'donnees' => $donnees];
}

/**
 * Retourne le libellé lisible d'un type de projet.
 */
function libelle_type_projet(string $type): string {
    $types = [
        'site-vitrine'    => 'Site vitrine',
        'e-commerce'      => 'E-commerce',
        'application-web' => 'Application web',
        'autre'           => 'Autre',
    ];
    return $types[$type] ?? $type;
}

/**
 * Retourne le libellé lisible d'un budget.
 */
function libelle_budget(string $budget): string {
    $budgets = [
        'moins-100k' => 'Moins de 100 000 FCFA',
        '100-300k'   => '100 000 — 300 000 FCFA',
        'plus-300k'  => 'Plus de 300 000 FCFA',
    ];
    return $budgets[$budget] ?? 'Non spécifié';
}


/* ============================================================
   PARTIE 3 — Sécurité : CSRF + Sessions + Journalisation
   ============================================================ */

/**
 * GÉNÈRE un jeton CSRF et le stocke en session.
 *
 * Fonctionnement :
 *  1. On vérifie si un token existe déjà dans la session.
 *  2. S'il n'existe pas, on en crée un avec random_bytes(32)
 *     → 32 octets aléatoires cryptographiquement sûrs
 *     → convertis en hexadécimal (64 caractères)
 *  3. On le retourne pour l'injecter dans le formulaire HTML.
 *
 * Pourquoi random_bytes et pas rand() ?
 *  rand() est prévisible. random_bytes() utilise le générateur
 *  aléatoire du système d'exploitation, impossible à prédire.
 *
 * @return string Token CSRF (64 caractères hexadécimaux)
 */
function generer_token_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * VÉRIFIE le jeton CSRF soumis par le formulaire.
 *
 * Fonctionnement :
 *  1. On récupère le token du formulaire ($_POST['csrf_token'])
 *  2. On compare avec celui stocké en session
 *  3. On utilise hash_equals() et non == ou ===
 *
 * Pourquoi hash_equals() et pas === ?
 *  L'opérateur === est vulnérable aux "timing attacks" :
 *  un pirate peut mesurer le temps de comparaison caractère
 *  par caractère pour deviner le token progressivement.
 *  hash_equals() prend toujours le même temps, quelle que
 *  soit la différence entre les deux chaînes.
 *
 * En cas d'échec : on arrête immédiatement avec HTTP 403.
 *
 * @return void  Stoppe l'exécution si le token est invalide
 */
function verifier_token_csrf(): void {
    $token_recu    = $_POST['csrf_token'] ?? '';
    $token_session = $_SESSION['csrf_token'] ?? '';

    /* Double vérification : token non vide ET identique */
    if (
        empty($token_recu)
        || empty($token_session)
        || !hash_equals($token_session, $token_recu)
    ) {
        /* On invalide la session pour forcer une nouvelle session propre */
        session_destroy();

        /* HTTP 403 Forbidden */
        http_response_code(403);
        die('
            <div style="
                font-family:sans-serif;
                max-width:480px;
                margin:4rem auto;
                padding:2rem;
                background:#fee2e2;
                border:1px solid #fca5a5;
                border-radius:8px;
                text-align:center;
            ">
                <h2 style="color:#991b1b;">⛔ Requête non autorisée</h2>
                <p style="color:#991b1b;">
                    Jeton de sécurité invalide ou expiré.<br>
                    <a href="contact.php" style="color:#991b1b;">
                        ← Retourner au formulaire
                    </a>
                </p>
            </div>
        ');
    }

    /*
     * Après vérification réussie : on régénère le token.
     * Chaque soumission valide utilise un token à usage unique.
     * → Protection contre les attaques par rejeu (replay attack)
     */
    unset($_SESSION['csrf_token']);
}

/**
 * JOURNALISE une visite dans la table MySQL `visites`.
 *
 * Fonctionnement :
 *  1. Récupère l'adresse IP du visiteur
 *  2. Récupère le nom de la page actuelle
 *  3. Insère une ligne dans la table visites via PDO
 *
 * Pourquoi journaliser ?
 *  - Montrer au professeur que le site est utilisé
 *  - Permettre des statistiques dans le dashboard admin
 *  - Exigence explicite du cahier des charges Partie 3
 *
 * Note sur l'IP : $_SERVER['REMOTE_ADDR'] peut retourner
 * l'IP du proxy sur certains hébergeurs. Pour XAMPP local,
 * ce sera toujours 127.0.0.1, ce qui est normal.
 *
 * @param PDO    $pdo  Instance PDO déjà connectée
 * @param string $page Nom de la page (ex: 'index', 'contact')
 * @return void
 */
function enregistrer_visite(PDO $pdo, string $page): void {
    /*
     * Récupération sécurisée de l'IP.
     * On nettoie avec filter_var pour éviter les injections
     * dans les logs (Log Injection attack).
     */
    $ip_brute = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ip       = filter_var($ip_brute, FILTER_VALIDATE_IP)
                    ? $ip_brute
                    : '0.0.0.0';

    /*
     * Requête préparée → jamais de données directement dans le SQL.
     * Les marqueurs :adresse_ip et :page sont remplacés de façon
     * sécurisée par PDO, sans risque d'injection SQL.
     */
    try {
        $sql  = 'INSERT INTO visites (adresse_ip, page) VALUES (:adresse_ip, :page)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':adresse_ip' => $ip,
            ':page'       => nettoyer($page),
        ]);
    } catch (PDOException $e) {
        /*
         * On ne bloque JAMAIS l'affichage d'une page à cause
         * d'une erreur de journalisation. On logge silencieusement.
         */
        error_log('[PORTFOLIO] Erreur journalisation visite : ' . $e->getMessage());
    }
}


/* ============================================================
   PARTIE 3 — Authentification admin
   ============================================================ */

/**
 * Récupère l'adresse IP du visiteur, validée.
 * Fonction extraite pour être réutilisée par le système
 * anti-brute-force (en plus de enregistrer_visite()).
 *
 * @return string Adresse IP (IPv4 ou IPv6), ou '0.0.0.0' si invalide
 */
function obtenir_ip_visiteur(): string {
    $ip_brute = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return filter_var($ip_brute, FILTER_VALIDATE_IP) ? $ip_brute : '0.0.0.0';
}

/**
 * COMPTE le nombre de tentatives de connexion échouées
 * pour une IP donnée, sur les 15 dernières minutes.
 *
 * Pourquoi 15 minutes et pas "toujours" ?
 * Si on comptait toutes les tentatives depuis le début des
 * temps, un utilisateur légitime qui s'est trompé plusieurs
 * fois il y a une semaine resterait pénalisé pour toujours.
 * On ne regarde donc qu'une fenêtre de temps glissante.
 *
 * @param PDO    $pdo Instance PDO
 * @param string $ip  Adresse IP à vérifier
 * @return int Nombre de tentatives échouées récentes
 */
function compter_tentatives_echouees(PDO $pdo, string $ip): int {
    try {
        $sql = '
            SELECT COUNT(*) AS total
            FROM tentatives_connexion
            WHERE adresse_ip = :ip
              AND date_tentative >= (NOW() - INTERVAL 15 MINUTE)
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ip' => $ip]);
        return (int) $stmt->fetch()['total'];
    } catch (PDOException $e) {
        error_log('[PORTFOLIO] Erreur comptage tentatives : ' . $e->getMessage());
        /* En cas d'erreur technique, on suppose 0 tentative
           plutôt que de bloquer un utilisateur légitime. */
        return 0;
    }
}

/**
 * CALCULE le délai d'attente (en secondes) avant la prochaine
 * tentative autorisée, en fonction du nombre d'échecs récents.
 *
 * Formule : délai = 2^(tentatives - 1), avec un plafond.
 *   1 échec  → 0 seconde  (premier essai toujours libre)
 *   2 échecs → 1 seconde
 *   3 échecs → 2 secondes
 *   4 échecs → 4 secondes
 *   5 échecs → 8 secondes
 *   ...
 *   plafonné à 300 secondes (5 minutes) pour rester raisonnable
 *
 * @param int $nb_echecs Nombre de tentatives échouées récentes
 * @return int Délai en secondes avant la prochaine tentative
 */
function calculer_delai_attente(int $nb_echecs): int {
    if ($nb_echecs <= 1) {
        return 0;
    }
    $delai = (int) pow(2, $nb_echecs - 1);
    return min($delai, 300); // plafond de sécurité : 5 minutes max
}

/**
 * ENREGISTRE une tentative de connexion échouée pour une IP.
 *
 * @param PDO    $pdo Instance PDO
 * @param string $ip  Adresse IP de la tentative
 * @return void
 */
function enregistrer_tentative_echouee(PDO $pdo, string $ip): void {
    try {
        $sql  = 'INSERT INTO tentatives_connexion (adresse_ip) VALUES (:ip)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ip' => $ip]);
    } catch (PDOException $e) {
        error_log('[PORTFOLIO] Erreur enregistrement tentative : ' . $e->getMessage());
    }
}

/**
 * RÉINITIALISE les tentatives échouées d'une IP après une
 * connexion réussie — l'utilisateur n'a plus besoin d'être
 * pénalisé une fois correctement authentifié.
 *
 * @param PDO    $pdo Instance PDO
 * @param string $ip  Adresse IP à nettoyer
 * @return void
 */
function reinitialiser_tentatives(PDO $pdo, string $ip): void {
    try {
        $sql  = 'DELETE FROM tentatives_connexion WHERE adresse_ip = :ip';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ip' => $ip]);
    } catch (PDOException $e) {
        error_log('[PORTFOLIO] Erreur réinitialisation tentatives : ' . $e->getMessage());
    }
}

/**
 * VÉRIFIE si l'administrateur est actuellement connecté.
 * Utilisée par auth.php (garde d'accès) et par les pages
 * publiques qui veulent adapter leur affichage (rare ici).
 *
 * @return bool true si une session admin valide existe
 */
function est_connecte(): bool {
    return isset($_SESSION['admin_id'])
        && isset($_SESSION['admin_email'])
        && !empty($_SESSION['admin_id']);
}


/* ============================================================
   PARTIE 3 — Upload sécurisé d'image (CRUD projets)
   ============================================================ */

/**
 * TRAITE l'upload d'une image de projet de façon sécurisée.
 *
 * Étapes de sécurité appliquées :
 *   1. Vérifie qu'un fichier a bien été envoyé sans erreur
 *   2. Vérifie le TYPE RÉEL du fichier via finfo (pas l'extension,
 *      qu'un attaquant peut facilement falsifier)
 *   3. Vérifie la taille (max 3 Mo)
 *   4. Génère un nom de fichier aléatoire pour le stockage
 *      → empêche l'écrasement accidentel d'un fichier existant
 *      → empêche l'injection de caractères dangereux dans le nom
 *   5. Déplace le fichier avec move_uploaded_file() (et non
 *      copy() ou rename() — cette fonction vérifie en plus que
 *      le fichier provient bien d'un upload HTTP légitime)
 *
 * @param array $fichier Le tableau $_FILES['nom_du_champ']
 * @return array ['succes' => bool, 'chemin' => string|null, 'erreur' => string|null]
 */
function traiter_upload_image(array $fichier): array {

    /* Aucun fichier sélectionné — pas une erreur si l'image
       est optionnelle (ex: lors d'une modification) */
    if ($fichier['error'] === UPLOAD_ERR_NO_FILE) {
        return ['succes' => true, 'chemin' => null, 'erreur' => null];
    }

    /* Erreur d'upload (fichier trop gros pour le serveur, etc.) */
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        return ['succes' => false, 'chemin' => null, 'erreur' => 'Erreur lors de l\'envoi du fichier.'];
    }

    /* Limite de taille : 3 Mo */
    $taille_max = 3 * 1024 * 1024;
    if ($fichier['size'] > $taille_max) {
        return ['succes' => false, 'chemin' => null, 'erreur' => 'L\'image ne doit pas dépasser 3 Mo.'];
    }

    /*
     * Vérification du TYPE RÉEL du fichier.
     * finfo_file() lit les premiers octets du fichier (sa
     * "signature binaire") plutôt que de se fier à l'extension
     * ou au champ 'type' envoyé par le navigateur — les deux
     * sont falsifiables par un attaquant.
     */
    $types_autorises = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    $finfo      = finfo_open(FILEINFO_MIME_TYPE);
    $type_reel  = finfo_file($finfo, $fichier['tmp_name']);
    finfo_close($finfo);

    if (!isset($types_autorises[$type_reel])) {
        return ['succes' => false, 'chemin' => null, 'erreur' => 'Format d\'image non autorisé (JPG, PNG ou WEBP uniquement).'];
    }

    /*
     * Génération d'un nom de fichier ALÉATOIRE et sûr.
     * On ignore complètement le nom d'origine du fichier
     * (ex: "../../etc/passwd.jpg" serait dangereux si on
     * le réutilisait tel quel).
     */
    $extension     = $types_autorises[$type_reel];
    $nom_fichier   = bin2hex(random_bytes(16)) . '.' . $extension;
    $dossier_cible = __DIR__ . '/images/projets/';
    $chemin_cible  = $dossier_cible . $nom_fichier;

    /* Crée le dossier de destination s'il n'existe pas encore */
    if (!is_dir($dossier_cible)) {
        mkdir($dossier_cible, 0755, true);
    }

    /*
     * move_uploaded_file() au lieu de rename()/copy() :
     * cette fonction vérifie en plus que le fichier source
     * provient bien d'un upload HTTP réel (via is_uploaded_file
     * en interne), ce qui bloque certaines attaques par
     * manipulation de chemin de fichier.
     */
    if (!move_uploaded_file($fichier['tmp_name'], $chemin_cible)) {
        return ['succes' => false, 'chemin' => null, 'erreur' => 'Impossible d\'enregistrer l\'image sur le serveur.'];
    }

    /* Chemin relatif stocké en base, utilisable directement dans un <img src="..."> */
    return ['succes' => true, 'chemin' => 'images/projets/' . $nom_fichier, 'erreur' => null];
}

/**
 * SUPPRIME un fichier image du disque, en toute sécurité.
 * Utilisée lors de la suppression/modification d'un projet
 * pour ne pas accumuler des fichiers orphelins sur le serveur.
 *
 * @param string|null $chemin_relatif Chemin stocké en base (ex: images/projets/abc.jpg)
 * @return void
 */
function supprimer_fichier_image(?string $chemin_relatif): void {
    if (empty($chemin_relatif)) {
        return;
    }

    $chemin_absolu = __DIR__ . '/' . $chemin_relatif;

    /*
     * Vérification de sécurité : on s'assure que le chemin
     * résolu reste BIEN à l'intérieur du dossier images/projets/
     * avant de supprimer quoi que ce soit. Ça empêche un chemin
     * malveillant du type "../../config/connexion.php" de
     * provoquer la suppression d'un fichier sensible.
     */
    $dossier_autorise = realpath(__DIR__ . '/images/projets');
    $cible_reelle      = realpath($chemin_absolu);

    if (
        $cible_reelle !== false
        && $dossier_autorise !== false
        && str_starts_with($cible_reelle, $dossier_autorise)
        && file_exists($cible_reelle)
    ) {
        unlink($cible_reelle);
    }
}


/* ============================================================
   PARTIE 3 — CRUD Administrateurs : règles de sécurité
   ============================================================ */

/**
 * COMPTE le nombre total d'administrateurs en base.
 * Utilisée pour empêcher la suppression du DERNIER admin
 * restant — le système ne doit jamais se retrouver sans
 * aucun compte capable de s'y connecter.
 *
 * @param PDO $pdo Instance PDO
 * @return int Nombre total d'administrateurs
 */
function compter_administrateurs(PDO $pdo): int {
    try {
        return (int) $pdo->query('SELECT COUNT(*) AS total FROM administrateurs')->fetch()['total'];
    } catch (PDOException $e) {
        error_log('[PORTFOLIO][ADMIN] Erreur comptage administrateurs : ' . $e->getMessage());
        /* En cas de doute, on retourne une valeur qui BLOQUE la
           suppression plutôt que de risquer de vider la table */
        return 1;
    }
}


