<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace DamnIt\Handler;
use DamnIt\Handler\Handler;

class DummyHandler extends Handler
{
    /**
     * @var \Exception[]
     */
    public $exceptions = array();

    /**
     * @param \Exception
     * @return int|null
     */
    public function handle(\Exception $exception)
    {
        $this->exceptions[] = $exception;
    }
}
