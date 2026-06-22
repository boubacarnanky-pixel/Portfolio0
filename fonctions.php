<?php
/* ============================================================
   FONCTIONS.PHP — Fonctions réutilisables
   Portfolio Boubacar Nanky — Partie 2 PHP
   ============================================================ */

/**
 * Nettoie une valeur entrée par l'utilisateur
 * Supprime les espaces, échappe les caractères HTML
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie qu'un champ requis n'est pas vide
 * Retourne true si valide, false sinon
 */
function champ_requis(string $valeur): bool {
    return trim($valeur) !== '';
}

/**
 * Valide une adresse email
 */
function email_valide(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide la longueur minimale d'une chaîne
 */
function longueur_min(string $valeur, int $min): bool {
    return mb_strlen(trim($valeur)) >= $min;
}

/**
 * Valide la longueur maximale d'une chaîne
 */
function longueur_max(string $valeur, int $max): bool {
    return mb_strlen(trim($valeur)) <= $max;
}

/**
 * Récupère et nettoie une valeur POST
 * Retourne une chaîne vide si la clé n'existe pas
 */
function get_post(string $cle): string {
    return isset($_POST[$cle]) ? nettoyer($_POST[$cle]) : '';
}

/**
 * Récupère une valeur POST brute (pour textarea, avant affichage)
 * Retourne une chaîne vide si la clé n'existe pas
 */
function get_post_raw(string $cle): string {
    return isset($_POST[$cle]) ? trim($_POST[$cle]) : '';
}

/**
 * Valide le formulaire de contact simple
 * Retourne un tableau ['erreurs' => [], 'donnees' => []]
 */
function valider_formulaire_contact(array $post): array {
    $erreurs = [];
    $donnees = [];

    // Nom
    $nom = get_post('nom');
    if (!champ_requis($nom)) {
        $erreurs['nom'] = 'Le nom est obligatoire.';
    } elseif (!longueur_min($nom, 2)) {
        $erreurs['nom'] = 'Le nom doit contenir au moins 2 caractères.';
    } elseif (!longueur_max($nom, 80)) {
        $erreurs['nom'] = 'Le nom ne doit pas dépasser 80 caractères.';
    }
    $donnees['nom'] = $nom;

    // Email
    $email = get_post('email');
    if (!champ_requis($email)) {
        $erreurs['email'] = 'L\'email est obligatoire.';
    } elseif (!email_valide($email)) {
        $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
    }
    $donnees['email'] = $email;

    // Sujet
    $sujet = get_post('sujet');
    if (!champ_requis($sujet)) {
        $erreurs['sujet'] = 'Le sujet est obligatoire.';
    } elseif (!longueur_min($sujet, 3)) {
        $erreurs['sujet'] = 'Le sujet doit contenir au moins 3 caractères.';
    }
    $donnees['sujet'] = $sujet;

    // Message
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
 * Valide le formulaire de demande de projet
 * Retourne un tableau ['erreurs' => [], 'donnees' => []]
 */
function valider_formulaire_projet(array $post): array {
    $erreurs = [];
    $donnees = [];

    $types_valides  = ['site-vitrine', 'e-commerce', 'application-web', 'autre'];
    $budgets_valides = ['moins-100k', '100-300k', 'plus-300k'];

    // Nom / Entreprise
    $p_nom = get_post('p_nom');
    if (!champ_requis($p_nom)) {
        $erreurs['p_nom'] = 'Le nom ou l\'entreprise est obligatoire.';
    } elseif (!longueur_min($p_nom, 2)) {
        $erreurs['p_nom'] = 'Le nom doit contenir au moins 2 caractères.';
    }
    $donnees['p_nom'] = $p_nom;

    // Email
    $p_email = get_post('p_email');
    if (!champ_requis($p_email)) {
        $erreurs['p_email'] = 'L\'email est obligatoire.';
    } elseif (!email_valide($p_email)) {
        $erreurs['p_email'] = 'L\'adresse email n\'est pas valide.';
    }
    $donnees['p_email'] = $p_email;

    // Type de projet
    $p_type = get_post('p_type');
    if (!champ_requis($p_type) || !in_array($p_type, $types_valides)) {
        $erreurs['p_type'] = 'Veuillez sélectionner un type de projet valide.';
    }
    $donnees['p_type'] = $p_type;

    // Budget (facultatif mais doit être valide si fourni)
    $p_budget = get_post('p_budget');
    if (champ_requis($p_budget) && !in_array($p_budget, $budgets_valides)) {
        $erreurs['p_budget'] = 'Veuillez sélectionner un budget valide.';
    }
    $donnees['p_budget'] = $p_budget;

    // Description
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
 * Retourne le libellé lisible d'un type de projet
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
 * Retourne le libellé lisible d'un budget
 */
function libelle_budget(string $budget): string {
    $budgets = [
        'moins-100k' => 'Moins de 100 000 FCFA',
        '100-300k'   => '100 000 — 300 000 FCFA',
        'plus-300k'  => 'Plus de 300 000 FCFA',
    ];
    return $budgets[$budget] ?? 'Non spécifié';
}
