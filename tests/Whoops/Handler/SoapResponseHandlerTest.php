<?php
/**
 * Whoops - php errors for cool kids
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\SoapResponseHandler;
use RuntimeException;

class SoapResponseHandlerTest extends TestCase
{
    public function testSimpleValid()
    {
        $handler = new SoapResponseHandler;

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        ob_start();
        $run->handleException($this->getException());
        $data = ob_get_clean();

        $this->assertTrue($this->isValidXml($data));

        return simplexml_load_string($data);
    }

    /**
     * @depends testSimpleValid
     */
    public function testSimpleValidCode(\SimpleXMLElement $xml)
    {
        $this->checkField($xml, 'faultcode', (string) $this->getException()->getCode());
    }

    /**
     * @depends testSimpleValid
     */
    public function testSimpleValidMessage(\SimpleXMLElement $xml)
    {
        $this->checkField($xml, 'faultstring', $this->getException()->getMessage());
    }


    /**
     * Helper for testSimpleValid*
     */
    private function checkField(\SimpleXMLElement $xml, $field, $value)
    {
        $list = $xml->xpath('/SOAP-ENV:Envelope/SOAP-ENV:Body/SOAP-ENV:Fault/'.$field);
        $this->assertArrayHasKey(0, $list);
        $this->assertSame($value, (string) $list[0]);
    }

    private function getException()
    {
        return new RuntimeException('boom', 678);
    }

    /**
     * See if passed string is a valid XML document
     * @param string $data
     * @return bool
     */
    private function isValidXml($data)
    {
        $prev = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($data);
        libxml_use_internal_errors($prev);
        return $xml !== false;
    }
}
