<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;
use Damnit\Exception\Inspector;
use Damnit\Run;
use Exception;

interface HandlerInterface
{
    /**
     * @return int|null  A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle();

    /**
     * @param Damnit\Run
     */
    public function setRun(Run $run);

    /**
     * @param Exception
     */
    public function setException(Exception $exception);

    /**
     * @param Damnit\Exception\Inspector
     */
    public function setInspector(Inspector $run);
}
