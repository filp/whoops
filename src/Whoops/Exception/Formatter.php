<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

class Formatter
{
    /**
     * Returns all basic information about the exception in a simple array
     * for further convertion to other languages
     * @param Inspector $inspector
     * @param bool $shouldAddTrace
     * @return array
     */
    public static function formatExceptionAsDataArray(Inspector $inspector, $shouldAddTrace)
    {
        $exception = $inspector->getException();
        $response = array(
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine()
        );

        if($shouldAddTrace) {
            $frames    = $inspector->getFrames();
            $frameData = array();

            foreach($frames as $frame) {
                /** @var Frame $frame */
                $frameData[] = array(
                    'file'     => $frame->getFile(),
                    'line'     => $frame->getLine(),
                    'function' => $frame->getFunction(),
                    'class'    => $frame->getClass(),
                    'args'     => $frame->getArgs()
                );
            }

            $response['trace'] = $frameData;
        }

        return $response;
    }
}
