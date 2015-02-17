<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;

use Exception;
use InvalidArgumentException;
use Whoops\Exception\ErrorException;
use Whoops\Exception\Inspector;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\Handler;
use Whoops\Handler\HandlerInterface;

class Run
{
    const EXCEPTION_HANDLER = "handleException";
    const ERROR_HANDLER     = "handleError";
    const SHUTDOWN_HANDLER  = "handleShutdown";

    protected $isRegistered;
    protected $allowQuit       = true;
    protected $sendOutput      = true;

    /**
     * @var integer|false
     */
    protected $sendHttpCode    = 500;

    /**
     * @var HandlerInterface[]
     */
    protected $handlerStack = array();

    protected $silencedPatterns = array();

    /**
     * Pushes a handler to the end of the stack
     *
     * @throws InvalidArgumentException  If argument is not callable or instance of HandlerInterface
     * @param  Callable|HandlerInterface $handler
     * @return Run
     */
    public function pushHandler($handler)
    {
        if (is_callable($handler)) {
            $handler = new CallbackHandler($handler);
        }

        if (!$handler instanceof HandlerInterface) {
            throw new InvalidArgumentException(
                  "Argument to " . __METHOD__ . " must be a callable, or instance of"
                . "Whoops\\Handler\\HandlerInterface"
            );
        }

        $this->handlerStack[] = $handler;
        return $this;
    }

    /**
     * Removes the last handler in the stack and returns it.
     * Returns null if there"s nothing else to pop.
     * @return null|HandlerInterface
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
     * @return Run
     */
    public function clearHandlers()
    {
        $this->handlerStack = array();
        return $this;
    }

    /**
     * @param  Exception $exception
     * @return Inspector
     */
    protected function getInspector(Exception $exception)
    {
        return new Inspector($exception);
    }

    /**
     * Registers this instance as an error handler.
     * @return Run
     */
    public function register()
    {
        if (!$this->isRegistered) {
            // Workaround PHP bug 42098
            // https://bugs.php.net/bug.php?id=42098
            class_exists("\\Whoops\\Exception\\ErrorException");
            class_exists("\\Whoops\\Exception\\FrameCollection");
            class_exists("\\Whoops\\Exception\\Frame");
            class_exists("\\Whoops\\Exception\\Inspector");

            set_error_handler(array($this, self::ERROR_HANDLER));
            set_exception_handler(array($this, self::EXCEPTION_HANDLER));
            register_shutdown_function(array($this, self::SHUTDOWN_HANDLER));

            $this->isRegistered = true;
        }

        return $this;
    }

    /**
     * Unregisters all handlers registered by this Whoops\Run instance
     * @return Run
     */
    public function unregister()
    {
        if ($this->isRegistered) {
            restore_exception_handler();
            restore_error_handler();

            $this->isRegistered = false;
        }

        return $this;
    }

    /**
     * Should Whoops allow Handlers to force the script to quit?
     * @param  bool|int $exit
     * @return bool
     */
    public function allowQuit($exit = null)
    {
        if (func_num_args() == 0) {
            return $this->allowQuit;
        }

        return $this->allowQuit = (bool) $exit;
    }

    /**
     * Silence particular errors in particular files
     * @param  array|string $patterns List or a single regex pattern to match
     * @param  int          $levels   Defaults to E_STRICT | E_DEPRECATED
     * @return \Whoops\Run
     */
    public function silenceErrorsInPaths($patterns, $levels = 10240)
    {
        $this->silencedPatterns = array_merge(
            $this->silencedPatterns,
            array_map(
                function ($pattern) use ($levels) {
                    return array(
                        "pattern" => $pattern,
                        "levels" => $levels,
                    );
                },
                (array) $patterns
            )
        );
        return $this;
    }

    /*
     * Should Whoops send HTTP error code to the browser if possible?
     * Whoops will by default send HTTP code 500, but you may wish to
     * use 502, 503, or another 5xx family code.
     *
     * @param bool|int $code
     * @return int|false
     */
    public function sendHttpCode($code = null)
    {
        if (func_num_args() == 0) {
            return $this->sendHttpCode;
        }

        if (!$code) {
            return $this->sendHttpCode = false;
        }

        if ($code === true) {
            $code = 500;
        }

        if ($code < 400 || 600 <= $code) {
            throw new InvalidArgumentException(
                 "Invalid status code '$code', must be 4xx or 5xx"
            );
        }

        return $this->sendHttpCode = $code;
    }

