<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;

use Damnit\Exception\Inspector;
use Damnit\Run;

interface HandlerInterface
{
    /**
     * @param \Exception
     * @return int|null  A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle(\Exception $exception);

    /**
     * @param Damnit\Run
     */
    public function setRun(Run $run);


    /**
     * @param Damnit\Exception\Inspector
     */
    public function setInspector(Inspector $run);
}
