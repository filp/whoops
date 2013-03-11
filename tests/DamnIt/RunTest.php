<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace DamnIt;
use DamnIt\TestCase;
use DamnIt\Run;
use Damnit\Handler\Handler;
use DamnIt\Handler\DummyHandler;
use \RuntimeException;

class RunTest extends TestCase
{
    /**
     * @return DamnIt\Run
     */
    protected function getRunInstance()
    {
        return new Run;
    }

    /**
     * @covers DamnIt\Run::clearHandlers
     */
    public function testClearHandlers()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlers = $run->getHandlers();

        $this->assertEmpty($handlers);
    }

    /**
     * @covers DamnIt\Run::pushHandler
     */
    public function testPushHandler()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlerOne = new DummyHandler;
        $handlerTwo = new DummyHandler;

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);

        $handlers = $run->getHandlers();

        $this->assertCount(2, $handlers);
        $this->assertContains($handlerOne, $handlers);
        $this->assertContains($handlerTwo, $handlers);
    }

    /**
     * @covers DamnIt\Run::popHandler
     * @covers DamnIt\Run::getHandlers
     */
    public function testPopHandler()
    {
        $run = $this->getRunInstance();
        
        $handlerOne   = new DummyHandler;
        $handlerTwo   = new DummyHandler;
        $handlerThree = new DummyHandler;

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);

        $this->assertSame($handlerThree, $run->popHandler());
        $this->assertSame($handlerTwo, $run->popHandler());
        $this->assertSame($handlerOne, $run->popHandler());

        // Should return null if there's nothing else in
        // the stack
        $this->assertNull($run->popHandler());

        // Should be empty since we popped everything off
        // the stack:
        $this->assertEmpty($run->getHandlers());
    }

    /**
     * @covers DamnIt\Run::register
     */
    public function testRegisterHandler()
    {
        $this->markTestSkipped("Need to test exception handler");

        $run = $this->getRunInstance();
        $run->register();

        $handler = new DummyHandler;
        $run->pushHandler($handler);

        throw new RuntimeException('Hi! :)');

        $this->assertCount(2, $handler->exceptions);
    }

    /**
     * @covers DamnIt\Run::unregister
     * @expectedException RuntimeException
     */
    public function testUnregisterHandler()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = new DummyHandler;
        $run->pushHandler($handler);

        $run->unregister();
        throw new RuntimeException("I'm not supposed to be caught!");
    }

    /**
     * @covers DamnIt\Run::pushHandler
     * @covers DamnIt\Run::getHandlers
     */
    public function testHandlerHoldsOrder()
    {
        $run = $this->getRunInstance();

        $handlerOne   = new DummyHandler;
        $handlerTwo   = new DummyHandler;
        $handlerThree = new DummyHandler;
        $handlerFour  = new DummyHandler;

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);
        $run->pushHandler($handlerFour);

        $handlers = $run->getHandlers();

        $this->assertSame($handlers[0], $handlerOne);
        $this->assertSame($handlers[1], $handlerTwo);
        $this->assertSame($handlers[2], $handlerThree);
        $this->assertSame($handlers[3], $handlerFour);
    }
}
