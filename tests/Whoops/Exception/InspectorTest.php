<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

use Exception;
use Whoops\TestCase;

class InspectorTest extends TestCase
{
    /**
     * @param  string     $message
     * @param  int        $code
     * @param  Exception $previous
     * @return Exception
     */
    protected function getException($message = "", $code = 0, $previous = null)
    {
        return new Exception($message, $code, $previous);
    }

    /**
     * @param  Exception                  $exception|null
     * @return \Whoops\Exception\Inspector
     */
    protected function getInspectorInstance($exception = null)
    {
        return new Inspector($exception);
    }

    /**
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testCorrectNestedFrames($value = '')
    {
        // Create manually to have a different line number from the outer
        $inner = new Exception('inner');
        $outer = $this->getException('outer', 0, $inner);
        $inspector = $this->getInspectorInstance($outer);
        $frames = $inspector->getFrames();
        $this->assertSame($outer->getLine(), $frames[0]->getLine());
    }

    /**
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testDoesNotFailOnPHP7ErrorObject()
    {
        if (!class_exists('Error')) {
            $this->markTestSkipped(
              'PHP 5.x, the Error class is not available.'
            );
        }

        $inner = new \Error('inner');
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
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testGetFramesWithFiltersReturnsCollection()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $frames = $inspector->getFrames([
            function(Frame $frame) {
                return true;
            },
        ]);

        $this->assertInstanceOf('Whoops\\Exception\\FrameCollection', $frames);
        $this->assertNotEmpty($frames);
    }

    /**
     * @covers Whoops\Exception\Inspector::getFrames
     */
    public function testGetFramesWithFiltersReturnsEmptyCollection()
    {
        $exception = $this->getException();
        $inspector = $this->getInspectorInstance($exception);

        $frames = $inspector->getFrames([
            function(Frame $frame) {
                return false;
            },
        ]);

        $this->assertInstanceOf('Whoops\\Exception\\FrameCollection', $frames);
        $this->assertEmpty($frames);
    }

    /**
     * @covers Whoops\Exception\Inspector::hasPreviousException
     * @covers Whoops\Exception\Inspector::getPreviousExceptionInspector
     */
    public function testPreviousException()
    {
        $previousException = $this->getException("I'm here first!");
        $exception         = $this->getException("Oh boy", 0, $previousException);
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

    /**
     * @covers Whoops\Exception\Inspector::getPreviousExceptions
     */
    public function testGetPreviousExceptionsReturnsListOfExceptions()
    {
        $exception1        = $this->getException('My first exception');
        $exception2        = $this->getException('My second exception', 0, $exception1);
        $exception3        = $this->getException('And the third one', 0, $exception2);

        $inspector         = $this->getInspectorInstance($exception3);

        $previousExceptions = $inspector->getPreviousExceptions();
        $this->assertCount(2, $previousExceptions);
        $this->assertEquals($exception2, $previousExceptions[0]);
        $this->assertEquals($exception1, $previousExceptions[1]);
    }

    /**
     * @covers Whoops\Exception\Inspector::getPreviousExceptions
     */
    public function testGetPreviousExceptionsReturnsEmptyListIfThereAreNoPreviousExceptions()
    {
        $exception         = $this->getException('My exception');
        $inspector         = $this->getInspectorInstance($exception);

        $previousExceptions = $inspector->getPreviousExceptions();
        $this->assertCount(0, $previousExceptions);
    }

    /**
     * @covers Whoops\Exception\Inspector::getPreviousExceptionMessages
     */
    public function testGetPreviousExceptionMessages()
    {
        $exception1        = $this->getException('My first exception');
        $exception2        = $this->getException('My second exception', 0, $exception1);
        $exception3        = $this->getException('And the third one', 0, $exception2);

        $inspector         = $this->getInspectorInstance($exception3);

        $previousExceptions = $inspector->getPreviousExceptionMessages();

        $this->assertEquals($exception2->getMessage(), $previousExceptions[0]);
        $this->assertEquals($exception1->getMessage(), $previousExceptions[1]);
    }


    /**
     * @covers Whoops\Exception\Inspector::getPreviousExceptionCodes
     */
    public function testGetPreviousExceptionCodes()
    {
        $exception1        = $this->getException('My first exception', 99);
        $exception2        = $this->getException('My second exception', 20, $exception1);
        $exception3        = $this->getException('And the third one', 10, $exception2);

        $inspector         = $this->getInspectorInstance($exception3);

        $previousExceptions = $inspector->getPreviousExceptionCodes();

        $this->assertEquals($exception2->getCode(), $previousExceptions[0]);
        $this->assertEquals($exception1->getCode(), $previousExceptions[1]);
    }
}
