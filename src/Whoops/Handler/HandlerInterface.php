<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use Exception;
use Whoops\Exception\Inspector;
use Whoops\Run;

interface HandlerInterface
{
    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle();

    /**
     * @param  Run  $run
     * @return void
     */
    public function setRun(Run $run);

    /**
     * @param  Exception $exception
     * @return void
     */
    public function setException(Exception $exception);

    /**
     * @param  Inspector $inspector
     * @return void
     */
    public function setInspector(Inspector $inspector);
}
