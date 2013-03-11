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
     * @var callable
     */
    protected $onHandleCallable;

    /**
     * @param \Exception
     * @return int|null
     */
    public function handle(\Exception $exception)
    {
        $this->exceptions[] = $exception;
        if($this->onHandleCallable) {
            call_user_func($this->onHandleCallable, $exception);
        }
    }

    /**
     * Set a callable to be executed when DummyHandler::handle
     * is executed. The callable receives the exception passed
     * to the method.
     */
    public function onHandle($callable)
    {
        $this->onHandleCallable = $callable;
    }
}
