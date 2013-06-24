<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\Handler\Handler;
use InvalidArgumentException;
use RuntimeException;

class PrettyPageHandler extends Handler
{
    /**
     * Search paths to be scanned for resources, in the reverse
     * order they're declared.
     * 
     * @var array
     */
    private $searchPaths = array();

    /**
     * Fast lookup cache for known resource locations.
     * 
     * @var array
     */
    private $resourceCache = array();

    /**
     * @var array[]
     */
    private $extraTables = array();

    /**
     * @var string
     */
    private $pageTitle = "Whoops! There was an error";

    /**
     * A string identifier for a known IDE/text editor, or a closure
     * that resolves a string that can be used to open a given file
     * in an editor. If the string contains the special substrings
     * %file or %line, they will be replaced with the correct data.
     *
     * @example
     *  "txmt://open?url=%file&line=%line"
     * @var mixed $editor
     */
    protected $editor;

    /**
     * A list of known editor strings
     * @var array
     */
    protected $editors = array(
        "sublime"  => "subl://open?url=file://%file&line=%line",
        "textmate" => "txmt://open?url=file://%file&line=%line",
        "emacs"    => "emacs://open?url=file://%file&line=%line",
        "macvim"   => "mvim://open/?url=file://%file&line=%line"
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (extension_loaded('xdebug')) {
            // Register editor using xdebug's file_link_format option.
            $this->editors['xdebug'] = function($file, $line) {
                return str_replace(array('%f', '%l'), array($file, $line), ini_get('xdebug.file_link_format'));
            };
        }

        // Add the default, local resource search path:
        $this->searchPaths[] = __DIR__ . "/../Resources";
    }

    /**
     * @return int|null
     */
    public function handle()
    {
        // Check conditions for outputting HTML:
        // @todo: make this more robust
        if(php_sapi_name() === 'cli' && !isset($_ENV['whoops-test'])) {
            return Handler::DONE;
        }

        $templateFile = $this->getResource("pretty-template.php");
        $cssFile      = $this->getResource("pretty-page.css");

        // Prepare the $v global variable that will pass relevant
        // information to the template
        $inspector = $this->getInspector();
        $frames    = $inspector->getFrames();

        $v = (object) array(
            'title'        => $this->getPageTitle(),
            'name'         => explode('\\', $inspector->getExceptionName()),
            'message'      => $inspector->getException()->getMessage(),
            'frames'       => $frames,
            'hasFrames'    => !!count($frames),
            'handler'      => $this,
            'handlers'     => $this->getRun()->getHandlers(),
            'pageStyle'    => file_get_contents($cssFile),

            'tables'      => array(
                'Server/Request Data'   => $_SERVER,
                'GET Data'              => $_GET,
                'POST Data'             => $_POST,
                'Files'                 => $_FILES,
                'Cookies'               => $_COOKIE,
                'Session'               => isset($_SESSION) ? $_SESSION:  array(),
                'Environment Variables' => $_ENV
            )
        );

        $extraTables = array_map(function($table) {
            return $table instanceof \Closure ? $table() : $table;
        }, $this->getDataTables());

        // Add extra entries list of data tables:
        $v->tables = array_merge($extraTables, $v->tables);

        call_user_func(function() use($templateFile, $v) {
            // $e -> cleanup output, optionally preserving URIs as anchors:
            $e = function($_, $allowLinks = false) {
                $escaped = htmlspecialchars($_, ENT_QUOTES, 'UTF-8');

                // convert URIs to clickable anchor elements:
                if($allowLinks) {
                    $escaped = preg_replace(
                        '@([A-z]+?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
                        "<a href=\"$1\" target=\"_blank\">$1</a>", $escaped
                    );
                }

                return $escaped;
            };

            // $slug -> sluggify string (i.e: Hello world! -> hello-world)
            $slug = function($_) {
                $_ = str_replace(" ", "-", $_);
                $_ = preg_replace('/[^\w\d\-\_]/i', '', $_);
                return strtolower($_);
            };

            require $templateFile;
        });


        return Handler::QUIT;
    }

    /**
     * Adds an entry to the list of tables displayed in the template.
     * The expected data is a simple associative array. Any nested arrays
     * will be flattened with print_r
     * @param string $label
     * @param array  $data
     */
    public function addDataTable($label, array $data)
    {
        $this->extraTables[$label] = $data;
    }

    /**
     * Lazily adds an entry to the list of tables displayed in the table.
     * The supplied callback argument will be called when the error is rendered,
     * it should produce a simple associative array. Any nested arrays will
     * be flattened with print_r.
     * @param string   $label
     * @param callable $callback Callable returning an associative array
     */
    public function addDataTableCallback($label, /* callable */ $callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Expecting callback argument to be callable');
        }

