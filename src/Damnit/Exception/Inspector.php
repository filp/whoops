<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use \Exception;

/**
 * The Inspector is able to lazily inspect an
 * exception, and gather information about its
 * related components.
 */
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
}
