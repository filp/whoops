<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace DamnIt;
use DamnIt\TestCase;
use DamnIt\Run;
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
}
