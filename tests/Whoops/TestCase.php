<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit;
use Damnit\Run;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Damnit\Run
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
