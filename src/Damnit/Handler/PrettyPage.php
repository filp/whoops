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
            'name'      => explode('\\', $inspector->getExceptionName()),
            'message'   => $inspector->getException()->getMessage(),
            'frames'    => $frames,
            'hasFrames' => !!count($frames)
        );

        call_user_func(function() use($templateFile, $v) {
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
