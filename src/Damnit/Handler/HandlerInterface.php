<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;

interface HandlerInterface
{
    /**
     * @param \Exception
     * @return int|null  A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle(\Exception $exception);
}
