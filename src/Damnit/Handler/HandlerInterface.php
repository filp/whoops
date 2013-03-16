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
     * @param Damnit\Run $run
     */
    public function setRun(Run $run);

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception);

    /**
     * @param Damnit\Exception\Inspector $run
     */
    public function setInspector(Inspector $run);
}
