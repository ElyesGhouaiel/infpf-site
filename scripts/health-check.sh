#!/bin/bash
###############################################################################
# Script de vérification de santé du site INFPF
# Auteur: Elyes Ghouaiel
# Usage: ./scripts/health-check.sh
# Cron: */5 * * * * /home/u665392393/domains/infpf.fr/public_html/scripts/health-check.sh
###############################################################################

set -e

# Configuration
SITE_URL="https://infpf.fr"
ALERT_EMAIL="admin@infpf.fr"
LOG_FILE="/home/u665392393/domains/infpf.fr/public_html/var/log/health-check.log"
MAX_RESPONSE_TIME=3  # secondes

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Fonction pour envoyer une alerte
send_alert() {
    local subject="$1"
    local message="$2"
    echo -e "${RED}🚨 ALERTE: $subject${NC}"
    echo "$message" | mail -s "[INFPF] $subject" "$ALERT_EMAIL" 2>/dev/null || echo "Email non envoyé"
}

# Fonction pour logger
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

echo -e "${YELLOW}🏥 Vérification de santé du site INFPF...${NC}"

# 1. Vérifier que le site répond
echo -e "${YELLOW}📡 Test de connectivité...${NC}"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time $MAX_RESPONSE_TIME "$SITE_URL" || echo "000")

if [ "$HTTP_CODE" != "200" ]; then
    send_alert "Site inaccessible" "Le site $SITE_URL retourne le code HTTP $HTTP_CODE"
    log_message "ERROR: Site inaccessible (HTTP $HTTP_CODE)"
    echo -e "${RED}❌ Site inaccessible (HTTP $HTTP_CODE)${NC}"
    exit 1
else
    echo -e "${GREEN}✅ Site accessible (HTTP $HTTP_CODE)${NC}"
    log_message "OK: Site accessible"
fi

# 2. Vérifier le temps de réponse
echo -e "${YELLOW}⏱️  Test de performance...${NC}"
RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" --max-time $MAX_RESPONSE_TIME "$SITE_URL" || echo "999")

if (( $(echo "$RESPONSE_TIME > $MAX_RESPONSE_TIME" | bc -l) )); then
    send_alert "Site lent" "Temps de réponse: ${RESPONSE_TIME}s (seuil: ${MAX_RESPONSE_TIME}s)"
    log_message "WARNING: Site lent (${RESPONSE_TIME}s)"
    echo -e "${YELLOW}⚠️  Site lent (${RESPONSE_TIME}s)${NC}"
else
    echo -e "${GREEN}✅ Temps de réponse OK (${RESPONSE_TIME}s)${NC}"
    log_message "OK: Performance normale (${RESPONSE_TIME}s)"
fi

# 3. Vérifier le certificat SSL
echo -e "${YELLOW}🔒 Test du certificat SSL...${NC}"
SSL_EXPIRY=$(echo | openssl s_client -servername infpf.fr -connect infpf.fr:443 2>/dev/null | openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2)

if [ -n "$SSL_EXPIRY" ]; then
    SSL_EXPIRY_EPOCH=$(date -d "$SSL_EXPIRY" +%s)
    NOW_EPOCH=$(date +%s)
    DAYS_LEFT=$(( ($SSL_EXPIRY_EPOCH - $NOW_EPOCH) / 86400 ))
    
    if [ $DAYS_LEFT -lt 30 ]; then
        send_alert "Certificat SSL expire bientôt" "Le certificat SSL expire dans $DAYS_LEFT jours"
        log_message "WARNING: Certificat SSL expire dans $DAYS_LEFT jours"
        echo -e "${YELLOW}⚠️  Certificat SSL expire dans $DAYS_LEFT jours${NC}"
    else
        echo -e "${GREEN}✅ Certificat SSL valide ($DAYS_LEFT jours restants)${NC}"
        log_message "OK: Certificat SSL valide"
    fi
else
    echo -e "${YELLOW}⚠️  Impossible de vérifier le certificat SSL${NC}"
fi

# 4. Vérifier l'espace disque
echo -e "${YELLOW}💾 Test de l'espace disque...${NC}"
DISK_USAGE=$(df -h /home/u665392393 | awk 'NR==2 {print $5}' | sed 's/%//')

if [ $DISK_USAGE -gt 90 ]; then
    send_alert "Espace disque critique" "Utilisation: ${DISK_USAGE}%"
    log_message "CRITICAL: Espace disque ${DISK_USAGE}%"
    echo -e "${RED}❌ Espace disque critique (${DISK_USAGE}%)${NC}"
elif [ $DISK_USAGE -gt 80 ]; then
    log_message "WARNING: Espace disque ${DISK_USAGE}%"
    echo -e "${YELLOW}⚠️  Espace disque élevé (${DISK_USAGE}%)${NC}"
else
    echo -e "${GREEN}✅ Espace disque OK (${DISK_USAGE}%)${NC}"
    log_message "OK: Espace disque ${DISK_USAGE}%"
fi

# 5. Vérifier les logs d'erreurs récents
echo -e "${YELLOW}📋 Vérification des erreurs récentes...${NC}"
ERROR_COUNT=$(find /home/u665392393/domains/infpf.fr/public_html/var/log -name "*.log" -mtime -1 -exec grep -i "error\|critical\|fatal" {} \; 2>/dev/null | wc -l)

if [ $ERROR_COUNT -gt 10 ]; then
    send_alert "Erreurs multiples détectées" "$ERROR_COUNT erreurs dans les dernières 24h"
    log_message "WARNING: $ERROR_COUNT erreurs détectées"
    echo -e "${YELLOW}⚠️  $ERROR_COUNT erreurs détectées${NC}"
else
    echo -e "${GREEN}✅ Pas d'erreurs critiques ($ERROR_COUNT erreurs)${NC}"
    log_message "OK: Pas d'erreurs critiques"
fi

echo -e "${GREEN}✅ Vérification de santé terminée !${NC}"
log_message "Health check completed successfully"




