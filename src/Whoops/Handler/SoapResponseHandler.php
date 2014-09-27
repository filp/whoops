<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;


/**
 * Catches an exception and converts it to an Soap XML
 * response.
 *
 * @author Markus Staab <http://github.com/staabm>
 */
class SoapResponseHandler extends Handler
{
    /**
     * @return int
     */
    public function handle()
    {
        $exception = $this->getException();

        echo $this->toXml($exception);

        return Handler::QUIT;
    }

    /**
     * Converts a Exception into a SoapFault XML
     */
    private function toXml(\Exception $exception)
    {
        $xml = '';
        $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">';
        $xml .= '  <SOAP-ENV:Body>';
        $xml .= '    <SOAP-ENV:Fault>';
        $xml .= '      <faultcode>'. htmlspecialchars($exception->getCode()) .'</faultcode>';
        $xml .= '      <faultstring>'. htmlspecialchars($exception->getMessage()) .'</faultstring>';
        $xml .= '      <detail><trace>'. htmlspecialchars($exception->getTraceAsString()) .'</trace></detail>';
        $xml .= '    </SOAP-ENV:Fault>';
        $xml .= '  </SOAP-ENV:Body>';
        $xml .= '</SOAP-ENV:Envelope>';

        return $xml;
    }
}
