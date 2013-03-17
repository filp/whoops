<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;
use Whoops\Run;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Whoops\Run
     */
    protected function getRunInstance()
    {
        $run = new Run;
        $run->allowQuit(false);

        return $run;
    }

    /**
     * @param string $message
     * @return Exception
     */
    protected function getException($message = null)
    {
        return m::mock('Exception', array($message));
    }
}
