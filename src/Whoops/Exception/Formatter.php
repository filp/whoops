<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Util\TemplateHelper;

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
    
    public static function formatExceptionPlain(Inspector $inspector) {
        $tpl = new TemplateHelper();
        $name = explode("\\", $inspector->getExceptionName());
        $message = $inspector->getException()->getMessage();
        $frames = $inspector->getFrames();
    
        $plain = '';
        foreach($name as $i => $nameSection) {
            if($i == count($name) - 1) {
                $plain .= $tpl->escape($nameSection);
            } else {
                $plain .= $tpl->escape($nameSection) . '\\';
            }
        }
    
        $plain .= ' thrown with message "';
        $plain .= $tpl->escape($message);
        $plain .= '"'."\n\n";
    
        $plain .= "Stacktrace:\n";
        foreach($frames as $i => $frame) {
            $plain .= "#". (count($frames) - $i - 1). " ";
            $plain .= $tpl->escape($frame->getClass() ?: '');
            $plain .= ($frame->getClass() && $frame->getFunction()) ? ":" : "";
            $plain .= $tpl->escape($frame->getFunction() ?: '');
            $plain .= ' in ';
            $plain .= ($frame->getFile() ?: '<#unknown>');
            $plain .= ':';
            $plain .= (int) $frame->getLine(). "\n";
        }
        
        return $plain;
    }    
}
