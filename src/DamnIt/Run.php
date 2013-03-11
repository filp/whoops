<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace DamnIt;

use DamnIt\Handler\HandlerInterface;

class Run
{
    const EXCEPTION_HANDLER = 'handleException';
    const ERROR_HANDLER     = 'handleError';


    protected $isRegistered;

    /**
     * @var DamnIt\HandlerInterface[]
     */
    protected $handlerStack = array();

    /**
     * Adds a handler to the stack. By default, it will be added
     * to the end of the stack, but an optional second argument
     * can be provided with an integer priority value.
     *
     * @param  DamnIt\HandlerInterface $handler
     * @param  int $priority
     * @return DamnIt\Run
     */
    public function addHandler(HandlerInterface $handler, $priority = null)
    {
        $this->handlerStack[] = $handler;
        return $this;
    }

    /**
     * Returns an array with all handlers, sorted by priority.
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlerStack;
    }

    /**
     * Clears all handlers in the handlerStack, including
     * the default PrettyPage handler.
     * @return DamnIt\Run
     */
    public function clearHandlers()
    {
        $this->handlerStack = array();
        return $this;
    }

    /**
     * Registers this instance as an error handler.
     * @return DamnIt\Run
     */
    public function register()
    {
        if(!$this->isRegistered) {
            set_error_handler(array($this, self::ERROR_HANDLER));
            set_exception_handler(array($this, self::EXCEPTION_HANDLER));

            $this->isRegistered = true;
        }

        return $this;
    }

    /**
     * Unregisters all handlers registered by this DamnIt\Run instance
     * @return DamnIt\Run
     */
    public function unregister()
    {
        if($this->isRegistered) {
            restore_exception_handler();
            restore_error_handler();

            $this->isRegistered = false;
        }

        return $this;
    }

    /**
     * Handles an exception, ultimately generating a DamnIt error
     * page.
     *
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
    }

    /**
     * Converts generic PHP errors to \ErrorException
     * instances, before passing them off to be handled.
     *
     * This method MUST be compatible with set_error_handler.
     *
     * @param int    $level
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        $this->handleException(
            new ErrorException(
                $message, $level, 0, $file, $line
            )
        );
    }
}
