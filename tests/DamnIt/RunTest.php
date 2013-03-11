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
     * @covers DamnIt\Run::addHandler
     */
    public function testAddHandler()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlerOne = new DummyHandler;
        $handlerTwo = new DummyHandler;

        $run->addHandler($handlerOne);
        $run->addHandler($handlerTwo);

        $handlers = $run->getHandlers();

        $this->assertCount(2, $handlers);
        $this->assertContains($handlerOne, $handlers);
        $this->assertContains($handlerTwo, $handlers);
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
        $run->addHandler($handler);

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
        $run->addHandler($handler);

        $run->unregister();
        throw new RuntimeException("I'm not supposed to be caught!");
    }

    /**
     * @covers DamnIt\Run::addHandler
     * @covers DamnIt\Run::getHandlers
     */
    public function testHandlerSorting()
    {
        $run = $this->getRunInstance();

        $handlerOne   = new DummyHandler;
        $handlerTwo   = new DummyHandler;
        $handlerThree = new DummyHandler;
        $handlerFour  = new DummyHandler;

        // Priority with integer, or no priority:
        $run->addHandler($handlerOne, 10);
        $run->addHandler($handlerTwo, 0);
        $run->addHandler($handlerThree, 25);
        $run->addHandler($handlerFour, -10);

        $handlers = $run->getHandlers();
        
        $this->assertSame($handlers[0], $handlerFour);
        $this->assertSame($handlers[1], $handlerTwo);
        $this->assertSame($handlers[2], $handlerOne);
        $this->assertSame($handlers[3], $handlerThree);

        // Use priority constants to ensure handlers
        // go either to the end or start of the stack
        $run->clearHandlers();

        $run->addHandler($handlerOne, Handler::HIGH_PRIORITY);
        $run->addHandler($handlerTwo, Handler::HIGH_PRIORITY);
        $run->addHandler($handlerThree, -2);
        $run->addHandler($handlerFour, Handler::LOW_PRIORITY);

        $handlers = $run->getHandlers();

        $this->assertSame($handler[0], $handlerFour);
        $this->assertSame($handler[1], $handlerThree);
        $this->assertSame($handler[2], $handlerOne);
        $this->assertSame($handler[3], $handlerTwo);
    }
}
