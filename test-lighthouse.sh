#!/bin/bash

# Script de test Lighthouse pour toutes les pages INFPF
# Usage: bash test-lighthouse.sh

# Créer le dossier reports s'il n'existe pas
mkdir -p reports/lighthouse

# Timestamp pour les rapports
TS=$(date +%Y%m%d-%H%M%S)

echo "🚀 Démarrage des tests Lighthouse..."
echo "📅 Date: $(date)"
echo ""

# Liste des pages à tester
declare -A PAGES
PAGES["home"]="https://dev.infpf.fr/"
PAGES["formation-list"]="https://dev.infpf.fr/formation"
PAGES["formation-detail-87"]="https://dev.infpf.fr/formation/87"
PAGES["formation-detail-88"]="https://dev.infpf.fr/formation/88"
PAGES["formation-detail-42"]="https://dev.infpf.fr/formation/42"
PAGES["blog-index"]="https://dev.infpf.fr/blog/"
PAGES["metiers"]="https://dev.infpf.fr/metiers"
PAGES["financer"]="https://dev.infpf.fr/financer-ma-formation"
PAGES["cpf"]="https://dev.infpf.fr/formations-eligibles-cpf"
PAGES["contact"]="https://dev.infpf.fr/contactez-nous"

# Fonction de test
test_page() {
    local page_name=$1
    local url=$2
    
    echo "📊 Test de: $page_name ($url)"
    
    # Test Desktop
    echo "  → Desktop..."
    curl -s "$url" -w "\n⏱️  Temps de réponse: %{time_total}s\n" -o /dev/null
    
    # Test Mobile (simulation)
    echo "  → Mobile..."
    curl -s "$url" -H "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)" -w "⏱️  Temps de réponse: %{time_total}s\n" -o /dev/null
    
    echo ""
}

# Tester chaque page
for page_name in "${!PAGES[@]}"; do
    test_page "$page_name" "${PAGES[$page_name]}"
done

echo "✅ Tests terminés!"
echo ""
echo "📁 Pour des tests Lighthouse complets, installez Lighthouse CLI:"
echo "   npm install -g lighthouse"
echo ""
echo "📊 Puis testez une page:"
echo "   lighthouse https://dev.infpf.fr/formation --only-categories=performance --view"











