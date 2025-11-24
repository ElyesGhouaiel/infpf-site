#!/bin/bash
###############################################################################
# Script de sauvegarde automatique de la base de données
# Auteur: Elyes Ghouaiel
# Usage: ./scripts/backup-database.sh
# Cron: 0 3 * * * /home/u665392393/domains/infpf.fr/public_html/scripts/backup-database.sh
###############################################################################

set -e  # Arrêter en cas d'erreur

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/home/u665392393/domains/infpf.fr/public_html"
BACKUP_DIR="$PROJECT_DIR/backups/database"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
RETENTION_DAYS=30  # Garder les sauvegardes pendant 30 jours

# Créer le dossier de backup s'il n'existe pas
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}🔄 Démarrage de la sauvegarde de la base de données...${NC}"

# Charger les variables d'environnement
if [ -f "$PROJECT_DIR/.env" ]; then
    source <(grep -v '^#' "$PROJECT_DIR/.env" | sed 's/^/export /')
else
    echo -e "${RED}❌ Fichier .env introuvable !${NC}"
    exit 1
fi

# Extraire les informations de connexion depuis DATABASE_URL
# Format: mysql://user:password@host:port/database
DB_USER=$(echo $DATABASE_URL | sed -n 's/.*:\/\/\([^:]*\):.*/\1/p')
DB_PASS=$(echo $DATABASE_URL | sed -n 's/.*:\/\/[^:]*:\([^@]*\)@.*/\1/p')
DB_HOST=$(echo $DATABASE_URL | sed -n 's/.*@\([^:]*\):.*/\1/p')
DB_PORT=$(echo $DATABASE_URL | sed -n 's/.*:\([0-9]*\)\/.*/\1/p')
DB_NAME=$(echo $DATABASE_URL | sed -n 's/.*\/\([^?]*\).*/\1/p')

# Vérification des variables
if [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo -e "${RED}❌ Impossible d'extraire les informations de DATABASE_URL${NC}"
    exit 1
fi

# Nom du fichier de sauvegarde
BACKUP_FILE="$BACKUP_DIR/infpf_backup_$DATE.sql"
BACKUP_FILE_GZ="$BACKUP_FILE.gz"

echo -e "${YELLOW}📊 Base de données: $DB_NAME${NC}"
echo -e "${YELLOW}📁 Destination: $BACKUP_FILE_GZ${NC}"

# Effectuer la sauvegarde
if mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    --single-transaction \
    --quick \
    --lock-tables=false \
    --routines \
    --triggers \
    --events \
    > "$BACKUP_FILE" 2>/dev/null; then
    
    # Compresser la sauvegarde
    gzip -9 "$BACKUP_FILE"
    
    # Vérifier la taille du fichier
    BACKUP_SIZE=$(du -h "$BACKUP_FILE_GZ" | cut -f1)
    
    echo -e "${GREEN}✅ Sauvegarde réussie !${NC}"
    echo -e "${GREEN}📦 Taille: $BACKUP_SIZE${NC}"
    echo -e "${GREEN}📍 Fichier: $BACKUP_FILE_GZ${NC}"
    
    # Nettoyer les anciennes sauvegardes (> RETENTION_DAYS jours)
    echo -e "${YELLOW}🧹 Nettoyage des anciennes sauvegardes (> $RETENTION_DAYS jours)...${NC}"
    find "$BACKUP_DIR" -name "infpf_backup_*.sql.gz" -type f -mtime +$RETENTION_DAYS -delete
    
    # Compter les sauvegardes restantes
    BACKUP_COUNT=$(find "$BACKUP_DIR" -name "infpf_backup_*.sql.gz" -type f | wc -l)
    echo -e "${GREEN}📊 Nombre de sauvegardes conservées: $BACKUP_COUNT${NC}"
    
    # Log de succès
    echo "$(date): Backup successful - $BACKUP_FILE_GZ ($BACKUP_SIZE)" >> "$PROJECT_DIR/var/log/backup.log"
    
else
    echo -e "${RED}❌ Erreur lors de la sauvegarde !${NC}"
    echo "$(date): Backup FAILED" >> "$PROJECT_DIR/var/log/backup.log"
    exit 1
fi

echo -e "${GREEN}✅ Processus de sauvegarde terminé avec succès !${NC}"




