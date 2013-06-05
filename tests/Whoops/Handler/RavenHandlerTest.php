<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\RavenHandler;
use RuntimeException;

class RavenHandlerTest extends TestCase
{
    /**
     * @return Whoops\Handler\JsonResponseHandler
     */
    private function getHandler()
    {
        return new RavenHandler;
    }

    /**
     * @return RuntimeException
     */
    public function getException($message = 'test message')
    {
        return new RuntimeException($message);
    }

    /**
     * @covers Whoops\Handler\RavenHandler::ravenDsn
     */
    public function testRavenDsnString()
    {
        $handler = $this->getHandler();
        // Check that nothing has been set yet.
        $this->assertEquals($handler->ravenDsn(), null);
        // Set the Raven DSN to a string, and then check that it gets returned correctly.
        $dsn = 'https://public:secret@app.getsentry.com/1234';
        $handler->ravenDsn($dsn);
        $this->assertSame($handler->ravenDsn(), $dsn);
        // Make sure that a string is returned regardless of what is set.
        $handler->ravenDsn(1234);
        $this->assertSame($handler->ravenDsn(), '1234');
    }

    /**
     * @covers Whoops\Handler\RavenHandler::ravenOptions
     */
    public function testRavenOptions()
    {
        $handler = $this->getHandler();
        // Check that nothing has been set yet.
        $this->assertSame($handler->ravenOptions(), array());
        // Set the Raven DSN to a string, and then check that it gets returned correctly.
        $options = array('key' => 'value');
        $handler->ravenOptions($options);
        $this->assertSame($handler->ravenOptions(), $options);
        // Make sure that a string is returned regardless of what is set.
        $handler->ravenOptions(1234);
        $this->assertSame($handler->ravenOptions(), array(1234));
    }

    /**
     * @covers Whoops\Handler\RavenHandler::extraData
     */
    public function testExtraData()
    {
        $handler = $this->getHandler();
        // Check that nothing has been set yet.
        $this->assertSame($handler->extraData(), array());
        // Set the Raven DSN to a string, and then check that it gets returned correctly.
        $options = array('key' => 'value');
        $handler->extraData($options);
        $this->assertSame($handler->extraData(), $options);
        // Make sure that a string is returned regardless of what is set.
        $handler->extraData(1234);
        $this->assertSame($handler->extraData(), array(1234));
    }

    public function testHandlerMayNotHaveRavenAvailable()
    {
        $handler = $this->getHandler();

        $handler->ravenDsn('https://public:secret@app.getsentry.com/1234');
        $handler->ravenOptions(array('key' => 'value'));
        $handler->extraData(array('key' => 'value'));

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();
        $exception = $this->getException();
        ob_start();
        $run->handleException($exception);
    }

}
