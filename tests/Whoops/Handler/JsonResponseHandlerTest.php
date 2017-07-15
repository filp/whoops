<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use RuntimeException;
use Whoops\TestCase;

class JsonResponseHandlerTest extends TestCase
{
    /**
     * @return Whoops\Handler\JsonResponseHandler
     */
    private function getHandler()
    {
        return new JsonResponseHandler();
    }

    /**
     * @return RuntimeException
     */
    public function getException($message = 'test message')
    {
        $exception = new RuntimeException($message);
        $exception->foo = 'bar'; // public property
        return $exception;
    }

    /**
     * @param  bool  $withTrace
     * @return array
     */
    private function getJsonResponseFromHandler($withTrace = false, $jsonApi = false, $discoverPublicProperties = false)
    {
        $handler = $this->getHandler();
        $handler->setJsonApi($jsonApi);
        $handler->addTraceToOutput($withTrace);
        $handler->discoverPublicProperties($discoverPublicProperties);

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        $exception = $this->getException();
        ob_start();
        $run->handleException($exception);
        $json = json_decode(ob_get_clean(), true);

        // Check that the json response is parse-able:
        $this->assertEquals(json_last_error(), JSON_ERROR_NONE);

        return $json;
    }

    /**
     * @covers Whoops\Handler\JsonResponseHandler::addTraceToOutput
     * @covers Whoops\Handler\JsonResponseHandler::handle
     */
    public function testReturnsWithoutFrames()
    {
        $json = $this->getJsonResponseFromHandler($withTrace = false,$jsonApi = false);

        // Check that the response has the expected keys:
        $this->assertArrayHasKey('error', $json);
        $this->assertArrayHasKey('type', $json['error']);
        $this->assertArrayHasKey('file', $json['error']);
        $this->assertArrayHasKey('line', $json['error']);

        // Check the field values:
        $this->assertEquals($json['error']['file'], __FILE__);
        $this->assertEquals($json['error']['message'], 'test message');
        $this->assertEquals($json['error']['type'], get_class($this->getException()));

        // Check that the trace is NOT returned:
        $this->assertArrayNotHasKey('trace', $json['error']);
    }

    /**
     * @covers Whoops\Handler\JsonResponseHandler::addTraceToOutput
     * @covers Whoops\Handler\JsonResponseHandler::handle
     */
    public function testReturnsWithFrames()
    {
        $json = $this->getJsonResponseFromHandler($withTrace = true, $jsonApi = false);

        // Check that the trace is returned:
        $this->assertArrayHasKey('trace', $json['error']);

        // Check that a random frame has the expected fields
        $traceFrame = reset($json['error']['trace']);
        $this->assertArrayHasKey('file', $traceFrame);
        $this->assertArrayHasKey('line', $traceFrame);
        $this->assertArrayHasKey('function', $traceFrame);
        $this->assertArrayHasKey('class', $traceFrame);
        $this->assertArrayHasKey('args', $traceFrame);
    }

    /**
     * @covers Whoops\Handler\JsonResponseHandler::addTraceToOutput
     * @covers Whoops\Handler\JsonResponseHandler::handle
     */
    public function testReturnsJsonApi()
    {
        $json = $this->getJsonResponseFromHandler(
            $withTrace = false,
            $jsonApi = true,
            $discoverPublicProperties = false
        );

        // Check that the response has the expected keys:
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('type', $json['errors'][0]);
        $this->assertArrayHasKey('file', $json['errors'][0]);
        $this->assertArrayHasKey('line', $json['errors'][0]);

        // Check the field values:
        $this->assertEquals($json['errors'][0]['file'], __FILE__);
        $this->assertEquals($json['errors'][0]['message'], 'test message');
        $this->assertEquals($json['errors'][0]['type'], get_class($this->getException()));

        // Check that the trace is NOT returned:
        $this->assertArrayNotHasKey('trace', $json['errors']);
        // Check that the public properties are NOT returned:
        $this->assertArrayNotHasKey('properties', $json['errors'][0]);
    }

    /**
     * @covers Whoops\Handler\JsonResponseHandler::discoverPublicProperties
     * @covers Whoops\Handler\JsonResponseHandler::handle
     */
    public function testReturnsPublicProperties()
    {
        $json = $this->getJsonResponseFromHandler(
            $withTrace = false,
            $jsonApi = false,
            $discoverPublicProperties = true
        );

        // Check that the response has the expected key:
        $this->assertArrayHasKey('properties', $json['error']);

        // Check the field values:
        $this->assertEquals($json['error']['properties']['foo'], 'bar');
    }
}
