/**
 * Système de tracking analytics PRO conforme RGPD avec exclusions strictes
 * INFPF - Institut National de la Formation Professionnelle Française
 * Version 2.0 - Analytics Professionnel
 * 
 * Fonctionnalités PRO ajoutées :
 * ✅ Parcours utilisateur (user journey, entonnoirs)
 * ✅ Nouveaux vs Retours (fidélisation)
 * ✅ Engagement (sessions engagées)
 * ✅ Événements personnalisés (clics CTA, téléchargements, formulaires)
 * ✅ Performance Web (Core Web Vitals)
 * ✅ Recherche interne
 * ✅ Données techniques avancées
 */

(function() {
    'use strict';
    
    // ===== CONFIGURATION =====
    const CONFIG = {
        apiEndpoint: '/cookie',
        consentCookieName: 'infpf_consent',
        sessionCookieName: 'infpf_session',
        userIdCookieName: 'infpf_user_id', // Nouveau: identifiant utilisateur long terme
        devCookieName: 'xeilos_dev',
        consentDuration: 13 * 30, // jours (13 mois)
        sessionDuration: 30 * 60 * 1000, // 30 minutes
        userIdDuration: 730, // 2 ans
        trackingInterval: 10000, // Envoyer les données toutes les 10 secondes
        scrollThresholds: [25, 50, 75, 90, 100],
        
        // Routes privées (JAMAIS trackées)
        privatePathPatterns: [
            /^\/admin/,
            /^\/backoffice/,
            /^\/dashboard/,
            /^\/api\//,
            /^\/_profiler/,
            /^\/_wdt/,
        ],
        
        // Événements à tracker automatiquement
        autoTrackEvents: {
            calendly: true,        // Clics sur boutons Calendly
            downloads: true,       // Téléchargements (PDF, etc.)
            forms: true,           // Soumissions de formulaires
            externalLinks: true,   // Clics sur liens externes
            mailto: true,          // Clics sur liens email
            tel: true,             // Clics sur liens téléphone
        },
    };
    
    // ===== ÉTAT GLOBAL =====
    const state = {
        consentToken: null,
        sessionId: null,
        userId: null,           // Nouveau: ID utilisateur long terme
        anonymousMode: false,
        trackingEnabled: false,
        isExcluded: false,
        excludeReason: null,
        pageLoadTime: Date.now(),
        maxScrollDepth: 0,
        interactions: 0,
        clickCount: 0,          // Nouveau: compteur de clics
        lastActivity: Date.now(),
        scrollThresholdsReached: [],
        previousPage: null,     // Nouveau: page précédente
        landingPage: null,      // Nouveau: page d'entrée
        pagesInSession: 1,      // Nouveau: compteur de pages
        sessionStartTime: Date.now(),
        performanceData: null,  // Nouveau: données de performance
    };
    
    // ===== VÉRIFICATIONS D'EXCLUSION =====
    const ExclusionChecker = {
        shouldBlock() {
            if (this.hasDevCookie()) {
                state.isExcluded = true;
                state.excludeReason = 'dev_cookie';
                console.log('🚫 Tracking bloqué: cookie développeur détecté');
                return true;
            }
            
            if (this.isPrivatePath()) {
                state.isExcluded = true;
                state.excludeReason = 'private_path';
                console.log('🚫 Tracking bloqué: route privée', window.location.pathname);
                return true;
            }
            
            return false;
        },
        
        hasDevCookie() {
            return CookieManager.get(CONFIG.devCookieName) === '1';
        },
        
        isPrivatePath() {
            const path = window.location.pathname;
            return CONFIG.privatePathPatterns.some(pattern => pattern.test(path));
        },
    };
    
    // ===== UTILITAIRES COOKIES =====
    const CookieManager = {
        set(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + d.toUTCString();
            document.cookie = `${name}=${value};${expires};path=/;SameSite=Strict`;
        },
        
        get(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(nameEQ) === 0) {
                    return c.substring(nameEQ.length);
                }
            }
            return null;
        },
        
        delete(name) {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        },
        
        setDevCookie() {
            this.set(CONFIG.devCookieName, '1', 365);
            console.log('🔒 Cookie développeur activé - Tracking complètement désactivé');
            window.location.reload();
        },
        
        removeDevCookie() {
            this.delete(CONFIG.devCookieName);
            console.log('✅ Cookie développeur désactivé - Tracking réactivé');
            window.location.reload();
        }
    };
    
    // ===== GESTION DU CONSENTEMENT =====
    const ConsentManager = {
        async acceptAll() {
            state.anonymousMode = false;
            state.trackingEnabled = true;
            
            const token = await this.saveConsent(true, true);
            if (token) {
                console.log('✅ Lancement immédiat du tracking après acceptation');
                AnalyticsTracker.init();
            } else {
                console.error('❌ Échec du lancement du tracking: pas de token');
            }
        },
        
        rejectAll() {
            state.anonymousMode = true;
            state.trackingEnabled = false;
            state.consentToken = null;
            
            CookieManager.delete(CONFIG.consentCookieName);
            CookieManager.delete(CONFIG.sessionCookieName);
            
            localStorage.setItem('infpf_consent_rejected', '1');
            
            console.log('🚫 Mode anonyme activé: aucun tracking individuel');
            
            this.trackAnonymousPageView();
        },
        
        async saveCustom(analytics, marketing) {
            if (analytics) {
                state.anonymousMode = false;
                state.trackingEnabled = true;
                const token = await this.saveConsent(analytics, marketing);
                if (token) {
                    AnalyticsTracker.init();
                }
            } else {
                this.rejectAll();
            }
        },
        
        async saveConsent(analytics, marketing) {
            try {
                const response = await fetch(`${CONFIG.apiEndpoint}/consent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ analytics, marketing })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    state.consentToken = data.token;
                    CookieManager.set(CONFIG.consentCookieName, data.token, CONFIG.consentDuration);
                    localStorage.removeItem('infpf_consent_rejected');
                    
                    console.log('✅ Consentement enregistré', {
                        analytics,
                        marketing,
                        token: data.token
                    });
                    
                    return data.token;
                }
            } catch (error) {
                console.error('❌ Erreur sauvegarde consentement:', error);
            }
            return null;
        },
        
        trackAnonymousPageView() {
            console.log('📊 Page vue (mode anonyme) - pas de tracking individuel');
            const pageViews = parseInt(localStorage.getItem('anonymous_page_views') || '0') + 1;
            localStorage.setItem('anonymous_page_views', pageViews.toString());
        },
        
        revoke() {
            state.trackingEnabled = false;
            state.consentToken = null;
            state.anonymousMode = true;
            
            CookieManager.delete(CONFIG.consentCookieName);
            CookieManager.delete(CONFIG.sessionCookieName);
            
            console.log('🔴 Consentement révoqué - Tracking arrêté');
        },
    };
    
    // ===== TRACKING ANALYTICS =====
    const AnalyticsTracker = {
        init() {
            if (state.isExcluded) {
                console.log(`🚫 Tracking non initialisé: ${state.excludeReason}`);
                return;
            }
            
            if (!state.trackingEnabled || state.anonymousMode) {
                console.log('🚫 Tracking non initialisé: pas de consentement ou mode anonyme');
                return;
            }
            
            console.log('🚀 Initialisation du tracking analytics PRO');
            
            // Initialiser les IDs
            this.initUserTracking();
            
            // Collecter les données de performance
            this.collectPerformanceData();
            
            // Tracker la page vue initiale
            this.trackPageView();
            
            // Écouter les événements
            this.attachEventListeners();
            
            // Auto-tracking des événements
            this.setupAutoTracking();
            
            // Envoyer les données périodiquement
            this.startPeriodicTracking();
        },
        
        initUserTracking() {
            // Session ID (courte durée)
            let sessionId = CookieManager.get(CONFIG.sessionCookieName);
            if (!sessionId) {
                sessionId = this.generateId('sess');
                CookieManager.set(CONFIG.sessionCookieName, sessionId, CONFIG.sessionDuration / (24 * 60 * 60 * 1000));
                state.pagesInSession = 1;
                state.landingPage = window.location.pathname + window.location.search;
            } else {
                // Session existante, incrémenter le compteur de pages
                const pageCount = parseInt(sessionStorage.getItem('infpf_pages_count') || '1') + 1;
                state.pagesInSession = pageCount;
                sessionStorage.setItem('infpf_pages_count', pageCount.toString());
            }
            state.sessionId = sessionId;
            
            // User ID (longue durée, pour nouveaux vs retours)
            let userId = CookieManager.get(CONFIG.userIdCookieName);
            if (!userId) {
                userId = this.generateId('usr');
                CookieManager.set(CONFIG.userIdCookieName, userId, CONFIG.userIdDuration);
            }
            state.userId = userId;
            
            // Page précédente (depuis sessionStorage)
            state.previousPage = sessionStorage.getItem('infpf_previous_page') || null;
            
            // Sauvegarder la page actuelle pour la prochaine navigation
            sessionStorage.setItem('infpf_previous_page', window.location.pathname + window.location.search);
            
            console.log('🔑 User ID:', userId, '| Session ID:', sessionId, '| Pages:', state.pagesInSession);
        },
        
        generateId(prefix = 'id') {
            return prefix + '_' + Math.random().toString(36).substr(2, 16) + Date.now().toString(36);
        },
        
        collectPerformanceData() {
            if (!window.performance || !window.performance.timing) {
                return;
            }
            
            // Attendre que la page soit complètement chargée
            window.addEventListener('load', () => {
                setTimeout(() => {
                    const timing = performance.timing;
                    const paint = performance.getEntriesByType('paint');
                    
                    state.performanceData = {
                        pageLoadTime: timing.loadEventEnd - timing.navigationStart,
                        domReadyTime: timing.domContentLoadedEventEnd - timing.navigationStart,
                        firstPaintTime: paint.length > 0 ? Math.round(paint[0].startTime) : null,
                    };
                    
                    console.log('⚡ Performance collectée:', state.performanceData);
                }, 1000); // Attendre 1s pour être sûr que tout est chargé
            });
        },
        
        async trackPageView() {
            const data = {
                sessionId: state.sessionId,
                userId: state.userId,
                consentToken: state.consentToken,
                anonymousMode: state.anonymousMode,
                pageUrl: window.location.pathname + window.location.search,
                pageTitle: document.title,
                referrer: document.referrer || null,
                
                // Nouveaux champs PRO
                previousPageUrl: state.previousPage,
                landingPage: state.landingPage || (window.location.pathname + window.location.search),
                pagesPerSession: state.pagesInSession,
                
                // Données techniques
                language: navigator.language || null,
                screenResolution: `${screen.width}x${screen.height}`,
                viewportSize: `${window.innerWidth}x${window.innerHeight}`,
                
                // Performance (si disponible)
                ...(state.performanceData || {}),
                
                // Métriques de base
                timeOnPage: 0,
                scrollDepth: 0,
                isBounce: false,
                isEngaged: false,
                clickCount: 0,
                
                // UTM
                ...this.getUtmParameters(),
            };
            
            console.log('📤 Envoi données tracking:', {
                url: `${CONFIG.apiEndpoint}/track`,
                pageUrl: data.pageUrl,
                sessionId: data.sessionId.substring(0, 10) + '...',
                consentToken: data.consentToken ? data.consentToken.substring(0, 10) + '...' : 'NULL'
            });
            
            try {
                const response = await fetch(`${CONFIG.apiEndpoint}/track`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                console.log('✅ Réponse serveur tracking:', {
                    success: result.success,
                    excluded: result.excluded,
                    status: response.status
                });
                
                if (result.excluded) {
                    console.log(`🚫 Tracking exclu côté serveur: ${result.reason}`);
                    state.isExcluded = true;
                    state.excludeReason = result.reason;
                    return;
                }
                
                if (result.anonymous) {
                    console.log('📊 Mode anonyme confirmé - pas de tracking individuel');
                    return;
                }
                
                console.log('✅ Page vue enregistrée');
            } catch (error) {
                console.error('❌ Erreur tracking page vue:', error);
            }
        },
        
        async updateMetrics() {
            if (!state.trackingEnabled || state.anonymousMode || state.isExcluded) {
                return;
            }
            
            const timeOnPage = Math.floor((Date.now() - state.pageLoadTime) / 1000);
            const isBounce = state.interactions === 0 && timeOnPage < 30;
            
            // Calcul de l'engagement : >= 2 pages OU (>= 10s ET >= 1 interaction)
            const isEngaged = state.pagesInSession >= 2 || (timeOnPage >= 10 && state.interactions > 0);
            
            const data = {
                sessionId: state.sessionId,
                pageUrl: window.location.pathname + window.location.search,
                timeOnPage,
                scrollDepth: state.maxScrollDepth,
                isBounce,
                isEngaged,
                clickCount: state.clickCount,
                pagesPerSession: state.pagesInSession,
            };
            
            try {
                await fetch(`${CONFIG.apiEndpoint}/event`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
            } catch (error) {
                console.error('❌ Erreur mise à jour métriques:', error);
            }
        },
        
        attachEventListeners() {
            // Scroll depth
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    this.trackScrollDepth();
                }, 200);
            });
            
            // Interactions (clics, touches clavier)
            ['click', 'touchstart', 'keydown'].forEach(event => {
                document.addEventListener(event, () => {
                    state.interactions++;
                    state.lastActivity = Date.now();
                    if (event === 'click') {
                        state.clickCount++;
                    }
                });
            });
            
            // Sortie de page (envoyer les dernières données)
            window.addEventListener('beforeunload', () => {
                this.updateMetrics();
            });
            
            // Visibilité (pause tracking si onglet caché)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.updateMetrics();
                }
            });
        },
        
        trackScrollDepth() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            const scrollPercent = Math.floor((scrollTop / (documentHeight - windowHeight)) * 100);
            
            if (scrollPercent > state.maxScrollDepth) {
                state.maxScrollDepth = Math.min(scrollPercent, 100);
            }
        },
        
        startPeriodicTracking() {
            setInterval(() => {
                if (state.trackingEnabled && !state.anonymousMode && !state.isExcluded) {
                    this.updateMetrics();
                }
            }, CONFIG.trackingInterval);
        },
        
        getUtmParameters() {
            const params = new URLSearchParams(window.location.search);
            return {
                utmSource: params.get('utm_source') || null,
                utmMedium: params.get('utm_medium') || null,
                utmCampaign: params.get('utm_campaign') || null,
                utmTerm: params.get('utm_term') || null,
                utmContent: params.get('utm_content') || null,
            };
        },
        
        // ===== AUTO-TRACKING DES ÉVÉNEMENTS =====
        
        setupAutoTracking() {
            if (CONFIG.autoTrackEvents.calendly) {
                this.trackCalendlyClicks();
            }
            if (CONFIG.autoTrackEvents.downloads) {
                this.trackDownloads();
            }
            if (CONFIG.autoTrackEvents.forms) {
                this.trackFormSubmissions();
            }
            if (CONFIG.autoTrackEvents.externalLinks) {
                this.trackExternalLinks();
            }
            if (CONFIG.autoTrackEvents.mailto) {
                this.trackMailtoLinks();
            }
            if (CONFIG.autoTrackEvents.tel) {
                this.trackTelLinks();
            }
        },
        
        trackCalendlyClicks() {
            document.addEventListener('click', (e) => {
                const target = e.target.closest('[data-calendly-url], .calendly-btn, .btn-calendly-contact');
                if (target) {
                    const url = target.getAttribute('data-calendly-url') || target.href;
                    EventTracker.track('calendly_click', {
                        category: 'conversion',
                        label: target.textContent.trim(),
                        value: null,
                        data: { calendly_url: url }
                    });
                }
            });
        },
        
        trackDownloads() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href) {
                    const href = link.href.toLowerCase();
                    const downloadExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.zip', '.rar'];
                    
                    if (downloadExtensions.some(ext => href.includes(ext)) || link.hasAttribute('download')) {
                        const fileName = link.href.split('/').pop();
                        EventTracker.track('file_download', {
                            category: 'engagement',
                            label: fileName,
                            value: null,
                            data: { file_url: link.href, file_name: fileName }
                        });
                    }
                }
            });
        },
        
        trackFormSubmissions() {
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (form && form.tagName === 'FORM') {
                    const formId = form.id || form.name || 'unknown_form';
                    const formAction = form.action || window.location.href;
                    
                    EventTracker.track('form_submit', {
                        category: 'conversion',
                        label: formId,
                        value: null,
                        data: { form_id: formId, form_action: formAction }
                    });
                }
            });
        },
        
        trackExternalLinks() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href) {
                    const currentDomain = window.location.hostname;
                    const linkDomain = new URL(link.href).hostname;
                    
                    if (linkDomain && linkDomain !== currentDomain && !link.href.startsWith('mailto:') && !link.href.startsWith('tel:')) {
                        EventTracker.track('external_link_click', {
                            category: 'engagement',
                            label: linkDomain,
                            value: null,
                            data: { external_url: link.href, link_text: link.textContent.trim() }
                        });
                    }
                }
            });
        },
        
        trackMailtoLinks() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && link.href.startsWith('mailto:')) {
                    const email = link.href.replace('mailto:', '').split('?')[0];
                    EventTracker.track('mailto_click', {
                        category: 'engagement',
                        label: email,
                        value: null,
                        data: { email }
                    });
                }
            });
        },
        
        trackTelLinks() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && link.href.startsWith('tel:')) {
                    const phone = link.href.replace('tel:', '');
                    EventTracker.track('tel_click', {
                        category: 'engagement',
                        label: phone,
                        value: null,
                        data: { phone }
                    });
                }
            });
        },
    };
    
    // ===== TRACKING D'ÉVÉNEMENTS PERSONNALISÉS =====
    const EventTracker = {
        async track(eventName, options = {}) {
            if (!state.trackingEnabled || state.anonymousMode || state.isExcluded) {
                console.log(`🚫 Événement non tracké: ${eventName}`);
                return;
            }
            
            const {
                category = 'engagement',
                label = null,
                value = null,
                data = {}
            } = options;
            
            const eventData = {
                sessionId: state.sessionId,
                consentToken: state.consentToken,
                eventName,
                eventCategory: category,
                eventLabel: label,
                eventValue: value,
                pageUrl: window.location.pathname + window.location.search,
                pageTitle: document.title,
                eventData: data,
            };
            
            try {
                const response = await fetch(`${CONFIG.apiEndpoint}/event/custom`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(eventData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    console.log(`✅ Événement tracké: ${eventName}`, category, label);
                }
            } catch (error) {
                console.error(`❌ Erreur tracking événement ${eventName}:`, error);
            }
        }
    };
    
    // ===== INITIALISATION =====
    function init() {
        console.log('🍪 Initialisation du système de cookies & analytics RGPD PRO v2.0');
        
        // 1. Vérifier les exclusions
        if (ExclusionChecker.shouldBlock()) {
            return;
        }
        
        // 2. Vérifier le consentement existant
        const existingConsent = CookieManager.get(CONFIG.consentCookieName);
        const consentRejected = localStorage.getItem('infpf_consent_rejected') === '1';
        
        if (existingConsent) {
            state.consentToken = existingConsent;
            state.trackingEnabled = true;
            state.anonymousMode = false;
            AnalyticsTracker.init();
        } else if (consentRejected) {
            state.anonymousMode = true;
            state.trackingEnabled = false;
            console.log('🚫 Consentement refusé précédemment - Mode anonyme');
        } else {
            console.log('⏸️ En attente du consentement utilisateur');
        }
    }
    
    // ===== EXPOSITION GLOBALE =====
    window.INFPFAnalytics = {
        // Consentement
        acceptAll: () => ConsentManager.acceptAll(),
        rejectAll: () => ConsentManager.rejectAll(),
        saveCustom: (analytics, marketing) => ConsentManager.saveCustom(analytics, marketing),
        revoke: () => ConsentManager.revoke(),
        
        // Cookie développeur
        setDevCookie: () => CookieManager.setDevCookie(),
        removeDevCookie: () => CookieManager.removeDevCookie(),
        
        // Événements personnalisés (API publique)
        trackEvent: (eventName, options) => EventTracker.track(eventName, options),
        
        // État
        getState: () => ({ ...state }),
    };
    
    // Initialiser au chargement de la page
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
