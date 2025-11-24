#!/bin/bash

###############################################################################
# Script de Backup Complet AVANT Déploiement en Production
###############################################################################

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="/home/u665392393/domains/infpf.fr/backups/pre-production-${TIMESTAMP}"
PROD_DIR="/home/u665392393/domains/infpf.fr/public_html"
DB_BACKUP_FILE="${BACKUP_DIR}/database-backup.sql"
FILES_BACKUP_FILE="${BACKUP_DIR}/files-backup.tar.gz"
ENV_BACKUP_FILE="${BACKUP_DIR}/env-files-backup.tar.gz"
GIT_BACKUP_FILE="${BACKUP_DIR}/git-status.txt"
ROLLBACK_SCRIPT="${BACKUP_DIR}/rollback.sh"

echo -e "${BLUE}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   BACKUP COMPLET AVANT DÉPLOIEMENT EN PRODUCTION         ║${NC}"
echo -e "${BLUE}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""

# Créer le répertoire de backup
echo -e "${YELLOW}📁 Création du répertoire de backup...${NC}"
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✅ Répertoire créé : ${BACKUP_DIR}${NC}"
echo ""

# 1. BACKUP DE LA BASE DE DONNÉES
echo -e "${YELLOW}🗄️  Backup de la base de données de production...${NC}"
cd "$PROD_DIR"

if [ -f ".env.local" ]; then
    DB_URL=$(grep '^DATABASE_URL=' .env.local | cut -d'=' -f2- | tr -d '"' | tr -d "'")
    
    DB_USER=$(echo $DB_URL | sed -n 's|.*://\([^:]*\):.*|\1|p')
    DB_PASS=$(echo $DB_URL | sed -n 's|.*://[^:]*:\([^@]*\)@.*|\1|p')
    DB_HOST=$(echo $DB_URL | sed -n 's|.*@\([^:]*\):.*|\1|p')
    DB_PORT=$(echo $DB_URL | sed -n 's|.*:\([0-9]*\)/.*|\1|p')
    DB_NAME=$(echo $DB_URL | sed -n 's|.*/\([^?]*\).*|\1|p')
    
    mysqldump -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" > "${DB_BACKUP_FILE}" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        DB_SIZE=$(du -h "${DB_BACKUP_FILE}" | cut -f1)
        echo -e "${GREEN}✅ Base de données sauvegardée : ${DB_SIZE}${NC}"
        echo "   📍 Fichier : ${DB_BACKUP_FILE}"
    else
        echo -e "${RED}❌ Erreur lors du backup de la base de données${NC}"
    fi
else
    echo -e "${RED}❌ Fichier .env.local introuvable${NC}"
fi
echo ""

# 2. BACKUP DES FICHIERS CRITIQUES
echo -e "${YELLOW}📦 Backup des fichiers critiques...${NC}"
cd "$PROD_DIR"

tar -czf "${FILES_BACKUP_FILE}" \
    --exclude='var/cache/*' \
    --exclude='var/log/*' \
    --exclude='vendor/*' \
    --exclude='node_modules/*' \
    --exclude='backups/*' \
    --exclude='.git/*' \
    . 2>/dev/null

if [ $? -eq 0 ]; then
    FILES_SIZE=$(du -h "${FILES_BACKUP_FILE}" | cut -f1)
    echo -e "${GREEN}✅ Fichiers sauvegardés : ${FILES_SIZE}${NC}"
    echo "   📍 Fichier : ${FILES_BACKUP_FILE}"
else
    echo -e "${RED}❌ Erreur lors du backup des fichiers${NC}"
fi
echo ""

# 3. BACKUP DES FICHIERS .ENV
echo -e "${YELLOW}🔐 Backup des fichiers d'environnement...${NC}"
cd "$PROD_DIR"

tar -czf "${ENV_BACKUP_FILE}" \
    .env \
    .env.local \
    .env.local.php 2>/dev/null

if [ $? -eq 0 ]; then
    ENV_SIZE=$(du -h "${ENV_BACKUP_FILE}" | cut -f1)
    echo -e "${GREEN}✅ Fichiers .env sauvegardés : ${ENV_SIZE}${NC}"
    echo "   📍 Fichier : ${ENV_BACKUP_FILE}"
else
    echo -e "${YELLOW}⚠️  Certains fichiers .env n'existent pas (normal)${NC}"
fi
echo ""

# 4. BACKUP DU STATUT GIT
echo -e "${YELLOW}📝 Sauvegarde du statut Git...${NC}"
cd "$PROD_DIR"

{
    echo "=== GIT STATUS ==="
    git status
    echo ""
    echo "=== GIT BRANCH ==="
    git branch -v
    echo ""
    echo "=== GIT LOG (10 derniers commits) ==="
    git log --oneline -10
    echo ""
    echo "=== CURRENT COMMIT ==="
    git rev-parse HEAD
} > "${GIT_BACKUP_FILE}" 2>&1

