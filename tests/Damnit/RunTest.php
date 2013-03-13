<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit;
use Damnit\TestCase;
use Damnit\Run;
use Damnit\Handler\Handler;
use RuntimeException;
use ArrayObject;
use Mockery as m;

class RunTest extends TestCase
{
    /**
     * @return Damnit\Run
     */
    protected function getRunInstance()
    {
        $run = new Run;
        $run->exitWhenDone(false);

        return $run;
    }

    /**
     * @return Damnit\Handler\Handler
     */
    protected function getHandler()
    {
        return m::mock('Damnit\\Handler\\Handler')
            ->shouldReceive('setRun')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setInspector')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setException')
                ->andReturn(null)
            ->mock()
        ;
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

        $handlerOne = $this->getHandler();
        $handlerTwo = $this->getHandler();

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

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();

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

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        throw $this->getException();

        $this->assertCount(2, $handler->exceptions);
    }

    /**
     * @covers Damnit\Run::unregister
     * @expectedException Exception
     */
    public function testUnregisterHandler()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $run->unregister();
        throw $this->getException("I'm not supposed to be caught!");
    }

    /**
     * @covers Damnit\Run::pushHandler
     * @covers Damnit\Run::getHandlers
     */
    public function testHandlerHoldsOrder()
    {
        $run = $this->getRunInstance();

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();
        $handlerFour  = $this->getHandler();

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
        $run       = $this->getRunInstance();
        $exception = $this->getException();
        $order     = new ArrayObject;

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();

        $handlerOne->shouldReceive('handle')
            ->andReturnUsing(function() use($order) { $order[] = 1; });
        $handlerTwo->shouldReceive('handle')
            ->andReturnUsing(function() use($order) { $order[] = 2; });
        $handlerThree->shouldReceive('handle')
            ->andReturnUsing(function() use($order) { $order[] = 3; });

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);

        // Get an exception to be handled, and verify that the handlers
        // are given the handler, and in the inverse order they were
        // registered.
        $run->handleException($exception);
        $this->assertEquals((array) $order, array(3, 2, 1));
    }

    /**
     * @covers Damnit\Run::handleException
     */
    public function testLastHandler()
    {
        $run = $this->getRunInstance();

        $handlerOne = $this->getHandler();
        $handlerTwo = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);

        $test = $this;
        $handlerOne
            ->shouldReceive('handle')
            ->andReturnUsing(function () use($test) {
                $test->fail('$handlerOne should not be called');
            })
        ;

        $handlerTwo
            ->shouldReceive('handle')
            ->andReturn(Handler::LAST_HANDLER)
        ;

        $run->handleException($this->getException());
    }
}
