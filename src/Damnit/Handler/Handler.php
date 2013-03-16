<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;
use Damnit\Handler\HandlerInterface;
use Damnit\Exception\Inspector;
use Damnit\Run;
use Exception;

/**
 * Abstract implementation of a Handler.
 */
abstract class Handler implements HandlerInterface
{
    /**
     * Return constants that can be returned from Handler::handle
     * to message the handler walker.
     */
    const DONE         = 0x10; // returning this is optional, only exists for
                               // semantic purposes
    const LAST_HANDLER = 0x20;
    const QUIT         = 0x30;

    /**
     * @var Damnit\Run
     */
    private $run;

    /**
     * @var Damnit\Exception\Inspector $inspector
     */
    private $inspector;

    /**
     * @var Exception $exception
     */
    private $exception;

    /**
     * @param Damnit\Run $run
     */
    public function setRun(Run $run)
    {
        $this->run = $run;
    }

    /**
     * @return Damnit\Run
     */
    protected function getRun()
    {
        return $this->run;
    }

    /**
     * @param Damnit\Exception\Inspector $inspector
     */
    public function setInspector(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    /**
     * @return Damnit\Run
     */
    protected function getInspector()
    {
        return $this->inspector;
    }

    /**
     * @param Exception
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Exception
     */
    protected function getException()
    {
        return $this->exception;
    }
}