echo -e "${GREEN}✅ Statut Git sauvegardé${NC}"
echo "   📍 Fichier : ${GIT_BACKUP_FILE}"
echo ""

# 5. CRÉER LE SCRIPT DE ROLLBACK
echo -e "${YELLOW}🔄 Création du script de rollback...${NC}"

cat > "${ROLLBACK_SCRIPT}" << 'ROLLBACK_EOF'
#!/bin/bash

###############################################################################
# Script de ROLLBACK Automatique
###############################################################################

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROD_DIR="/home/u665392393/domains/infpf.fr/public_html"
DB_BACKUP_FILE="${BACKUP_DIR}/database-backup.sql"
ENV_BACKUP_FILE="${BACKUP_DIR}/env-files-backup.tar.gz"
GIT_BACKUP_FILE="${BACKUP_DIR}/git-status.txt"

echo -e "${RED}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${RED}║          ⚠️  ROLLBACK EN COURS ⚠️                         ║${NC}"
echo -e "${RED}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""

read -p "Êtes-vous sûr de vouloir faire un rollback ? (oui/non) : " CONFIRM
if [ "$CONFIRM" != "oui" ]; then
    echo -e "${YELLOW}Rollback annulé.${NC}"
    exit 0
fi

# 1. Restaurer la base de données
echo -e "${YELLOW}🗄️  Restauration de la base de données...${NC}"
if [ -f "${DB_BACKUP_FILE}" ]; then
    cd "$PROD_DIR"
    
    if [ -f ".env.local" ]; then
        DB_URL=$(grep '^DATABASE_URL=' .env.local | cut -d'=' -f2- | tr -d '"' | tr -d "'")
        DB_USER=$(echo $DB_URL | sed -n 's|.*://\([^:]*\):.*|\1|p')
        DB_PASS=$(echo $DB_URL | sed -n 's|.*://[^:]*:\([^@]*\)@.*|\1|p')
        DB_HOST=$(echo $DB_URL | sed -n 's|.*@\([^:]*\):.*|\1|p')
        DB_PORT=$(echo $DB_URL | sed -n 's|.*:\([0-9]*\)/.*|\1|p')
        DB_NAME=$(echo $DB_URL | sed -n 's|.*/\([^?]*\).*|\1|p')
        
        mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "${DB_BACKUP_FILE}" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✅ Base de données restaurée${NC}"
        else
            echo -e "${RED}❌ Erreur lors de la restauration de la base de données${NC}"
        fi
    fi
else
    echo -e "${RED}❌ Backup de la base de données introuvable${NC}"
fi
echo ""

# 2. Restaurer les fichiers .env
echo -e "${YELLOW}🔐 Restauration des fichiers .env...${NC}"
if [ -f "${ENV_BACKUP_FILE}" ]; then
    cd "$PROD_DIR"
    tar -xzf "${ENV_BACKUP_FILE}" 2>/dev/null
    echo -e "${GREEN}✅ Fichiers .env restaurés${NC}"
else
    echo -e "${RED}❌ Backup des fichiers .env introuvable${NC}"
fi
echo ""

# 3. Restaurer Git à l'ancien commit
echo -e "${YELLOW}📝 Restauration de Git...${NC}"
if [ -f "${GIT_BACKUP_FILE}" ]; then
    OLD_COMMIT=$(grep -A1 "CURRENT COMMIT" "${GIT_BACKUP_FILE}" | tail -1)
    cd "$PROD_DIR"
    git checkout main
    git reset --hard "$OLD_COMMIT"
    echo -e "${GREEN}✅ Git restauré au commit : ${OLD_COMMIT}${NC}"
else
    echo -e "${RED}❌ Informations Git introuvables${NC}"
fi
echo ""

# 4. Clear cache
echo -e "${YELLOW}🧹 Nettoyage du cache...${NC}"
cd "$PROD_DIR"
php bin/console cache:clear --env=prod --no-debug
echo -e "${GREEN}✅ Cache nettoyé${NC}"
echo ""

echo -e "${GREEN}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║              ✅ ROLLBACK TERMINÉ ✅                        ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════════════╝${NC}"
ROLLBACK_EOF

chmod +x "${ROLLBACK_SCRIPT}"
echo -e "${GREEN}✅ Script de rollback créé${NC}"
echo "   📍 Fichier : ${ROLLBACK_SCRIPT}"
echo ""

# RÉSUMÉ
echo -e "${GREEN}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║           ✅ BACKUP COMPLET TERMINÉ ✅                    ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📍 Localisation du backup : ${BACKUP_DIR}${NC}"
echo ""
echo -e "${YELLOW}⚠️  En cas de problème : bash ${ROLLBACK_SCRIPT}${NC}"
echo ""
echo -e "${GREEN}🚀 Prêt pour le déploiement en production !${NC}"
