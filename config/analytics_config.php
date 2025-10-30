<?php

/**
 * Configuration Analytics & RGPD
 * 
 * Ce fichier centralise tous les paramètres d'exclusion et de conformité RGPD
 * pour le système de tracking et d'analytics.
 */

return [
    // ===== EXCLUSIONS DE TRAFIC =====
    
    /**
     * Rôles utilisateurs à exclure du tracking
     * Les utilisateurs connectés avec ces rôles ne seront JAMAIS trackés
     */
    'blocked_roles' => [
        'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN',
    ],
    
    /**
     * Routes privées (regex) - AUCUN tracking sur ces pages
     * Les patterns sont testés avec preg_match
     */
    'private_paths' => [
        '/^\/admin/',
        '/^\/backoffice/',
        '/^\/dashboard/',
        '/^\/api\//',
        '/^\/_profiler/',
        '/^\/_wdt/',
    ],
    
    /**
     * Routes publiques autorisées (whitelist)
     * Si non vide, SEULES ces routes seront trackées
     * Si vide, toutes les routes SAUF private_paths sont trackées
     */
    'allowed_public_paths' => [
        // Exemple : '/^\/formation/', '/^\/metiers/', '/^\/blog/'
        // Laisser vide pour tracker toutes les pages publiques
    ],
    
    /**
     * IP publiques à exclure du tracking
     * IMPORTANT : Ce sont vos IP PUBLIQUES (pas 192.168.x.x qui sont locales)
     * Pour connaître votre IP publique : https://www.whatismyip.com/
     */
    'excluded_public_ips' => [
        // Exemple : '203.0.113.45', '198.51.100.78'
        // À compléter avec VOTRE IP publique
    ],
    
    /**
     * Environnements à exclure du tracking
     */
    'excluded_environments' => [
        'dev',
        'test',
        'staging',
    ],
    
    /**
     * Sous-domaines à exclure (préprod, staging, etc.)
     */
    'excluded_subdomains' => [
        'dev',
        'staging',
        'preprod',
        'test',
        'demo',
    ],
    
    // ===== COOKIE D'OPT-OUT DÉVELOPPEUR =====
    
    /**
     * Cookie spécial pour opt-out complet (développeurs, testeurs)
     * Si ce cookie existe avec la valeur spécifiée, AUCUN tracking
     */
    'dev_cookie' => [
        'name' => 'xeilos_dev',
        'value' => '1',
        'expires' => 365, // jours
    ],
    
    // ===== RGPD & CONSENTEMENT =====
    
    /**
     * Mode "Tout refuser" - Données collectées en mode anonyme strict
     */
    'anonymous_mode' => [
        'enabled' => true,
        'collect_page_views' => true,  // Comptage agrégé uniquement
        'collect_ip' => false,          // JAMAIS l'IP
        'collect_session_id' => false,  // Pas d'identification
        'collect_device_info' => false, // Pas de fingerprinting
        'collect_referrer' => false,    // Pas de source
        'aggregate_only' => true,       // Agrégation immédiate
    ],
    
    /**
     * Mode "Tout accepter" - Données collectées (conformes RGPD)
     */
    'full_consent_mode' => [
        'collect_country' => true,      // Pays (géoloc serveur, IP non stockée)
        'collect_device' => true,       // Desktop/Mobile/Tablet
        'collect_os' => true,           // Windows/Mac/Linux/iOS/Android
        'collect_browser' => true,      // Chrome/Firefox/Safari...
        'collect_language' => true,     // Langue navigateur
        'collect_referrer' => true,     // Source de trafic
        'collect_utm' => true,          // Paramètres UTM (campagnes)
        'collect_page_views' => true,   // Pages visitées
        'collect_time_on_page' => true, // Durée sur page
        'collect_scroll_depth' => true, // Profondeur de scroll
        'collect_clicks' => true,       // Clics sur éléments importants
        'collect_search' => true,       // Recherches internes
        'anonymize_ip' => true,         // IP anonymisée (derniers octets masqués)
        'session_id_random' => true,    // ID aléatoire (pas d'email/PII)
    ],
    
    /**
     * Durée de conservation des données (RGPD)
     */
    'data_retention' => [
        'analytics' => 365,      // jours (1 an)
        'consent' => 1095,       // jours (3 ans pour preuve légale)
    ],
    
    /**
     * Version de la politique de confidentialité
     * À incrémenter à chaque changement majeur
     */
    'privacy_policy_version' => '1.0',
    
    /**
     * Géolocalisation
     */
    'geolocation' => [
        'enabled' => true,
        'provider' => 'server_side',    // 'server_side' ou 'ip_api'
        'store_ip' => false,             // Ne JAMAIS stocker l'IP brute
        'precision' => 'country',        // 'country', 'city', ou 'region'
    ],
    
    // ===== SDK & SERVICES EXTERNES =====
    
    /**
     * Services externes à bloquer si "tout refuser"
     */
    'blocked_on_reject' => [
        'google_analytics',
        'facebook_pixel',
        'google_ads',
        'linkedin_insight',
        'tiktok_pixel',
        'hotjar',
    ],
    
    /**
     * Éléments à tracker (événements)
     */
    'tracked_events' => [
        'cta_click' => true,             // Clics sur boutons CTA
        'form_submit' => true,           // Soumissions de formulaires
        'calendly_open' => true,         // Ouverture Calendly
        'pdf_download' => true,          // Téléchargements PDF
        'video_play' => true,            // Lecture vidéo
        'search' => true,                // Recherches
        'filter_use' => true,            // Utilisation des filtres
    ],
];

