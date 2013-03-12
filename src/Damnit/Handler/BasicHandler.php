<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;
use Damnit\Handler\HandlerInterface;

class BasicHandler extends Handler
{
    /**
     * @param \Exception
     * @return int|null
     */
    public function handle(\Exception $exception)
    {
        $name    = get_class($exception);
        $message = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();

        print "There was an error!\n";
        print "-> $name\n";
        print "-> $message\n";
        print "In file: $file\n";
        print "In line: $line\n";
        print "---\n";
        print "Trace:\n";
        print $exception->getTraceAsString();
    }
}
