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
        ];
    }

    public function formatFormationText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $trimmed = trim($text);

        if ($this->isHtmlContent($trimmed)) {
            return $this->sanitizeHtml($trimmed);
        }

        return $this->convertPlainTextToHtml($trimmed);
    }

    private function isHtmlContent(string $text): bool
    {
        return preg_match('/<(p|ul|ol|li|h[1-6]|div|br|strong|em|a|blockquote)\b/i', $text) === 1;
    }

    /**
     * Nettoie le HTML provenant de l'editeur WYSIWYG.
     * On garde uniquement les balises sures pour le contenu formation.
     */
    private function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><h4><h5><h6><a><blockquote><pre><code><div><span><hr><sub><sup>';
        return strip_tags($html, $allowed);
    }

    /**
     * Convertit du texte brut (legacy) en HTML basique.
     * Utilise des regles simples : double saut = paragraphe, saut simple = <br>.
     */
    private function convertPlainTextToHtml(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        $paragraphs = preg_split('/\n\s*\n/', $text);
        $paragraphs = array_filter(array_map('trim', $paragraphs));

        if (count($paragraphs) <= 1) {
            return '<p>' . nl2br(trim($text)) . '</p>';
        }

        $html = '';
        foreach ($paragraphs as $paragraph) {
            if (!empty(trim($paragraph))) {
                $html .= '<p>' . nl2br(trim($paragraph)) . '</p>';
            }
        }

        return $html;
    }
}