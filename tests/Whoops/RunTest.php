<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;

use ArrayObject;
use Exception;
use InvalidArgumentException;
use Mockery as m;
use RuntimeException;
use Whoops\Handler\Handler;
use Whoops\Exception\Frame;

class RunTest extends TestCase
{
    public function testImplementsRunInterface()
    {
        $this->assertNotFalse(class_implements('Whoops\\Run', 'Whoops\\RunInterface'));
    }

    public function testConstantsAreAccessibleFromTheClass()
    {
        $this->assertEquals(RunInterface::ERROR_HANDLER, Run::ERROR_HANDLER);
        $this->assertEquals(RunInterface::EXCEPTION_HANDLER, Run::EXCEPTION_HANDLER);
        $this->assertEquals(RunInterface::SHUTDOWN_HANDLER, Run::SHUTDOWN_HANDLER);
    }

    /**
     * @param  string    $message
     * @return Exception
     */
    protected function getException($message = "")
    {
        // HHVM does not support mocking exceptions
        // Since we do not use any additional features of Mockery for exceptions,
        // we can just use native Exceptions instead.
        return new \Exception($message);
    }

    /**
     * @return Handler
     */
    protected function getHandler()
    {
        return m::mock('Whoops\\Handler\\Handler')
            ->shouldReceive('setRun')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setInspector')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setException')
                ->andReturn(null)
            ->mock();
    }

    /**
     * @covers Whoops\Run::clearHandlers
     */
    public function testClearHandlers()
    {
        $run = $this->getRunInstance();
        $run->clearHandlers();

        $handlers = $run->getHandlers();

        $this->assertEmpty($handlers);
    }

    /**
     * @covers Whoops\Run::pushHandler
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
     * @covers Whoops\Run::pushHandler
     */
    public function testPushInvalidHandler()
    {
        $run = $this->getRunInstance();

        $this->expectExceptionOfType('InvalidArgumentException');

        $run->pushHandler('actually turnip');
    }

    /**
     * @covers Whoops\Run::pushHandler
     */
    public function testPushClosureBecomesHandler()
    {
        $run = $this->getRunInstance();
        $run->pushHandler(function () {});
        $this->assertInstanceOf('Whoops\\Handler\\CallbackHandler', $run->popHandler());
    }

    /**
     * @covers Whoops\Run::popHandler
     * @covers Whoops\Run::getHandlers
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
     * @covers Whoops\Run::removeFirstHandler
     * @covers Whoops\Run::removeLastHandler
     * @covers Whoops\Run::getHandlers
     */
    public function testRemoveHandler()
    {
        $run = $this->getRunInstance();

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);

        $run->removeLastHandler();
        $this->assertSame($handlerTwo, $run->getHandlers()[0]);
        $run->removeFirstHandler();
        $this->assertSame($handlerTwo, $run->getHandlers()[0]);
        $this->assertCount(1, $run->getHandlers());
    }

    /**
     * @covers Whoops\Run::register
     */
    public function testRegisterHandler()
    {
        // It is impossible to test the Run::register method using phpunit,
        // as given how every test is always inside a giant try/catch block,
        // any thrown exception will never hit a global exception handler.
        // On the other hand, there is not much need in testing
        // a call to a native PHP function.
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Run::unregister
     */
    public function testUnregisterHandler()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $run->unregister();

        $this->expectExceptionOfType('Exception');

        throw $this->getException("I'm not supposed to be caught!");
    }

    /**
     * @covers Whoops\Run::pushHandler
     * @covers Whoops\Run::getHandlers
     */
    public function testHandlerHoldsOrder()
    {
        $run = $this->getRunInstance();

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();
        $handlerFour  = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->prependHandler($handlerTwo);
        $run->appendHandler($handlerThree);
        $run->appendHandler($handlerFour);

        $handlers = $run->getHandlers();

        $this->assertSame($handlers[0], $handlerFour);
        $this->assertSame($handlers[1], $handlerThree);
        $this->assertSame($handlers[2], $handlerOne);
        $this->assertSame($handlers[3], $handlerTwo);
    }

    /**
     * @todo possibly split this up a bit and move
     *       some of this test to Handler unit tests?
     * @covers Whoops\Run::handleException
     */
    public function testHandlersGonnaHandle()
    {
        $run       = $this->getRunInstance();
        $exception = $this->getException();
        $order     = new ArrayObject();

        $handlerOne   = $this->getHandler();
        $handlerTwo   = $this->getHandler();
        $handlerThree = $this->getHandler();

        $handlerOne->shouldReceive('handle')
            ->andReturnUsing(function () use ($order) { $order[] = 1; });
        $handlerTwo->shouldReceive('handle')
            ->andReturnUsing(function () use ($order) { $order[] = 2; });
        $handlerThree->shouldReceive('handle')
            ->andReturnUsing(function () use ($order) { $order[] = 3; });

        $run->pushHandler($handlerOne);
        $run->pushHandler($handlerTwo);
        $run->pushHandler($handlerThree);

        // Get an exception to be handled, and verify that the handlers
        // are given the handler, and in the inverse order they were
        // registered.
        $run->handleException($exception);
        $this->assertEquals((array) $order, [3, 2, 1]);
    }

    /**
     * @covers Whoops\Run::handleException
     */
    public function testLastHandler()
    {
        $run = $this->getRunInstance();

        $handlerOne = $this->getHandler();
        $handlerTwo = $this->getHandler();
        $handlerThree = $this->getHandler();
        $handlerFour = $this->getHandler();

        $run->pushHandler($handlerOne);
        $run->prependHandler($handlerTwo);
        $run->appendHandler($handlerThree);
        $run->appendHandler($handlerFour);

        $test = $this;
        $handlerFour
            ->shouldReceive('handle')
            ->andReturnUsing(function () use ($test) {
                $test->fail('$handlerFour should not be called');
            });

        $handlerThree
            ->shouldReceive('handle')
            ->andReturn(Handler::LAST_HANDLER);

        $twoRan = false;

        $handlerOne
            ->shouldReceive('handle')
            ->andReturnUsing(function () use ($test, &$twoRan) {
                $test->assertTrue($twoRan);
            });

        $handlerTwo
            ->shouldReceive('handle')
            ->andReturnUsing(function () use (&$twoRan) {
                $twoRan = true;
            });

        $run->handleException($this->getException());

        // Reached the end without errors
        $this->assertTrue(true);
    }

    /**
     * Test error suppression using @ operator.
     */
    public function testErrorSuppression()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use ($test) {
                $test->fail('$handler should not be called, error not suppressed');
            });

        @trigger_error("Test error suppression");

        // Reached the end without errors
        $this->assertTrue(true);
    }

    public function testErrorCatching()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use ($test) {
                $test->fail('$handler should not be called error should be caught');
            });

        try {
            trigger_error('foo', E_USER_NOTICE);
            $this->fail('Should not continue after error thrown');
        } catch (\ErrorException $e) {
            // Do nothing
            $this->assertTrue(true);
            return;
        }
        $this->fail('Should not continue here, should have been caught.');
    }

    /**
     * Test to make sure that error_reporting is respected.
     */
    public function testErrorReporting()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use ($test) {
                $test->fail('$handler should not be called, error_reporting not respected');
            });

        $oldLevel = error_reporting(E_ALL ^ E_USER_NOTICE);
        trigger_error("Test error reporting", E_USER_NOTICE);
        error_reporting($oldLevel);

        // Reached the end without errors
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Run::silenceErrorsInPaths
     */
    public function testSilenceErrorsInPaths()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        $test = $this;
        $handler
            ->shouldReceive('handle')
            ->andReturnUsing(function () use ($test) {
                $test->fail('$handler should not be called, silenceErrorsInPaths not respected');
            });

        $run->silenceErrorsInPaths('@^'.preg_quote(__FILE__, '@').'$@', E_USER_NOTICE);
        trigger_error('Test', E_USER_NOTICE);
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Run::handleError
     * @requires PHP < 8
     */
    public function testGetSilencedError()
    {
        $run = $this->getRunInstance();
        $run->register();

        $handler = $this->getHandler();
        $run->pushHandler($handler);

        @strpos();

        $error = error_get_last();

        $this->assertTrue($error && strpos($error['message'], 'strpos()') !== false);
    }

    /**
     * @covers Whoops\Run::handleError
     * @see https://github.com/filp/whoops/issues/267
     */
    public function testErrorWrappedInException()
    {
        try {
            $run = $this->getRunInstance();
            $run->handleError(E_WARNING, 'my message', 'my file', 99);
            $this->fail("missing expected exception");
        } catch (\ErrorException $e) {
            $this->assertSame(E_WARNING, $e->getSeverity());
            $this->assertSame(E_WARNING, $e->getCode(), "For BC reasons getCode() should match getSeverity()");
            $this->assertSame('my message', $e->getMessage());
            $this->assertSame('my file', $e->getFile());
            $this->assertSame(99, $e->getLine());
        }
    }

    /**
     * @covers Whoops\Run::handleException
     * @covers Whoops\Run::writeToOutput
     */
    public function testOutputIsSent()
    {
        $run = $this->getRunInstance();
        $run->pushHandler(function () {
            echo "hello there";
        });

        ob_start();
        $run->handleException(new RuntimeException());
        $this->assertEquals("hello there", ob_get_clean());
    }

    /**
     * @covers Whoops\Run::handleException
     * @covers Whoops\Run::writeToOutput
     */
    public function testOutputIsNotSent()
    {
        $run = $this->getRunInstance();
        $run->writeToOutput(false);
        $run->pushHandler(function () {
            echo "hello there";
        });

        ob_start();
        $this->assertEquals("hello there", $run->handleException(new RuntimeException()));
        $this->assertEquals("", ob_get_clean());
    }

    /**
     * @covers Whoops\Run::sendHttpCode
     */
    public function testSendHttpCode()
    {
        $run = $this->getRunInstance();
        $run->sendHttpCode(true);
        $this->assertEquals(500, $run->sendHttpCode());
    }

    /**
     * @covers Whoops\Run::sendHttpCode
     */
    public function testSendHttpCodeNullCode()
    {
        $run = $this->getRunInstance();
        $this->assertEquals(false, $run->sendHttpCode(null));
    }

    /**
     * @covers Whoops\Run::sendHttpCode
     */
    public function testSendHttpCodeWrongCode()
    {
        $this->expectExceptionOfType('InvalidArgumentException');

        $this->getRunInstance()->sendHttpCode(1337);
    }

    /**
     * @covers Whoops\Run::sendHttpCode
     */
    public function testSendExitCode()
    {
        $run = $this->getRunInstance();
        $run->sendExitCode(42);
        $this->assertEquals(42, $run->sendExitCode());
    }

    /**
     * @covers Whoops\Run::sendExitCode
     */
    public function testSendExitCodeDefaultCode()
    {
        $run = $this->getRunInstance();
        $this->assertEquals(1, $run->sendExitCode());
    }

    /**
     * @covers Whoops\Run::sendExitCode
     */
    public function testSendExitCodeWrongCode()
    {
        $this->expectExceptionOfType('InvalidArgumentException');

        $this->getRunInstance()->sendExitCode(255);
    }

    /**
     * @covers Whoops\Run::addFrameFilter
     * @covers Whoops\Run::getFrameFilters
     */
    public function testAddFrameFilter()
    {
        $run = $this->getRunInstance();

        $filterCallbackOne = function(Frame $frame) {};
        $filterCallbackTwo = function(Frame $frame) {};

        $run
            ->addFrameFilter($filterCallbackOne)
            ->addFrameFilter($filterCallbackTwo);
        
        $frameFilters = $run->getFrameFilters();

        $this->assertCount(2, $frameFilters);
        $this->assertContains($filterCallbackOne, $frameFilters);
        $this->assertContains($filterCallbackTwo, $frameFilters);
        $this->assertInstanceOf("Whoops\\RunInterface", $run);
    }

    /**
     * @covers Whoops\Run::clearFrameFilters
     * @covers Whoops\Run::getFrameFilters
     */
    public function testClearFrameFilters()
    {
        $run = $this->getRunInstance();
        $run->addFrameFilter(function(Frame $frame) {});
        
        $run = $run->clearFrameFilters();

        $this->assertEmpty($run->getFrameFilters());
        $this->assertInstanceOf("Whoops\\RunInterface", $run);
    }
}
