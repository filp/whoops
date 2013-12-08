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
     * An array of variables to be passed to all templates
     * @var array
     */
    private $variables = array();

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

    /**
     * Given a template path, render it within its own scope. This
     * method also accepts an array of additional variables to be
     * passed to the template.
     *
     * @param string $__template
     * @param array  $additionalVariables
     */
    public function render($__template, array $additionalVariables = null)
    {
        $__variables = $this->getVariables();

        // Pass the helper to the template:
        $__variables["tpl"] = $this;

        if($additionalVariables !== null) {
            $__variables = array_replace($__variables, $additionalVariables);
        }

        call_user_func(function() use($__template, $__variables) {
            extract($__variables);
            require $__template;
        });
    }

    /**
     * Sets the variables to be passed to all templates rendered
     * by this template helper.
     *
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * Sets a single template variable, by its name:
     *
     * @param string $variableName
     * @param mixd   $variableValue
     */
    public function setVariable($variableName, $variableValue)
    {
        $this->variables[$variableName] = $variableValue;
    }

    /**
     * Gets a single template variable, by its name, or
     * $defaultValue if the variable does not exist
     *
     * @param  string $variableName
     * @param  mixed  $defaultValue
     * @return mixed
     */
    public function getVariable($variableName, $defaultValue = null)
    {
        return isset($this->variables[$variableName]) ?
            $this->variables[$variableName] : $defaultValue
        ;
    }

    /**
     * Unsets a single template variable, by its name
     *
     * @param string $variableName
     */
    public function delVariable($variableName)
    {
        unset($this->variables[$variableName]);
    }

    /**
     * Returns all variables for this helper
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
}
