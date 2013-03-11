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
