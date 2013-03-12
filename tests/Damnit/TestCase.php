<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit;
use \Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $message
     * @return Exception
     */
    protected function getException($message = null)
    {
        return m::mock('Exception', array($message));
    }
}
