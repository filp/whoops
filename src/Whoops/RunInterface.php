<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;

use InvalidArgumentException;
use Whoops\Exception\ErrorException;
use Whoops\Handler\HandlerInterface;

interface RunInterface
{
    public const EXCEPTION_HANDLER = "handleException";
    public const ERROR_HANDLER     = "handleError";
    public const SHUTDOWN_HANDLER  = "handleShutdown";

    /**
     * Pushes a handler to the end of the stack
     *
     * @throws InvalidArgumentException  If argument is not callable or instance of HandlerInterface
     * @param  Callable|HandlerInterface $handler
     * @return Run
     */
    public function pushHandler($handler);

    /**
     * Removes the last handler in the stack and returns it.
     * Returns null if there"s nothing else to pop.
     *
     * @return null|HandlerInterface
     */
    public function popHandler();

    /**
     * Returns an array with all handlers, in the
     * order they were added to the stack.
     *
     * @return array
     */
    public function getHandlers();

    /**
     * Clears all handlers in the handlerStack, including
     * the default PrettyPage handler.
     *
     * @return Run
     */
    public function clearHandlers();

    /**
     * Registers this instance as an error handler.
     *
     * @return Run
     */
    public function register();

    /**
     * Unregisters all handlers registered by this Whoops\Run instance
     *
     * @return Run
     */
    public function unregister();

    /**
     * Should Whoops allow Handlers to force the script to quit?
     *
     * @param  bool|int $exit
     * @return bool
     */
    public function allowQuit($exit = null);

    /**
     * Silence particular errors in particular files
     *
     * @param  array|string $patterns List or a single regex pattern to match
     * @param  int          $levels   Defaults to E_STRICT | E_DEPRECATED
     * @return \Whoops\Run
     */
    public function silenceErrorsInPaths($patterns, $levels = 10240);

    /**
     * Should Whoops send HTTP error code to the browser if possible?
     * Whoops will by default send HTTP code 500, but you may wish to
     * use 502, 503, or another 5xx family code.
     *
     * @param bool|int $code
     * @return int|false
     */
    public function sendHttpCode($code = null);

    /**
     * Should Whoops exit with a specific code on the CLI if possible?
     * Whoops will exit with 1 by default, but you can specify something else.
     *
     * @param int $code
     * @return int
     */
    public function sendExitCode($code = null);

    /**
     * Should Whoops push output directly to the client?
     * If this is false, output will be returned by handleException
     *
     * @param  bool|int $send
     * @return bool
     */
    public function writeToOutput($send = null);

    /**
     * Handles an exception, ultimately generating a Whoops error
     * page.
     *
     * @param  \Throwable $exception
     * @return string     Output generated by handlers
     */
    public function handleException($exception);

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
    public function handleError($level, $message, $file = null, $line = null);

    /**
     * Special case to deal with Fatal errors and the like.
     */
    public function handleShutdown();
}
