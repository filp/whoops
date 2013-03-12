<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use Damnit\Exception\FrameIterator;
use \Exception;

class Inspector
{
    /**
     * @var Exception
     */
    private $exception;

    /**
     * @param Exception $exception The exception to inspect
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Returns an iterator for the inspected exception's
     * frames.
     * @return DamnIt\Exception\FrameIterator
     */
    public function getFrames()
    {
        $iterator = new FrameIterator($this->exception->getTrace());
        return $iterator;
    }
}
