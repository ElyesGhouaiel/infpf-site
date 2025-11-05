#!/bin/bash

###############################################################################
# Script de backup automatique de la base de données INFPF
# Author: elyes@xeilos.fr
# Date: 2025-11-05
###############################################################################

# Configuration
PROJECT_DIR="/home/u665392393/domains/infpf.fr/dev"
BACKUP_DIR="/home/u665392393/backups/database"
LOG_FILE="/home/u665392393/backups/backup.log"
RETENTION_DAYS=30  # Garder les backups pendant 30 jours
DATE=$(date +"%Y-%m-%d_%H-%M-%S")

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction de logging
log() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] $1" | tee -a "$LOG_FILE"
}

# Fonction d'erreur
error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

# Fonction de succès
success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

# Fonction d'avertissement
warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Créer le répertoire de backup s'il n'existe pas
mkdir -p "$BACKUP_DIR" || error "Impossible de créer le répertoire de backup"
mkdir -p "$(dirname "$LOG_FILE")" || error "Impossible de créer le répertoire de logs"

log "========================================="
log "Début du backup de la base de données"
log "========================================="

# Extraire les informations de connexion depuis .env
cd "$PROJECT_DIR" || error "Répertoire projet introuvable"

if [ ! -f ".env" ]; then
    error "Fichier .env introuvable"
fi

# Parser DATABASE_URL
# Format: mysql://user:password@host:port/dbname
DATABASE_URL=$(grep "^DATABASE_URL=" .env | cut -d '=' -f 2- | tr -d '"')

if [ -z "$DATABASE_URL" ]; then
    error "DATABASE_URL non trouvé dans .env"
fi

# Extraction des composants de l'URL
DB_USER=$(echo "$DATABASE_URL" | sed -n 's/.*:\/\/\([^:]*\):.*/\1/p')
DB_PASS=$(echo "$DATABASE_URL" | sed -n 's/.*:\/\/[^:]*:\([^@]*\)@.*/\1/p')
DB_HOST=$(echo "$DATABASE_URL" | sed -n 's/.*@\([^:]*\):.*/\1/p')
DB_PORT=$(echo "$DATABASE_URL" | sed -n 's/.*:\([0-9]*\)\/.*/\1/p')
DB_NAME=$(echo "$DATABASE_URL" | sed -n 's/.*\/\([^?]*\).*/\1/p')

log "Base de données: $DB_NAME"
log "Hôte: $DB_HOST:$DB_PORT"
log "Utilisateur: $DB_USER"

# Nom du fichier de backup
BACKUP_FILE="$BACKUP_DIR/infpf_db_$DATE.sql.gz"

# Effectuer le dump de la base de données
log "Création du backup..."

if [ -z "$DB_PASS" ] || [ "$DB_PASS" == "@" ]; then
    # Pas de mot de passe
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" \
        --single-transaction \
        --routines \
        --triggers \
        --add-drop-table \
        2>> "$LOG_FILE" | gzip > "$BACKUP_FILE"
else
    # Avec mot de passe
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
        --single-transaction \
        --routines \
        --triggers \
        --add-drop-table \
        2>> "$LOG_FILE" | gzip > "$BACKUP_FILE"
fi

# Vérifier le succès du backup
if [ $? -eq 0 ] && [ -f "$BACKUP_FILE" ]; then
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    success "Backup créé avec succès : $BACKUP_FILE ($BACKUP_SIZE)"
else
    error "Échec de la création du backup"
fi

# Nettoyage des anciens backups
log "Nettoyage des backups de plus de $RETENTION_DAYS jours..."
DELETED=$(find "$BACKUP_DIR" -name "infpf_db_*.sql.gz" -type f -mtime +$RETENTION_DAYS -delete -print | wc -l)

if [ "$DELETED" -gt 0 ]; then
    success "$DELETED ancien(s) backup(s) supprimé(s)"
else
    log "Aucun ancien backup à supprimer"
fi

# Statistiques
TOTAL_BACKUPS=$(find "$BACKUP_DIR" -name "infpf_db_*.sql.gz" | wc -l)
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)

log "========================================="
log "Backup terminé avec succès"
log "Total backups: $TOTAL_BACKUPS"
log "Espace utilisé: $TOTAL_SIZE"
log "========================================="

exit 0