        $this->extraTables[$label] = function() use ($callback) {
            try {
                $result = call_user_func($callback);

                // Only return the result if it can be iterated over by foreach().
                return is_array($result) || $result instanceof \Traversable ? $result : array();
            } catch (\Exception $e) {
                // Don't allow failiure to break the rendering of the original exception.
                return array();
            }
        };
    }

    /**
     * Returns all the extra data tables registered with this handler.
     * Optionally accepts a 'label' parameter, to only return the data
     * table under that label.
     * @param string|null $label
     * @return array[]
     */
    public function getDataTables($label = null)
    {
        if($label !== null) {
            return isset($this->extraTables[$label]) ?
                   $this->extraTables[$label] : array();
        }

        return $this->extraTables;
    }

    /**
     * Adds an editor resolver, identified by a string
     * name, and that may be a string path, or a callable
     * resolver. If the callable returns a string, it will
     * be set as the file reference's href attribute.
     *
     * @example
     *  $run->addEditor('macvim', "mvim://open?url=file://%file&line=%line")
     * @example
     *   $run->addEditor('remove-it', function($file, $line) {
     *       unlink($file);
     *       return "http://stackoverflow.com";
     *   });
     * @param  string $identifier
     * @param  string $resolver
     */
    public function addEditor($identifier, $resolver)
    {
        $this->editors[$identifier] = $resolver;
    }

    /**
     * Set the editor to use to open referenced files, by a string
     * identifier, or a callable that will be executed for every
     * file reference, with a $file and $line argument, and should
     * return a string.
     *
     * @example
     *   $run->setEditor(function($file, $line) { return "file:///{$file}"; });
     * @example
     *   $run->setEditor('sublime');
     *
     * @param string|callable $editor
     */
    public function setEditor($editor)
    {
        if(!is_callable($editor) && !isset($this->editors[$editor])) {
            throw new InvalidArgumentException(
                "Unknown editor identifier: $editor. Known editors:" .
                implode(",", array_keys($this->editors))
            );
        }

        $this->editor = $editor;
    }

    /**
     * Given a string file path, and an integer file line,
     * executes the editor resolver and returns, if available,
     * a string that may be used as the href property for that
     * file reference.
     *
     * @param  string $filePath
     * @param  int    $line
     * @return string|false
     */
    public function getEditorHref($filePath, $line)
    {
        if($this->editor === null) {
            return false;
        }

        $editor = $this->editor;
        if(is_string($editor)) {
            $editor = $this->editors[$editor];
        }

        if(is_callable($editor)) {
            $editor = call_user_func($editor, $filePath, $line);
        }

        // Check that the editor is a string, and replace the
        // %line and %file placeholders:
        if(!is_string($editor)) {
            throw new InvalidArgumentException(
                __METHOD__ . " should always resolve to a string; got something else instead"
            );
        }

        $editor = str_replace("%line", rawurlencode($line), $editor);
        $editor = str_replace("%file", rawurlencode($filePath), $editor);

        return $editor;
    }

    /**
     * @var string
     */
    public function setPageTitle($title)
    {
        $this->pageTitle = (string) $title;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @deprecated 
     * 
     * @return string
     */
    public function getResourcesPath()
    {
        trigger_error(__METHOD__ . " is deprecated by PrettyPageHandler::getResourcePaths", E_USER_NOTICE);

        $allPaths = $this->getResourcePaths();

        // Compat: return only the first path
        return reset($allPaths) ?: null;
    }

    /**
     * @deprecated
     * 
     * @param string $resourcesPath
     */
    public function setResourcesPath($resourcesPath)
    {
        trigger_error(__METHOD__ . " is deprecated by PrettyPageHandler::addResourcePath", E_USER_NOTICE);
        $this->addResourcePath($resourcesPath);
    }

    /**
     * Adds a path to the list of paths to be searched for
     * resources.
     * 
     * @throws InvalidArgumnetException If $path is not a valid directory
     * 
     * @param string $path
     */
    public function addResourcePath($path)
    {
        if(!is_dir($path)) {
            throw new InvalidArgumentException(
                "'$path' is not a valid directory"
            );
        }

        $this->searchPaths[] = $path;
    }

    /**
     * @return array
     */
    public function getResourcePaths()
    {
        return $this->searchPaths;
    }

    /**
     * Finds a resource, by its relative path, in all available search paths.
     * The search is performed starting at the last search path, and all the
     * way back to the first, enabling a cascading-type system of overrides
     * for all resources.
     * 
     * @throws RuntimeException If resource cannot be found in any of the available paths
     * 
     * @param  string $resource
     * @return string
     */
    protected function getResource($resource)
    {
        // If the resource was found before, we can speed things up
        // by caching its absolute, resolved path:
        if(isset($this->resourceCache[$resource])) {
            return $this->resourceCache[$resource];
        }

        // Search through available search paths, in reverse order,
        // until we find the resource we're after:
        for($i = count($this->searchPaths) - 1; $i >= 0; $i--) {
            $fullPath = $this->searchPaths[$i] . "/$resource";

            if(is_file($fullPath)) {
                // Cache the result:
                $this->resourceCache[$resource] = $fullPath;
                return $fullPath;
            }
        }

        // If we got this far, nothing was found.
        throw new RuntimeException(
            "Could not find resource '$resource' in any resource paths."
            . "(searched: " . join(", ", $this->searchPaths). ")"
        );
    }
}
