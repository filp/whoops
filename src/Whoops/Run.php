<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\Handler;
use Whoops\Handler\CallbackHandler;
use Whoops\Exception\Inspector;
use Whoops\Exception\ErrorException;
use InvalidArgumentException;
use Exception;

class Run
{
    const EXCEPTION_HANDLER = 'handleException';
    const ERROR_HANDLER     = 'handleError';
    const SHUTDOWN_HANDLER  = 'handleShutdown';

    protected $isRegistered;
    protected $allowQuit = true;

    /**
     * @var DarnIt\Handler\HandlerInterface[]
     */
    protected $handlerStack = array();

    /**
     * Pushes a handler to the end of the stack.
     * @param  Whoops\HandlerInterface $handler
     * @return Whoops\Run
     */
    public function pushHandler($handler)
    {
        if(is_callable($handler)) {
            $handler = new CallbackHandler($handler);
        }

        if(!$handler instanceof HandlerInterface) {
            throw new InvalidArgumentException(
                  'Argument to ' . __METHOD__ . ' must be a callable, or instance of'
                . 'Whoops\\Handler\\HandlerInterface'
            );
        }

        $this->handlerStack[] = $handler;
        return $this;
    }

    /**
     * Removes the last handler in the stack and returns it.
     * Returns null if there's nothing else to pop.
     * @return null|Whoops\Handler\HandlerInterface
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
     * @return Whoops\Run
     */
    public function clearHandlers()
    {
        $this->handlerStack = array();
        return $this;
    }

    /**
     * @param  Exception $exception
     * @return Whoops\Exception\Inspector
     */
    protected function getInspector(Exception $exception)
    {
        return new Inspector($exception);
    }

    /**
     * Registers this instance as an error handler.
     * @return Whoops\Run
     */
    public function register()
    {
        if(!$this->isRegistered) {
            set_error_handler(array($this, self::ERROR_HANDLER));
            set_exception_handler(array($this, self::EXCEPTION_HANDLER));
            register_shutdown_function(array($this, self::SHUTDOWN_HANDLER));

            $this->isRegistered = true;
        }

        return $this;
    }

    /**
     * Unregisters all handlers registered by this Whoops\Run instance
     * @return Whoops\Run
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
     * Should Whoops allow Handlers to force the script to quit?
     * @param bool|num $exit
     * @return bool|null
     */
    public function allowQuit($exit = null)
    {
        if(func_num_args() == 0) {
            return $this->allowQuit;
        }

        return $this->allowQuit = (bool) $exit;
    }

    /**
     * Handles an exception, ultimately generating a Whoops error
     * page.
     *
     * @param Exception $exception
     */
    public function handleException(Exception $exception)
    {
        // Walk the registered handlers in the reverse order
        // they were registered, and pass off the exception
        $inspector = $this->getInspector($exception);

        for($i = count($this->handlerStack) - 1; $i >= 0; $i--) {
            $handler = $this->handlerStack[$i];

            $handler->setRun($this);
            $handler->setInspector($inspector);
            $handler->setException($exception);

            $handlerResponse = $handler->handle($exception);

            if($handlerResponse === Handler::LAST_HANDLER) {
                // The Handler has handled the exception in some way,
                // or signals that no further handlers should be queried,
                // but the script execution will continue
                break;
            } elseif($handlerResponse === Handler::QUIT) {
                // The Handler has handled the exception in some way,
                // and script execution should terminate, unless specifically
                // disallowed, in which case the behavior is the same as
                // Handler::LAST_HANDLER
                if($this->allowQuit()) {
                    exit;
                } else {
                    break;
                }
            }
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
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        if ($level & error_reporting()) {
            $this->handleException(
                new ErrorException(
                    $message, $level, 0, $file, $line
                )
            );
        }
    }

    /**
     * Special case to deal with Fatal errors and the like.
     */
    public function handleShutdown()
    {
        if($this->isRegistered && $error = error_get_last()) {
            $this->handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
}
