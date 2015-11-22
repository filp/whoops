<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use Whoops\Exception\Formatter;

/**
 * Catches an exception and converts it to a JSON
 * response. Additionally can also return exception
 * frames for consumption by an API.
 */
class JsonResponseHandler extends Handler
{
    /**
     * @var bool
     */
    private $returnFrames = false;

    /**
     * @param  bool|null  $returnFrames
     * @return bool|$this
     */
    public function addTraceToOutput($returnFrames = null)
    {
        if (func_num_args() == 0) {
            return $this->returnFrames;
        }

        $this->returnFrames = (bool) $returnFrames;
        return $this;
    }

    /**
     * @return int
     */
    public function handle()
    {
        $response = array(
            'error' => Formatter::formatExceptionAsDataArray(
                $this->getInspector(),
                $this->addTraceToOutput()
            ),
        );

        if (\Whoops\Util\Misc::canSendHeaders()) {
            header('Content-Type: application/json');
        }

        echo json_encode($response, defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0);

        return Handler::QUIT;
    }
}
