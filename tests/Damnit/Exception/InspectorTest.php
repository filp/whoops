<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use Damnit\Exception\Inspector;
use Damnit\TestCase;
use \RuntimeException;
use \Exception;

class InspectorTest extends TestCase
{
    /**
     * @param  Exception $exception|null
     * @return Damnit\Exception\Inspector
     */
    protected function getInspectorInstance(Exception $exception = null)
    {
        return new Inspector($exception);
    }

    /**
     * @covers Damnit\Exception\Inspector::__construct
     * @covers Damnit\Exception\Inspector::getException
     */
    public function testExceptionIsStoredAndReturned()
    {
        $exception = new RuntimeException;
        $inspector = $this->getInspectorInstance($exception);

        $this->assertSame($exception, $inspector->getException());
    }

    /**
     * @covers Damnit\Exception\Inspector::getFrames
     */
    public function testGetFramesReturnsIterator()
    {
        $exception = new RuntimeException;
        $inspector = $this->getInspectorInstance($exception);

        $this->assertInstanceOf('Damnit\\Exception\\FrameIterator', $inspector->getFrames());
    }
}
