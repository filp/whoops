<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace DamnIt;

use DamnIt\Handler\HandlerInterface;
use Damnit\Handler\Handler;

class Run
{
    const EXCEPTION_HANDLER = 'handleException';
    const ERROR_HANDLER     = 'handleError';

    protected $isRegistered;

    /**
     * @var DarnIt\Handler\HandlerInterface[]
     */
    protected $handlerStack = array();

    /**
     * Pushes a handler to the end of the stack.
     * @param  DamnIt\HandlerInterface $handler
     * @return DamnIt\Run
     */
    public function pushHandler(HandlerInterface $handler)
    {
        $this->handlerStack[] = $handler;
        return $this;
    }

    /**
     * Removes the last handler in the stack and returns it.
     * Returns null if there's nothing else to pop.
     * @return null|DamnIt\Handler\HandlerInterface
     */
    public function popHandler()
    {
        return array_pop($this->handlerStack);
    }

    /**
     * Returns an array with all handlers, in the
     * order they were added to the stack.
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
        // Walk the registered handlers in the reverse order
        // they were registered, and pass off the exception
        for($i = count($this->handlerStack) - 1; $i >= 0; $i--) {
            $handler = $this->handlerStack[$i];

            $handlerResponse = $handler->handle($exception);
        }
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
