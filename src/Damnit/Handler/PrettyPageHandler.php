<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;
use Damnit\Handler\Handler;
use InvalidArgumentException;

class PrettyPageHandler extends Handler
{
    /**
     * @var string
     */
    private $resourcesPath;

    /**
     * @var array[]
     */
    private $extraTables = array();

    /**
     * @return int|null
     */
    public function handle()
    {
        // Check conditions for outputting HTML:
        // @todo: make this more robust
        if(php_sapi_name() === 'cli') {
            return;
        }

        // Get the 'pretty-template.php' template file
        // @todo: this can be made more dynamic &&|| cleaned-up
        if(!($resources = $this->getResourcesPath())) {
            $resources = __DIR__ . '/../Resources';
        }
        $templateFile = "$resources/pretty-template.php";

        // Prepare the $v global variable that will pass relevant
        // information to the template
        $inspector = $this->getInspector();
        $frames = $inspector->getFrames();

        $v = (object) array(
            'name'        => explode('\\', $inspector->getExceptionName()),
            'message'     => $inspector->getException()->getMessage(),
            'frames'      => $frames,
            'hasFrames'   => !!count($frames),
            'handler'     => $this,
            'handlers'    => $this->getRun()->getHandlers(),

            'super'       => array(
                'GET Data'              => $_GET,
                'POST Data'             => $_POST,
                'Files'                 => $_FILES,
                'Cookies'               => $_COOKIE,
                'Session'               => isset($_SESSION) ? $_SESSION:  array(),
                'Server/Request Data'   => $_SERVER,
                'Environment Variables' => $_ENV
            )
        );

        // Add extra entries to the "super" list of data tables:
        $v->super = array_merge($v->super, $this->getDataTables());

        call_user_func(function() use($templateFile, $v) {
            // $e -> cleanup output
            $e    = function($_) { return htmlspecialchars($_, ENT_QUOTES, 'UTF-8'); };

            // $slug -> sluggify string (i.e: Hello world! -> hello-world)
            $slug = function($_) {
                $_ = str_replace(" ", "-", $_);
                $_ = preg_replace('/[^\w\d\-\_]/i', '', $_);
                return strtolower($_);
            };

            require $templateFile;
        });

        return Handler::LAST_HANDLER;
    }

    /**
     * Adds an entry to the list of superglobals displayed in the template.
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
                   $this->extraTables[$labe] : array();
        }

        return $this->extraTables;
    }

    /**
     * @return string
     */
    public function getResourcesPath()
    {
        return $this->resourcesPath;
    }

    /**
     * @param string $resourcesPath
     */
    public function setResourcesPath($resourcesPath)
    {
        if(!is_dir($resourcesPath)) {
            throw new InvalidArgumentException(
                "$resourcesPath is not a valid directory"
            );
        }

        $this->resourcesPath = $resourcesPath;
    }
}
