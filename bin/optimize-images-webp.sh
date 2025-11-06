#!/bin/bash

###############################################################################
# Script d'optimisation des images en WebP pour INFPF
# Compatible Hostinger - Utilise cwebp (outil Google)
#
# Usage: ./bin/optimize-images-webp.sh [--all|--new]
#   --all   : Convertit toutes les images (JPEG/PNG)
#   --new   : Convertit uniquement les nouvelles images (sans .webp)
#   (par défaut : --new)
#
# ⚠️ Nécessite cwebp : sudo apt install webp (ou yum install libwebp-tools)
###############################################################################

set -euo pipefail

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE_DIRS=("$PROJECT_DIR/public/img" "$PROJECT_DIR/public/uploads/images")
QUALITY=85  # Qualité WebP (0-100, recommandé: 80-90)
MODE="${1:---new}"  # Par défaut: --new

# Compteurs
TOTAL_CONVERTED=0
TOTAL_SKIPPED=0
TOTAL_SIZE_SAVED=0

###############################################################################
# Vérification de cwebp
###############################################################################
if ! command -v cwebp &> /dev/null; then
    echo -e "${RED}❌ Erreur : cwebp n'est pas installé.${NC}"
    echo ""
    echo "Installation selon votre système :"
    echo "  - Ubuntu/Debian : sudo apt install webp"
    echo "  - CentOS/RHEL   : sudo yum install libwebp-tools"
    echo "  - macOS         : brew install webp"
    echo ""
    echo "Via Hostinger, contactez le support pour installer webp."
    exit 1
fi

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}🚀 Optimisation des images en WebP${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""
echo -e "${GREEN}Mode sélectionné : $MODE${NC}"
echo -e "${GREEN}Qualité WebP     : $QUALITY${NC}"
echo ""

###############################################################################
# Fonction de conversion
###############################################################################
convert_to_webp() {
    local input_file="$1"
    local output_file="${input_file%.*}.webp"
    
    # Si --new et que le .webp existe déjà, on skip
    if [[ "$MODE" == "--new" ]] && [[ -f "$output_file" ]]; then
        echo -e "${YELLOW}⏭️  Déjà converti : $(basename "$output_file")${NC}"
        ((TOTAL_SKIPPED++))
        return
    fi
    
    # Conversion avec cwebp
    if cwebp -q "$QUALITY" "$input_file" -o "$output_file" &> /dev/null; then
        # Calcul de l'espace gagné
        local original_size=$(stat -c%s "$input_file" 2>/dev/null || stat -f%z "$input_file")
        local webp_size=$(stat -c%s "$output_file" 2>/dev/null || stat -f%z "$output_file")
        local saved=$((original_size - webp_size))
        local saved_mb=$(echo "scale=2; $saved / 1024 / 1024" | bc)
        
        TOTAL_SIZE_SAVED=$((TOTAL_SIZE_SAVED + saved))
        ((TOTAL_CONVERTED++))
        
        echo -e "${GREEN}✅ Converti : $(basename "$output_file") (gain: ${saved_mb} MB)${NC}"
    else
        echo -e "${RED}❌ Erreur lors de la conversion de $(basename "$input_file")${NC}"
    fi
}

###############################################################################
# Parcours des répertoires
###############################################################################
for DIR in "${IMAGE_DIRS[@]}"; do
    if [[ ! -d "$DIR" ]]; then
        echo -e "${YELLOW}⚠️  Répertoire introuvable : $DIR${NC}"
        continue
    fi
    
    echo ""
    echo -e "${BLUE}📂 Traitement : $DIR${NC}"
    echo ""
    
    # Trouver toutes les images JPEG/PNG
    find "$DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) | while read -r img; do
        convert_to_webp "$img"
    done
done

###############################################################################
# Résumé
###############################################################################
TOTAL_SAVED_MB=$(echo "scale=2; $TOTAL_SIZE_SAVED / 1024 / 1024" | bc)

echo ""
echo -e "${BLUE}================================================${NC}"
echo -e "${GREEN}✅ Optimisation terminée !${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""
echo -e "${GREEN}📊 Images converties : $TOTAL_CONVERTED${NC}"
echo -e "${YELLOW}⏭️  Images ignorées   : $TOTAL_SKIPPED${NC}"
echo -e "${GREEN}💾 Espace économisé  : ${TOTAL_SAVED_MB} MB${NC}"
echo ""
echo -e "${BLUE}💡 Prochaines étapes :${NC}"
echo "  1. Vérifie que les .webp sont bien générés"
echo "  2. Active le mod_rewrite dans .htaccess (déjà fait)"
echo "  3. Teste avec : curl -H \"Accept: image/webp\" -I https://dev.infpf.fr/img/logo.png"
echo ""




