<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\PlainTextHandler;
use RuntimeException;
use StdClass;

class PlainTextHandlerTest extends TestCase
{
    /**
     * @throws InvalidArgumentException If argument is not null or a LoggerInterface
     * @param Psr\Log\LoggerInterface|null $logger
     * @return Whoops\Handler\PlainTextHandler
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
     * @param  bool  $withTrace
     * @param  bool  $withTraceArgs
     * @param  bool  $loggerOnly
     * @param  bool  $onlyForCommandLine
     * @param  bool  $outputOnlyIfCommandLine
     * @return array
     */
    private function getPlainTextFromHandler(
        $withTrace = false,
        $withTraceArgs = false,
        $traceFunctionArgsOutputLimit = 1024,
        $loggerOnly = false,
        $onlyForCommandLine = false,
        $outputOnlyIfCommandLine = true
    )
    {
        $handler = $this->getHandler();
        $handler->addTraceToOutput($withTrace);
        $handler->addTraceFunctionArgsToOutput($withTraceArgs);
        $handler->setTraceFunctionArgsOutputLimit($traceFunctionArgsOutputLimit);
        $handler->loggerOnly($loggerOnly);
        $handler->onlyForCommandLine($onlyForCommandLine);
        $handler->outputOnlyIfCommandLine($outputOnlyIfCommandLine);

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        $exception = $this->getException();
        ob_start();
        $run->handleException($exception);

        return ob_get_clean();
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::__construct
     * @covers Whoops\Handler\PlainTextHandler::setLogger
     * @expectedException InvalidArgumentException
     */
    public function testConstructor()
    {
        $logger = new StdClass(); // guaranteed to be invalid!
        $this->getHandler($logger);
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::setLogger
     * @expectedException InvalidArgumentException
     */
    public function testSetLogger()
    {
        $logger = new StdClass(); // guaranteed to be invalid!
        $this->getHandler()->setLogger($logger);
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     */
    public function testAddTraceToOutput()
    {
        $handler = $this->getHandler();

        $handler->addTraceToOutput(true);
        $this->assertEquals(true, $handler->addTraceToOutput());

        $handler->addTraceToOutput(false);
        $this->assertEquals(false, $handler->addTraceToOutput());

        $handler->addTraceToOutput(null);
        $this->assertEquals(null, $handler->addTraceToOutput());

        $handler->addTraceToOutput(1);
        $this->assertEquals(true, $handler->addTraceToOutput());

        $handler->addTraceToOutput(0);
        $this->assertEquals(false, $handler->addTraceToOutput());

        $handler->addTraceToOutput('');
        $this->assertEquals(false, $handler->addTraceToOutput());

        $handler->addTraceToOutput('false');
        $this->assertEquals(true, $handler->addTraceToOutput());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceFunctionArgsToOutput
     */
    public function testAddTraceFunctionArgsToOutput()
    {
        $handler = $this->getHandler();

        $handler->addTraceFunctionArgsToOutput(true);
        $this->assertEquals(true, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(false);
        $this->assertEquals(false, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(null);
        $this->assertEquals(null, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(1);
        $this->assertEquals(1, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput(0);
        $this->assertEquals(0, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput('');
        $this->assertEquals(false, $handler->addTraceFunctionArgsToOutput());

        $handler->addTraceFunctionArgsToOutput('false');
        $this->assertEquals(true, $handler->addTraceFunctionArgsToOutput());
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
     * @covers Whoops\Handler\PlainTextHandler::onlyForCommandLine
     */
    public function testOnlyForCommandLine()
    {
        $handler = $this->getHandler();

        $handler->onlyForCommandLine(true);
        $this->assertEquals(true, $handler->onlyForCommandLine());

        $handler->onlyForCommandLine(false);
        $this->assertEquals(false, $handler->onlyForCommandLine());

        $handler->onlyForCommandLine(null);
        $this->assertEquals(null, $handler->onlyForCommandLine());

        $handler->onlyForCommandLine(1);
        $this->assertEquals(true, $handler->onlyForCommandLine());

        $handler->onlyForCommandLine(0);
        $this->assertEquals(false, $handler->onlyForCommandLine());

        $handler->onlyForCommandLine('');
        $this->assertEquals(false, $handler->onlyForCommandLine());

        $handler->onlyForCommandLine('false');
        $this->assertEquals(true, $handler->onlyForCommandLine());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::outputOnlyIfCommandLine
     */
    public function testOutputOnlyIfCommandLine()
    {
        $handler = $this->getHandler();

        $handler->outputOnlyIfCommandLine(true);
        $this->assertEquals(true, $handler->outputOnlyIfCommandLine());

        $handler->outputOnlyIfCommandLine(false);
        $this->assertEquals(false, $handler->outputOnlyIfCommandLine());

        $handler->outputOnlyIfCommandLine(null);
        $this->assertEquals(null, $handler->outputOnlyIfCommandLine());

        $handler->outputOnlyIfCommandLine(1);
        $this->assertEquals(true, $handler->outputOnlyIfCommandLine());

        $handler->outputOnlyIfCommandLine(0);
        $this->assertEquals(false, $handler->outputOnlyIfCommandLine());

        $handler->outputOnlyIfCommandLine('');
        $this->assertEquals(false, $handler->outputOnlyIfCommandLine());

        $handler->outputOnlyIfCommandLine('false');
        $this->assertEquals(true, $handler->outputOnlyIfCommandLine());
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::loggerOnly
     */
    public function testLoggerOnly()
    {
        $handler = $this->getHandler();

        $handler->loggerOnly(true);
        $this->assertEquals(true, $handler->loggerOnly());

        $handler->loggerOnly(false);
        $this->assertEquals(false, $handler->loggerOnly());

        $handler->loggerOnly(null);
        $this->assertEquals(null, $handler->loggerOnly());

        $handler->loggerOnly(1);
        $this->assertEquals(true, $handler->loggerOnly());

        $handler->loggerOnly(0);
        $this->assertEquals(false, $handler->loggerOnly());

        $handler->loggerOnly('');
        $this->assertEquals(false, $handler->loggerOnly());

        $handler->loggerOnly('false');
        $this->assertEquals(true, $handler->loggerOnly());
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
            $loggerOnly = false,
            $onlyForCommandLine = false,
            $outputOnlyIfCommandLine = true
        );

        // Check that the response has the correct value:
        // Check that the trace is NOT returned:
        $this->assertEquals(
            sprintf(
                "%s: %s in file %s on line %d\n",
                get_class($this->getException()),
                'test message',
                __FILE__,
                30
            ),
            $text
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::getTraceOutput
     * @covers Whoops\Handler\PlainTextHandler::canProcess
     * @covers Whoops\Handler\PlainTextHandler::canOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithFramesOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = false,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false,
            $onlyForCommandLine = false,
            $outputOnlyIfCommandLine = true
        );

        $lines = explode("\n", $text);


        // Check that the response has the correct value:
        $this->assertEquals('Stack trace:', $lines[1]);

        // Check that the trace is returned:
        $this->assertEquals(
            sprintf(
                '%3d. %s->%s() %s:%d',
                2,
                'Whoops\Handler\PlainTextHandlerTest',
                'getException',
                __FILE__,
                62
            ),
            $lines[3]
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::addTraceFunctionArgsToOutput
     * @covers Whoops\Handler\PlainTextHandler::getTraceOutput
     * @covers Whoops\Handler\PlainTextHandler::getFrameArgsOutput
     * @covers Whoops\Handler\PlainTextHandler::canProcess
     * @covers Whoops\Handler\PlainTextHandler::canOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithFramesAndArgsOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 2048,
            $loggerOnly = false,
            $onlyForCommandLine = false,
            $outputOnlyIfCommandLine = true
        );

        $lines = explode("\n", $text);

        // Check that the trace is returned with all arguments:
        $this->assertGreaterThan(60, count($lines));

        // Check that the response has the correct value:
        $this->assertEquals('Stack trace:', $lines[1]);

        // Check that the trace is returned:
        $this->assertEquals(
            sprintf(
                '%3d. %s->%s() %s:%d',
                2,
                'Whoops\Handler\PlainTextHandlerTest',
                'getException',
                __FILE__,
                62
            ),
            $lines[8]
        );
        // Check that the trace arguments are returned:
        $this->assertEquals(sprintf(
            '%s  string(%d) "%s"',
            PlainTextHandler::VAR_DUMP_PREFIX,
            strlen('test message'),
            'test message'
            ), $lines[5]
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::addTraceToOutput
     * @covers Whoops\Handler\PlainTextHandler::addTraceFunctionArgsToOutput
     * @covers Whoops\Handler\PlainTextHandler::getTraceOutput
     * @covers Whoops\Handler\PlainTextHandler::getFrameArgsOutput
     * @covers Whoops\Handler\PlainTextHandler::canProcess
     * @covers Whoops\Handler\PlainTextHandler::canOutput
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithFramesAndLimitedArgsOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = 3,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = false,
            $onlyForCommandLine = false,
            $outputOnlyIfCommandLine = true
        );

        $lines = explode("\n", $text);

        // Check that the response has the correct value:
        $this->assertEquals('Stack trace:', $lines[1]);

        // Check that the trace is returned:
        $this->assertEquals(
            sprintf(
                '%3d. %s->%s() %s:%d',
                2,
                'Whoops\Handler\PlainTextHandlerTest',
                'getException',
                __FILE__,
                62
            ),
            $lines[8]
        );

        // Check that the trace arguments are returned:
        $this->assertEquals(sprintf(
            '%s  string(%d) "%s"',
            PlainTextHandler::VAR_DUMP_PREFIX,
            strlen('test message'),
            'test message'
            ), $lines[5]
        );
    }

    /**
     * @covers Whoops\Handler\PlainTextHandler::loggerOnly
     * @covers Whoops\Handler\PlainTextHandler::canProcess
     * @covers Whoops\Handler\PlainTextHandler::handle
     */
    public function testReturnsWithLoggerOnlyOutput()
    {
        $text = $this->getPlainTextFromHandler(
            $withTrace = true,
            $withTraceArgs = true,
            $traceFunctionArgsOutputLimit = 1024,
            $loggerOnly = true,
            $onlyForCommandLine = false,
            $outputOnlyIfCommandLine = true
        );
        // Check that the response has the correct value:
        $this->assertEquals('', $text);
    }
}
