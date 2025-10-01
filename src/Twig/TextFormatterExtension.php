<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextFormatterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_formation_text', [$this, 'formatFormationText'], ['is_safe' => ['html']]),
            new TwigFilter('format_list_text', [$this, 'formatListText'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Formate un texte de formation en préservant strictement le contenu
     * - Respecte les retours à la ligne existants
     * - Convertit les puces en listes HTML si présentes
     * - Sépare les paragraphes sur les doubles retours à la ligne
     * - Corrige les problèmes d'encodage HTML
     * 
     * @param string|null $text
     * @return string
     */
    public function formatFormationText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Normaliser les retours à la ligne (Windows/Unix)
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Décoder les entités HTML si déjà présentes (éviter double-échappement)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Échapper les caractères HTML pour la sécurité UNIQUEMENT si pas déjà sûr
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        // Détecter si le texte contient des puces (•, -, *)
        $hasBullets = preg_match('/^[\s]*[•\-\*]\s+/m', $text);
        
        if ($hasBullets) {
            return $this->formatListText($text);
        }
        
        // Séparer les paragraphes sur les doubles retours à la ligne
        $paragraphs = preg_split('/\n\s*\n/', $text);
        $paragraphs = array_filter(array_map('trim', $paragraphs));
        
        if (count($paragraphs) <= 1) {
            // Un seul paragraphe, préserver les retours simples
            return '<p>' . nl2br(trim($text)) . '</p>';
        }
        
        // Plusieurs paragraphes détectés
        $html = '';
        foreach ($paragraphs as $paragraph) {
            if (!empty(trim($paragraph))) {
                $html .= '<p>' . nl2br(trim($paragraph)) . '</p>';
            }
        }
        
        return $html;
    }

    /**
     * Formate un texte contenant des listes à puces
     * - Convertit les puces (•, -, *) en vraies listes HTML
     * - Détecte automatiquement les sous-titres (MAJUSCULES, lignes avec :)
     * - Corrige les problèmes d'encodage HTML
     * 
     * @param string|null $text
     * @return string
     */
    public function formatListText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Normaliser les retours à la ligne
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Décoder les entités HTML si déjà présentes (éviter double-échappement)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Échapper pour la sécurité UNIQUEMENT si pas déjà sûr
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        $lines = explode("\n", $text);
        $html = '';
        $inList = false;
        $currentParagraph = '';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Ligne vide
            if (empty($trimmedLine)) {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                if (!empty($currentParagraph)) {
                    $html .= '<p>' . trim($currentParagraph) . '</p>';
                    $currentParagraph = '';
                }
                continue;
            }
            
            // Ligne avec puce
            if (preg_match('/^[\s]*([•\-\*])\s+(.+)$/', $trimmedLine, $matches)) {
                if (!empty($currentParagraph)) {
                    $html .= '<p>' . trim($currentParagraph) . '</p>';
                    $currentParagraph = '';
                }
                
                if (!$inList) {
                    $html .= '<ul>';
                    $inList = true;
                }
                
                $html .= '<li>' . trim($matches[2]) . '</li>';
            } 
            // Ligne qui ressemble à un sous-titre (MAJUSCULES complètes ou avec : à la fin)
            elseif (
                preg_match('/^[A-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ\s\d]+:?\s*$/', $trimmedLine) 
                && strlen($trimmedLine) > 3 
                && strlen($trimmedLine) < 80
                && !preg_match('/\b(de|du|des|le|la|les|un|une|et|ou|à|pour|dans|avec|sur|par|vers|sous|sans|entre|depuis|jusqu|pendant)\b/i', $trimmedLine)
            ) {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                if (!empty($currentParagraph)) {
                    $html .= '<p>' . trim($currentParagraph) . '</p>';
                    $currentParagraph = '';
                }
                
                $html .= '<h4>' . trim($trimmedLine) . '</h4>';
            } else {
                // Ligne normale
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                
                if (!empty($currentParagraph)) {
                    $currentParagraph .= ' ';
                }
                $currentParagraph .= $trimmedLine;
            }
        }
        
        // Fermer les éléments ouverts
        if ($inList) {
            $html .= '</ul>';
        }
        
        if (!empty($currentParagraph)) {
            $html .= '<p>' . trim($currentParagraph) . '</p>';
        }
        
        return $html;
    }
}