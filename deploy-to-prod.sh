#!/bin/bash

# Script de déploiement : dev → main (production)
# Usage: ./deploy-to-prod.sh "Description du déploiement"

set -e  # Arrêter en cas d'erreur

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

PROD_DIR="/home/u665392393/domains/infpf.fr/public_html"
DEV_DIR="/home/u665392393/domains/infpf.fr/dev"

echo -e "${BLUE}🚀 Déploiement dev → production${NC}"
echo "========================================"

# Vérifier le message de commit
if [ -z "$1" ]; then
    echo -e "${RED}❌ Erreur : Message de déploiement requis${NC}"
    echo "Usage: ./deploy-to-prod.sh \"Description du déploiement\""
    exit 1
fi

DEPLOY_MESSAGE="$1"

# Étape 1 : Vérifier l'état de dev
echo -e "\n${YELLOW}📋 Étape 1/6 : Vérification de la branche dev${NC}"
cd "$DEV_DIR"

if [ -n "$(git status --porcelain)" ]; then
    echo -e "${RED}❌ La branche dev a des modifications non commitées${NC}"
    git status
    echo -e "${YELLOW}Commitez ou stash vos changements avant de déployer${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Branche dev propre${NC}"

# Étape 2 : Mettre à jour dev depuis GitHub
echo -e "\n${YELLOW}📥 Étape 2/6 : Mise à jour de dev depuis GitHub${NC}"
git checkout dev
git pull origin dev
echo -e "${GREEN}✅ dev mise à jour${NC}"

# Étape 3 : Passer sur main (production)
echo -e "\n${YELLOW}🔄 Étape 3/6 : Passage sur la branche main${NC}"
cd "$PROD_DIR"
git checkout main
echo -e "${GREEN}✅ Sur la branche main${NC}"

# Étape 4 : Créer un backup avant merge
echo -e "\n${YELLOW}💾 Étape 4/6 : Création d'un backup${NC}"
BACKUP_BRANCH="backup/avant-deploy-$(date +%Y%m%d-%H%M%S)"
git branch "$BACKUP_BRANCH"
echo -e "${GREEN}✅ Backup créé : $BACKUP_BRANCH${NC}"

# Étape 5 : Merger dev dans main
echo -e "\n${YELLOW}🔀 Étape 5/6 : Merge dev → main${NC}"
if git merge dev -m "deploy: $DEPLOY_MESSAGE"; then
    echo -e "${GREEN}✅ Merge réussi${NC}"
else
    echo -e "${RED}❌ Conflit lors du merge${NC}"
    echo -e "${YELLOW}Résolvez les conflits, puis :${NC}"
    echo "  git add ."
    echo "  git commit"
    echo "  git push origin main"
    echo "  php bin/console cache:clear --env=prod"
    exit 1
fi

# Étape 6 : Pusher vers GitHub
echo -e "\n${YELLOW}📤 Étape 6/6 : Push vers GitHub${NC}"
git push origin main
echo -e "${GREEN}✅ Changements pushés${NC}"

# Étape 7 : Nettoyer le cache de production
echo -e "\n${YELLOW}🧹 Étape 7/7 : Nettoyage du cache de production${NC}"
php bin/console cache:clear --env=prod
echo -e "${GREEN}✅ Cache nettoyé${NC}"

# Récapitulatif
echo -e "\n${GREEN}✅ DÉPLOIEMENT RÉUSSI !${NC}"
echo "========================================"
echo -e "${BLUE}🌐 Site de production :${NC} https://infpf.fr/"
echo -e "${BLUE}🔧 Site de développement :${NC} https://dev.infpf.fr/"
echo -e "${BLUE}💾 Backup créé :${NC} $BACKUP_BRANCH"
echo ""
echo -e "${YELLOW}📝 Prochaines étapes :${NC}"
echo "  1. Tester sur https://infpf.fr/"
echo "  2. Continuer à développer sur la branche dev"
echo ""
echo -e "${YELLOW}⚠️  En cas de problème :${NC}"
echo "  git checkout main"
echo "  git reset --hard $BACKUP_BRANCH"
echo "  git push origin main --force"

