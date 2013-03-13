<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;
use Damnit\Handler\Handler;
use \InvalidArgumentException;

class PrettyPage extends Handler
{
    /**
     * @var string
     */
    private $resourcesPath;

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