    /**
     * Should Whoops push output directly to the client?
     * If this is false, output will be returned by handleException
     * @param  bool|int $send
     * @return bool
     */
    public function writeToOutput($send = null)
    {
        if (func_num_args() == 0) {
            return $this->sendOutput;
        }

        return $this->sendOutput = (bool) $send;
    }

    /**
     * Handles an exception, ultimately generating a Whoops error
     * page.
     *
     * @param  Exception $exception
     * @return string    Output generated by handlers
     */
    public function handleException(Exception $exception)
    {
        // Walk the registered handlers in the reverse order
        // they were registered, and pass off the exception
        $inspector = $this->getInspector($exception);

        // Capture output produced while handling the exception,
        // we might want to send it straight away to the client,
        // or return it silently.
        ob_start();

        // Just in case there are no handlers:
        $handlerResponse = null;

        foreach (array_reverse($this->handlerStack) as $handler) {
            $handler->setRun($this);
            $handler->setInspector($inspector);
            $handler->setException($exception);

            // The HandlerInterface does not require an Exception passed to handle()
            // and neither of our bundled handlers use it.
            // However, 3rd party handlers may have already relied on this parameter,
            // and removing it would be possibly breaking for users.
            $handlerResponse = $handler->handle($exception);

            if (in_array($handlerResponse, array(Handler::LAST_HANDLER, Handler::QUIT))) {
                // The Handler has handled the exception in some way, and
                // wishes to quit execution (Handler::QUIT), or skip any
                // other handlers (Handler::LAST_HANDLER). If $this->allowQuit
                // is false, Handler::QUIT behaves like Handler::LAST_HANDLER
                break;
            }
        }

        $willQuit = $handlerResponse == Handler::QUIT && $this->allowQuit();

        $output = ob_get_clean();

        // If we're allowed to, send output generated by handlers directly
        // to the output, otherwise, and if the script doesn't quit, return
        // it so that it may be used by the caller
        if ($this->writeToOutput()) {
            // @todo Might be able to clean this up a bit better
            // If we're going to quit execution, cleanup all other output
            // buffers before sending our own output:
            if ($willQuit) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }

            $this->writeToOutputNow($output);
        }

        if ($willQuit) {
            exit(1);
        }

        return $output;
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
     *
     * @return bool
     * @throws ErrorException
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        if ($level & error_reporting()) {
            foreach ($this->silencedPatterns as $entry) {
                $pathMatches = (bool) preg_match($entry["pattern"], $file);
                $levelMatches = $level & $entry["levels"];
                if ($pathMatches && $levelMatches) {
                    // Ignore the error, abort handling
                    return true;
                }
            }

            $exception = new ErrorException($message, $level, 0, $file, $line);
            if ($this->canThrowExceptions) {
                throw $exception;
            } else {
                $this->handleException($exception);
            }
            // Do not propagate errors which were already handled by Whoops.
            return true;
        }

        // Propagate error to the next handler, allows error_get_last() to
        // work on silenced errors.
        return false;
    }

    /**
     * Special case to deal with Fatal errors and the like.
     */
    public function handleShutdown()
    {
        // If we reached this step, we are in shutdown handler.
        // An exception thrown in a shutdown handler will not be propagated
        // to the exception handler. Pass that information along.
        $this->canThrowExceptions = false;

        $error = error_get_last();
        if ($error && $this->isLevelFatal($error['type'])) {
            // If there was a fatal error,
            // it was not handled in handleError yet.
            $this->handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    /**
     * In certain scenarios, like in shutdown handler, we can not throw exceptions
     * @var bool
     */
    private $canThrowExceptions = true;

    /**
     * Echo something to the browser
     * @param  string $output
     * @return $this
     */
    private function writeToOutputNow($output)
    {
        if ($this->sendHttpCode() && \Whoops\Util\Misc::canSendHeaders()) {
            $httpCode   = $this->sendHttpCode();

            if (function_exists('http_response_code')) {
                http_response_code($httpCode);
            } else {
                // http_response_code is added in 5.4.
                // For compatibility with 5.3 we use the third argument in header call
                // First argument must be a real header.
                // If it is empty, PHP will ignore the third argument.
                // If it is invalid, such as a single space, Apache will handle it well,
                // but the PHP development server will hang.
                // Setting a full status line would require us to hardcode
                // string values for all different status code, and detect the protocol.
                // which is an extra error-prone complexity.
                header('X-Ignore-This: 1', true, $httpCode);
            }
        }

        echo $output;

        return $this;
    }

    private static function isLevelFatal($level)
    {
        $errors = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_CORE_WARNING;
        $errors |= E_COMPILE_ERROR;
        $errors |= E_COMPILE_WARNING;
        return ($level & $errors) > 0;
    }
}
