<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit;
use Damnit\TestCase;
use Damnit\Run;
use Damnit\Handler\Handler;
use Damnit\Handler\DummyHandler;
use \RuntimeException;
use \ArrayObject;

class RunTest extends TestCase
{
    /**
     * @return Damnit\Run
     */
    protected function getRunInstance()
    {
        return new Run;
    }

    /**
     * @covers Damnit\Run::clearHandlers
     */
    public function testClearHandlers()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlers = $run->getHandlers();

        $this->assertEmpty($handlers);
    }

    /**
     * @covers Damnit\Run::pushHandler
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
     * @covers Damnit\Run::popHandler
     * @covers Damnit\Run::getHandlers
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
     * @covers Damnit\Run::register
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
     * @covers Damnit\Run::unregister
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
     * @covers Damnit\Run::pushHandler
     * @covers Damnit\Run::getHandlers
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

    /**
     * @todo possibly split this up a bit and move
     *       some of this test to Handler unit tests?
     * @covers Damnit\Run::handleException
     */
    public function testHandlersGonnaHandle()
    {
        $run          = $this->getRunInstance();
        $exception    = new RuntimeException;
        $handlerOrder = new ArrayObject;

        $handlerOne   = new DummyHandler;
        $handlerTwo   = new DummyHandler;
        $handlerThree = new DummyHandler;
        $handlerFour  = new DummyHandler;

        $handlerOne   ->onHandle(function() use($handlerOrder) {$handlerOrder[] = 1;});
        $handlerTwo   ->onHandle(function() use($handlerOrder) {$handlerOrder[] = 2;});
        $handlerThree ->onHandle(function() use($handlerOrder) {$handlerOrder[] = 3;});
        $handlerFour  ->onHandle(function() use($handlerOrder) {$handlerOrder[] = 4;});

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);
        $run->pushHandler($handlerFour);

        // Get an exception to be handled, and verify that the handlers
        // are given the handler, and in the inverse order they were
        // registered.
        $run->handleException($exception);

        $this->assertContains($exception, $handlerOne->exceptions);
        $this->assertContains($exception, $handlerTwo->exceptions);
        $this->assertContains($exception, $handlerThree->exceptions);
        $this->assertContains($exception, $handlerFour->exceptions);

        $this->assertEquals((array) $handlerOrder, array(4, 3, 2, 1));
    }

    /**
     * @covers Damnit\Run::handleException
     */
    public function testLastHandler()
    {
        $run = $this->getRunInstance();

        $handlerOne = new DummyHandler;
        $handlerTwo = new DummyHandler;

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);

        $test = $this;
        $handlerOne->onHandle(function() use($test) {
            $test->fail('$handlerOne should not be called to handle an exception');
        });

        $handlerTwo->onHandle(function() {
            return Handler::LAST_HANDLER;
        });

        $run->handleException(new RuntimeException);
    }
}
