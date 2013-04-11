<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\Exception\Inspector;
use Whoops\Run;
use Exception;

interface HandlerInterface
{
    /**
     * @return int|null  A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle();

    /**
     * @param Whoops\Run $run
     */
    public function setRun(Run $run);

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception);

    /**
     * @param Whoops\Exception\Inspector $run
     */
    public function setInspector(Inspector $inspector);
}
