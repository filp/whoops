<?php
/**
* Whoops - php errors for cool kids
* @author Filipe Dobreira <http://github.com/filp>
* Plaintext handler for command line and logs.
* @author Pierre-Yves Landuré <https://howto.biapy.com/>
*/

namespace Whoops\Handler;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Whoops\Exception\Frame;

/**
* Handler outputing plaintext error messages. Can be used
* directly, or will be instantiated automagically by Whoops\Run
* if passed to Run::pushHandler
*/
class PlainTextHandler extends Handler
{
    const VAR_DUMP_PREFIX = '   | ';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    private $addTraceToOutput = true;

    /**
     * @var bool|integer
     */
    private $addTraceFunctionArgsToOutput = false;

    /**
     * @var integer
     */
    private $traceFunctionArgsOutputLimit = 1024;

    /**
     * @var bool
     */
    private $loggerOnly = false;

    /**
     * Constructor.
     * @throws InvalidArgumentException     If argument is not null or a LoggerInterface
     * @param  \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct($logger = null)
    {
        $this->setLogger($logger);
    }

    /**
     * Set the output logger interface.
     * @throws InvalidArgumentException     If argument is not null or a LoggerInterface
     * @param  \Psr\Log\LoggerInterface|null $logger
     */
    public function setLogger($logger = null)
    {
        if (! (is_null($logger)
            || $logger instanceof LoggerInterface)) {
            throw new InvalidArgumentException(
                'Argument to ' . __METHOD__ .
                " must be a valid Logger Interface (aka. Monolog), " .
                get_class($logger) . ' given.'
            );
        }

        $this->logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Add error trace to output.
     * @param  bool|null  $addTraceToOutput
     * @return bool|$this
     */
    public function addTraceToOutput($addTraceToOutput = null)
    {
        if (func_num_args() == 0) {
            return $this->addTraceToOutput;
        }

        $this->addTraceToOutput = (bool) $addTraceToOutput;
        return $this;
    }

    /**
     * Add error trace function arguments to output.
     * Set to True for all frame args, or integer for the n first frame args.
     * @param  bool|integer|null $addTraceFunctionArgsToOutput
     * @return null|bool|integer
     */
    public function addTraceFunctionArgsToOutput($addTraceFunctionArgsToOutput = null)
    {
        if (func_num_args() == 0) {
            return $this->addTraceFunctionArgsToOutput;
        }

        if (! is_integer($addTraceFunctionArgsToOutput)) {
            $this->addTraceFunctionArgsToOutput = (bool) $addTraceFunctionArgsToOutput;
        } else {
            $this->addTraceFunctionArgsToOutput = $addTraceFunctionArgsToOutput;
        }
    }

    /**
     * Set the size limit in bytes of frame arguments var_dump output.
     * If the limit is reached, the var_dump output is discarded.
     * Prevent memory limit errors.
     * @var integer
     */
    public function setTraceFunctionArgsOutputLimit($traceFunctionArgsOutputLimit)
    {
        $this->traceFunctionArgsOutputLimit = (integer) $traceFunctionArgsOutputLimit;
    }

    /**
     * Create plain text response and return it as a string
     * @return string
     */
    public function generateResponse()
    {
        $exception = $this->getException();
        return sprintf("%s: %s in file %s on line %d%s\n",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $this->getTraceOutput()
        );
    }

    /**
     * Get the size limit in bytes of frame arguments var_dump output.
     * If the limit is reached, the var_dump output is discarded.
     * Prevent memory limit errors.
     * @return integer
     */
    public function getTraceFunctionArgsOutputLimit()
    {
        return $this->traceFunctionArgsOutputLimit;
    }

    /**
     * Only output to logger.
     * @param  bool|null $loggerOnly
     * @return null|bool
     */
    public function loggerOnly($loggerOnly = null)
    {
        if (func_num_args() == 0) {
            return $this->loggerOnly;
        }

        $this->loggerOnly = (bool) $loggerOnly;
    }

    /**
     * Test if handler can output to stdout.
     * @return bool
     */
    private function canOutput()
    {
        return !$this->loggerOnly();
    }

    /**
     * Get the frame args var_dump.
     * @param  \Whoops\Exception\Frame $frame [description]
     * @param  integer                 $line  [description]
     * @return string
     */
    private function getFrameArgsOutput(Frame $frame, $line)
    {
        if ($this->addTraceFunctionArgsToOutput() === false
            || $this->addTraceFunctionArgsToOutput() < $line) {
            return '';
        }

        // Dump the arguments:
        ob_start();
        var_dump($frame->getArgs());
        if (ob_get_length() > $this->getTraceFunctionArgsOutputLimit()) {
            // The argument var_dump is to big.
            // Discarded to limit memory usage.
            ob_clean();
            return sprintf(
                "\n%sArguments dump length greater than %d Bytes. Discarded.",
                self::VAR_DUMP_PREFIX,
                $this->getTraceFunctionArgsOutputLimit()
            );
        }

        return sprintf("\n%s",
            preg_replace('/^/m', self::VAR_DUMP_PREFIX, ob_get_clean())
        );
    }

    /**
     * Get the exception trace as plain text.
     * @return string
     */
    private function getTraceOutput()
    {
        if (! $this->addTraceToOutput()) {
            return '';
        }
        $inspector = $this->getInspector();
        $frames = $inspector->getFrames();

        $response = "\nStack trace:";

        $line = 1;
        foreach ($frames as $frame) {
            /** @var Frame $frame */
            $class = $frame->getClass();

            $template = "\n%3d. %s->%s() %s:%d%s";
            if (! $class) {
                // Remove method arrow (->) from output.
                $template = "\n%3d. %s%s() %s:%d%s";
            }

            $response .= sprintf(
                $template,
                $line,
                $class,
                $frame->getFunction(),
                $frame->getFile(),
                $frame->getLine(),
                $this->getFrameArgsOutput($frame, $line)
            );

            $line++;
        }

        return $response;
    }

    /**
     * @return int
     */
    public function handle()
    {
        $response = $this->generateResponse();

        if ($this->getLogger()) {
            $this->getLogger()->error($response);
        }

        if (! $this->canOutput()) {
            return Handler::DONE;
        }

        if (\Whoops\Util\Misc::canSendHeaders()) {
            header('Content-Type: text/plain');
        }

        echo $response;

        return Handler::QUIT;
    }
}
