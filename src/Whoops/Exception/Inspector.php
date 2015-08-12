<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

use Exception;
use Whoops\Util\Misc;

class Inspector
{
    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var \Whoops\Exception\FrameCollection
     */
    private $frames;

    /**
     * @var \Whoops\Exception\Inspector
     */
    private $previousExceptionInspector;

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
     * @return string
     */
    public function getExceptionName()
    {
        return get_class($this->exception);
    }

    /**
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->exception->getMessage();
    }

    /**
     * Does the wrapped Exception has a previous Exception?
     * @return bool
     */
    public function hasPreviousException()
    {
        return $this->previousExceptionInspector || $this->exception->getPrevious();
    }

    /**
     * Returns an Inspector for a previous Exception, if any.
     * @todo   Clean this up a bit, cache stuff a bit better.
     * @return Inspector
     */
    public function getPreviousExceptionInspector()
    {
        if ($this->previousExceptionInspector === null) {
            $previousException = $this->exception->getPrevious();

            if ($previousException) {
                $this->previousExceptionInspector = new Inspector($previousException);
            }
        }

        return $this->previousExceptionInspector;
    }

    /**
     * Returns an iterator for the inspected exception's
     * frames.
     * @return \Whoops\Exception\FrameCollection
     */
    public function getFrames()
    {
        if ($this->frames === null) {
            $frames = $this->getTrace($this->exception);
            
            // Find latest non-error handling frame index ($i) used to remove error handling frames
            // Also fill empty line/file info for call_user_func_array usages (PHP Bug #44428)
            $i = 0;
            foreach ($frames as $k => $frame) {
                if (empty($frame['file'])) {
                    $file = null;
                    $line = null;
                    if (!empty($frames[$k + 1]) && !empty($frames[$k + 1]['file'])) {
                        $file = $frames[$k + 1]['file'];
                        $line = $frames[$k + 1]['line'];
                    }
                    $frame['file'] = $frames[$k]['file'] = $file;
                    $frame['line'] = $frames[$k]['line'] = $line;
                }
                if ($frame['file'] == $this->exception->getFile() && $frame['line'] == $this->exception->getLine()) {
                    $i = $k;
                }
            }

            // Remove error handling frames
            if ($i > 0) {
                array_splice($frames, 0, $i);               
            } 
            
            $firstFrame = $this->getFrameFromException($this->exception);
            array_unshift($frames, $firstFrame);
            
            $this->frames = new FrameCollection($frames);

            if ($previousInspector = $this->getPreviousExceptionInspector()) {
                // Keep outer frame on top of the inner one
                $outerFrames = $this->frames;
                $newFrames = clone $previousInspector->getFrames();
                // I assume it will always be set, but let's be safe
                if (isset($newFrames[0])) {
                    $newFrames[0]->addComment(
                        $previousInspector->getExceptionMessage(),
                        'Exception message:'
                    );
                }
                $newFrames->prependFrames($outerFrames->topDiff($newFrames));
                $this->frames = $newFrames;
            }
        }

        return $this->frames;
    }

    /**
     * Gets the backgrace from an exception.
     *
     * If xdebug is installed
     *
     * @param Exception $e
     * @return array
     */
    protected function getTrace(\Exception $e)
    {
        $traces = $e->getTrace();

        // Get trace from xdebug if enabled, failure exceptions only trace to the shutdown handler by default
        if (!$e instanceof \ErrorException) {
            return $traces;
        }

        if (!Misc::isLevelFatal($e->getSeverity())) {
            return $traces;
        }

        if (!extension_loaded('xdebug') || !xdebug_is_enabled()) {
            return array();
        }

        // Use xdebug to get the full stack trace and remove the shutdown handler stack trace
        $stack = array_reverse(xdebug_get_function_stack());
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $traces = array_diff_key($stack, $trace);

        return $traces;
    }

    /**
     * Given an exception, generates an array in the format
     * generated by Exception::getTrace()
     * @param  Exception $exception
     * @return array
     */
    protected function getFrameFromException(Exception $exception)
    {
        return array(
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'class' => get_class($exception),
            'args'  => array(
                $exception->getMessage(),
            ),
        );
    }

    /**
     * Given an error, generates an array in the format
     * generated by ErrorException
     * @param  ErrorException $exception
     * @return array
     */
    protected function getFrameFromError(ErrorException $exception)
    {
        return array(
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'class' => null,
            'args'  => array(),
        );
    }
}
