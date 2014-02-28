<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Inspector;
use Whoops\TestCase;
use Exception;

class InspectorTest extends TestCase
{
    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     * @return Exception
     */
    protected function getException($message = null, $code = 0, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }

    /**
     * @param  Exception $exception|null
     * @return Whoops\Exception\Inspector
     */
    protected function getInspectorInstance(Exception $exception = null)
    {
        return new Inspector($exception);
    }

    /**
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testCorrectNestedFrames($value='')
    {
        // Create manually to have a different line number from the outer
        $inner = new Exception('inner');
        $outer = $this->getException('outer', 0, $inner);
        $inspector = $this->getInspectorInstance($outer);
        $frames = $inspector->getFrames();
        $this->assertSame($outer->getLine(), $frames[0]->getLine());
    }

    /**
     * @covers Whoops\Exception\Inspector::getExceptionName
     */
    public function testReturnsCorrectExceptionName()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $this->assertEquals(get_class($exception), $inspector->getExceptionName());
    }

    /**
     * @covers Whoops\Exception\Inspector::__construct
     * @covers Whoops\Exception\Inspector::getException
     */
    public function testExceptionIsStoredAndReturned()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $this->assertSame($exception, $inspector->getException());
    }

    /**
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testGetFramesReturnsCollection()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $this->assertInstanceOf('Whoops\\Exception\\FrameCollection', $inspector->getFrames());
    }

    /**
     * @covers Whoops\Exception\Inspector::hasPreviousException
     * @covers Whoops\Exception\Inspector::getPreviousExceptionInspector
     */
    public function testPreviousException()
    {
        $previousException = $this->getException("I'm here first!");
        $exception         = $this->getException("Oh boy", null, $previousException);
        $inspector         = $this->getInspectorInstance($exception);

        $this->assertTrue($inspector->hasPreviousException());
        $this->assertEquals($previousException, $inspector->getPreviousExceptionInspector()->getException());
    }

    /**
     * @covers Whoops\Exception\Inspector::hasPreviousException
     */
    public function testNegativeHasPreviousException()
    {
        $exception         = $this->getException("Oh boy");
        $inspector         = $this->getInspectorInstance($exception);

        $this->assertFalse($inspector->hasPreviousException());
    }
}
