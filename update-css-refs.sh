#!/bin/bash

# Script pour remplacer les références CSS par les versions minifiées

FILES=$(find templates -name "*.twig" -type f)

for FILE in $FILES; do
    # Ignorer les fichiers de sauvegarde
    if [[ $FILE == *"SAUVEGARDE"* ]]; then
        continue
    fi
    
    # Remplacer les références CSS (sauf celles déjà minifiées)
    sed -i.bak \
        -e 's|/css/fichier\.css|/css/fichier.min.css|g' \
        -e 's|/css/forma\.css|/css/forma.min.css|g' \
        -e 's|/css/footer1\.css|/css/footer1.min.css|g' \
        -e 's|/css/popups\.css|/css/popups.min.css|g' \
        -e 's|/css/bouton-scroll\.css|/css/bouton-scroll.min.css|g' \
        -e 's|/css/showforma\.css|/css/showforma.min.css|g' \
        -e 's|/css/newFormation\.css|/css/newFormation.min.css|g' \
        "$FILE"
    
    # Vérifier si des changements ont été faits
    if ! cmp -s "$FILE" "$FILE.bak"; then
        echo "✅ Modifié: $FILE"
    fi
done

# Nettoyer les fichiers de backup
find templates -name "*.bak" -delete

echo ""
echo "🎉 Toutes les références CSS ont été mises à jour vers les versions minifiées!"
