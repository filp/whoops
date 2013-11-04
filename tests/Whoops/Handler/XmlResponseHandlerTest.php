<?php
/**
 * Whoops - php errors for cool kids
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\XmlResponseHandler;
use RuntimeException;

class XmlResponseHandlerTest extends TestCase
{
    public function testValidXml()
    {
        $handler = new XmlResponseHandler;

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        ob_start();
        $run->handleException(new RuntimeException);
        $xml = ob_get_clean();

        $this->assertTrue($this->isValidXml($xml));
    }

    private function isValidXml($data)
    {
        $prev = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($data);
        libxml_use_internal_errors($prev);
        return $xml !== false;
    }
}
