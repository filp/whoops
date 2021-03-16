<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use RuntimeException;
use StdClass;
use Whoops\TestCase;
use Whoops\Exception\Frame;

class PlainTextHandlerTest extends TestCase
{
    const DEFAULT_EXCEPTION_LINE = 34;
    const DEFAULT_LINE_OF_CALLER = 65;

    /**
     * @throws \InvalidArgumentException        If argument is not null or a LoggerInterface
     * @param  \Psr\Log\LoggerInterface|null    $logger
     * @return \Whoops\Handler\PlainTextHandler
     */
    private function getHandler($logger = null)
    {
        return new PlainTextHandler($logger);
    }

    /**
     * @return RuntimeException
     */
    public function getException($message = 'test message')
    {
        return new RuntimeException($message);
    }

    /**
     * @param  bool $withTrace
     * @param  bool $withTraceArgs
     * @param  int $traceFunctionArgsOutputLimit
     * @param  bool $loggerOnly
     * @param bool $previousOutput
     * @param  null $exception
     * @return array
     */
    private function getPlainTextFromHandler(
        $withTrace = false,
        $withTraceArgs = false,
        $traceFunctionArgsOutputLimit = 1024,
        $loggerOnly = false,
        $previousOutput = false,
        $exception = null
    ) {
        $handler = $this->getHandler();
        $handler->addTraceToOutput($withTrace);
        $handler->addTraceFunctionArgsToOutput($withTraceArgs);
        $handler->setTraceFunctionArgsOutputLimit($traceFunctionArgsOutputLimit);
        $handler->addPreviousToOutput($previousOutput);
        $handler->loggerOnly($loggerOnly);

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        $exception = $exception ?: $this->getException();

        try {
            ob_start();
            $run->handleException($exception);
        } finally {
            return ob_get_clean();
        }
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::__construct
     * @covers Whoops\Handler\PlainTextHandler::setLogger
     */
    public function testConstructor()
    {
        $this->expectExceptionOfType('InvalidArgumentException');

        $this->getHandler(new StdClass());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::setLogger
     */
    public function testSetLogger()
    {
        $this->expectExceptionOfType('InvalidArgumentException');

        $this->getHandler()->setLogger(new StdClass());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     */
    public function testAddTraceToOutput()
    {
        $handler = $this->getHandler();

        $this->assertEquals($handler, $handler->addTraceToOutput(true));
        $this->assertTrue($handler->addTraceToOutput());

        $handler->addTraceToOutput(false);
        $this->assertFalse($handler->addTraceToOutput());

        $handler->addTraceToOutput(null);
        $this->assertEquals(null, $handler->addTraceToOutput());

        $handler->addTraceToOutput(1);
        $this->assertTrue($handler->addTraceToOutput());

        $handler->addTraceToOutput(0);
        $this->assertFalse($handler->addTraceToOutput());

        $handler->addTraceToOutput('');
        $this->assertFalse($handler->addTraceToOutput());

        $handler->addTraceToOutput('false');
        $this->assertTrue($handler->addTraceToOutput());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceFunctionArgsToOutput
     */
    public function testAddTraceFunctionArgsToOutput()
    {
        $handler = $this->getHandler();

        $this->assertEquals($handler, $handler->addTraceFunctionArgsToOutput(true));
        $this->assertTrue($handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(false);
        $this->assertFalse($handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(null);
        $this->assertEquals(null, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(1);
        $this->assertEquals(1, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(0);
        $this->assertEquals(0, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput('');
        $this->assertFalse($handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput('false');
        $this->assertTrue($handler->addTraceFunctionArgsToOutput());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::setTraceFunctionArgsOutputLimit
     * @covers Whoops\Handler\PlainTextHandler::getTraceFunctionArgsOutputLimit
     */
    public function testGetSetTraceFunctionArgsOutputLimit()
    {
        $addTraceFunctionArgsToOutput = 10240;

        $handler = $this->getHandler();

        $handler->setTraceFunctionArgsOutputLimit($addTraceFunctionArgsToOutput);
        $this->assertEquals($addTraceFunctionArgsToOutput, $handler->getTraceFunctionArgsOutputLimit());

        $handler->setTraceFunctionArgsOutputLimit('1024kB');
        $this->assertEquals(1024, $handler->getTraceFunctionArgsOutputLimit());

        $handler->setTraceFunctionArgsOutputLimit('true');
        $this->assertEquals(0, $handler->getTraceFunctionArgsOutputLimit());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::loggerOnly
     */
    public function testLoggerOnly()
    {
        $handler = $this->getHandler();

        $this->assertEquals($handler, $handler->loggerOnly(true));
        $this->assertTrue($handler->loggerOnly());

        $handler->loggerOnly(false);
        $this->assertFalse($handler->loggerOnly());

        $handler->loggerOnly(null);
        $this->assertEquals(null, $handler->loggerOnly());

        $handler->loggerOnly(1);
        $this->assertTrue($handler->loggerOnly());

        $handler->loggerOnly(0);
        $this->assertFalse($handler->loggerOnly());

        $handler->loggerOnly('');
        $this->assertFalse($handler->loggerOnly());

        $handler->loggerOnly('false');
        $this->assertTrue($handler->loggerOnly());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithoutFramesOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = false,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false
        );

        // Check that the response has the correct value:
        // Check that the trace is NOT returned:
        $this->assertEquals(
            sprintf(
                "%s: %s in file %s on line %d\n",
                get_class($this->getException()),
                'test message',
                __FILE__,
                self::DEFAULT_EXCEPTION_LINE
            ),
            $text
        );
    }

    public function testReturnsWithoutPreviousExceptions()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = false,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false,
            $previousOutput = false,
            new RuntimeException('Outer exception message', 0, new RuntimeException('Inner exception message'))
        );

        // Check that the response does not contain Inner exception message:
        $this->assertStringNotContains(
            sprintf(
                "%s: %s in file %s",
                RuntimeException::class,
                'Inner exception message',
                __FILE__
            ),
            $text
        );
    }

    public function testReturnsWithPreviousExceptions()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = false,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false,
            $previousOutput = true,
            new RuntimeException('Outer exception message', 0, new RuntimeException('Inner exception message'))
        );

        // Check that the response has the correct message:
        $this->assertEquals(
            sprintf(
                "%s: %s in file %s on line %d\n" .
                "%s: %s in file %s on line %d\n",
                RuntimeException::class,
                'Outer exception message',
                __FILE__,
                261,
                "\nCaused by\n" . RuntimeException::class,
                'Inner exception message',
                __FILE__,
                261
            ),
            $text
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::getTraceOutput
     * @covers Whoops\Handler\PlainTextHandler::canOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithFramesOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = false,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false
        );

        // Check that the response has the correct value:
        $this->assertStringContains('Stack trace:', $text);

        // Check that the trace is returned:
        $this->assertStringContains(
            sprintf(
                '%3d. %s->%s() %s:%d',
                2,
                __CLASS__,
                'getException',
                __FILE__,
                self::DEFAULT_LINE_OF_CALLER
            ),
            $text
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::addTraceFunctionArgsToOutput
     * @covers Whoops\Handler\PlainTextHandler::getTraceOutput
     * @covers Whoops\Handler\PlainTextHandler::getFrameArgsOutput
     * @covers Whoops\Handler\PlainTextHandler::canOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithFramesAndArgsOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 2048,
            $loggerOnly = false
        );

        $lines = explode("\n", $text);

        // Check that the trace is returned with all arguments:
        $this->assertGreaterThan(60, count($lines));

        // Check that the response has the correct value:
        $this->assertStringContains('Stack trace:', $text);

        // Check that the trace is returned:
        $this->assertStringContains(
            sprintf(
                '%3d. %s->%s() %s:%d',
                2,
                'Whoops\Handler\PlainTextHandlerTest',
                'getException',
                __FILE__,
                self::DEFAULT_LINE_OF_CALLER
            ),
            $text
        );
        // Check that the trace arguments are returned:
        $this->assertStringContains(sprintf(
            '%s  string(%d) "%s"',
            PlainTextHandler::VAR_DUMP_PREFIX,
            strlen('test message'),
            'test message'
            ), $text
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::addTraceFunctionArgsToOutput
     * @covers Whoops\Handler\PlainTextHandler::getTraceOutput
     * @covers Whoops\Handler\PlainTextHandler::getFrameArgsOutput
     * @covers Whoops\Handler\PlainTextHandler::canOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithFramesAndLimitedArgsOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = 3,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false
        );

        // Check that the response has the correct value:
        $this->assertStringContains('Stack trace:', $text);

        // Check that the trace is returned:
        $this->assertStringContains(
            sprintf(
                '%3d. %s->%s() %s:%d',
                2,
                'Whoops\Handler\PlainTextHandlerTest',
                'getException',
                __FILE__,
                self::DEFAULT_LINE_OF_CALLER
            ),
            $text
        );

        // Check that the trace arguments are returned:
        $this->assertStringContains(sprintf(
            '%s  string(%d) "%s"',
            PlainTextHandler::VAR_DUMP_PREFIX,
            strlen('test message'),
            'test message'
            ), $text
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::loggerOnly
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithLoggerOnlyOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = true
        );
        // Check that the response has the correct value:
        $this->assertEquals('', $text);
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::loggerOnly
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testGetFrameArgsOutputUsesDumper()
    {
        $values = [];
        $dumper = function ($var) use (&$values) {
            $values[] = $var;
        };

        $handler = $this->getHandler();
        $handler->setDumper($dumper);

        $args = [
           ['foo', 'bar', 'buz'],
           [1, 2, 'Fizz', 4, 'Buzz'],
        ];

        $actual = self::callPrivateMethod($handler, 'dump', [new Frame(['args' => $args[0]])]);
        $this->assertEquals('', $actual);
        $this->assertCount(1, $values);
        $this->assertEquals($args[0], $values[0]->getArgs());

        $actual = self::callPrivateMethod($handler, 'dump', [new Frame(['args' => $args[1]])]);
        $this->assertEquals('', $actual);
        $this->assertCount(2, $values);
        $this->assertEquals($args[1], $values[1]->getArgs());
    }
}
