<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Whoops\Exception\Frame;

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
        $flags = ENT_QUOTES;

        // HHVM has all constants defined, but only ENT_IGNORE
        // works at the moment
        if (defined("ENT_SUBSTITUTE") && !defined("HHVM_VERSION")) {
            $flags |= ENT_SUBSTITUTE;
        } else {
            // This is for 5.3.
            // The documentation warns of a potential security issue,
            // but it seems it does not apply in our case, because
            // we do not blacklist anything anywhere.
            $flags |= ENT_IGNORE;
        }

        return htmlspecialchars($raw, $flags, "UTF-8");
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
     * Format the given value into a human readable string.
     *
     * @param  mixed $value
     * @return string
     */
    public function dump($value)
    {
        if (class_exists('Symfony\Component\VarDumper\Cloner\VarCloner')) {
            static $dumper = null;

            // re-use the same var-dumper instance, so it won't re-render the global styles/scripts on each dump.
            if (!$dumper) {
                $dumper = new HtmlDumper();

                $styles = array(
                    'default' => '',
                    'num' => '',
                    'const' => '',
                    'str' => '',
                    'note' => '',
                    'ref' => '',
                    'public' => '',
                    'protected' => '',
                    'private' => '',
                    'meta' => '',
                    'key' => '',
                    'index' => '',
                );
                $dumper->setStyles($styles);
            }

            $cloner = new VarCloner();
            return $dumper->dump($cloner->cloneVar($value));
        }
        return print_r($value, true);
    }

    /**
     * Format the args of the given Frame as a human readable html string
     *
     * @param  Frame $frame
     * @return string the rendered html
     */
    public function dumpArgs(Frame $frame)
    {
        $html = '';
        $numFrames = count($frame->getArgs());

        if ($numFrames) {
            $html .= '(';

            foreach($frame->getArgs() as $j => $frameArg) {
                $class = 'frame-arg';
                if ($j != $numFrames - 1 ) {
                    $class .= ' frame-arg-separated';
                }
                $html .= '<span class="'. $class .'">'. $this->dumpArgType($frameArg) .'</span>';
            }

            $html .= ')';
        }

        return $html;
    }

    /**
     * Format the arg to a human readable short value
     *
     * @param  Frame $frameArg
     * @return string the rendered html
     */
    public function dumpArgType($frameArg) {
        if (is_null($frameArg))
            return '<span class="frame-arg-type-null">null</span>';

        if (is_bool($frameArg))
            return '<span class="frame-arg-type-bool">' . ($frameArg ? 'true' : 'false') . '</span>';

        if (is_string($frameArg)) {
            $html = '<span class="frame-arg-type-string">string (' . strlen($frameArg) . ') ';

            if ($frameArg !== '')
                $html .= htmlspecialchars(var_export($frameArg, true));

            $html .= '</span>';

            return $html;
        }

        if (is_resource($frameArg))
            return 'Resource(' . get_resource_type($frameArg) . ')';

        if (is_scalar($frameArg) || is_null($frameArg))
            return $this->dump($frameArg, true);

        if (is_object($frameArg))
            return 'Object(' . get_class($frameArg) . ')';

        if (is_array($frameArg))
            return 'array(' . count($frameArg) . ') { ... }';

        return '[UNKNOWN]';
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
     * @param string $template
     * @param array  $additionalVariables
     */
    public function render($template, array $additionalVariables = null)
    {
        $variables = $this->getVariables();

        // Pass the helper to the template:
        $variables["tpl"] = $this;

        if ($additionalVariables !== null) {
            $variables = array_replace($variables, $additionalVariables);
        }

        call_user_func(function () {
            extract(func_get_arg(1));
            require func_get_arg(0);
        }, $template, $variables);
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
            $this->variables[$variableName] : $defaultValue;
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
