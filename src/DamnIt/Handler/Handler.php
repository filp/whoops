<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace DamnIt\Handler;
use DamnIt\Handler\HandlerInterface;
use DamnIt\Run;

class Handler implements HandlerInterface
{
    /**
     * Can be used as priorities with Run::addHandler,
     * to ensure the handler is added to the end or
     * beginning of the handler stack.
     *
     * @example Low priority handler
     *     $run->addHandler($myHandler, Handler::LOW_PRIORITY);
     */
    const LOW_PRIORITY  = 0x10;
    const HIGH_PRIORITY = 0x20;

    /**
     * @var DamnIt\Run
     */
    private $run;

    /**
     * @param DamnIt\Run $run
     */
    public function __construct(Run $run = null)
    {
        if($run !== null) {
            $this->run = $run;
        }
    }

    /**
     * @return DamnIt\Run
     */
    protected function getRun()
    {
        return $this->run;
    }

    /**
     * @param \Exception
     * @return int|null
     */
    public function handle(\Exception $exception)
    {
        $this->getRun()->unregister();
        throw new $exception;
    }
}
