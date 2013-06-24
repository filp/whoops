<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;

/**
 * Exposes useful tools for working with/in templates
 */
class TemplateHelper
{
    /**
     * Escapes a string for output in an HTML document
     *
     * @param  string $raw
     * @return string
     */
    public function escape($raw)
    {
        return htmlspecialchars($raw, ENT_QUOTES, "UTF-8");
    }

    /**
     * Escapes a string for output in an HTML document, but preserves
     * URIs within it, and converts them to clickable anchor elements.
     *
     * @param  string $raw
     * @return string
     */
    public function escapeButPreserveUris($raw)
    {
        $escaped = $this->escape($raw);
        return preg_replace(
            "@([A-z]+?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@",
            "<a href=\"$1\" target=\"_blank\">$1</a>", $escaped
        );
    }

    /**
     * Convert a string to a slug version of itself
     * 
     * @param  string $original
     * @return string
     */
    public function slug($original)
    {
        $slug = str_replace(" ", "-", $original);
        $slug = preg_replace('/[^\w\d\-\_]/i', '', $slug);
        return strtolower($slug);
    }
}