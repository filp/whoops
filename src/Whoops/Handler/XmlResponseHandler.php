<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use SimpleXMLElement;
use Whoops\Exception\Frame;
use Whoops\Handler\Handler;

/**
 * Catches an exception and converts it to an XML
 * response. Additionally can also return exception
 * frames for consumption by an API.
 */
class XmlResponseHandler extends Handler
{
    /**
     * @var bool
     */
    private $returnFrames = false;

    /**
     * @param  bool|null $returnFrames
     * @return null|bool
     */
    public function addTraceToOutput($returnFrames = null)
    {
        if(func_num_args() == 0) {
            return $this->returnFrames;
        }

        $this->returnFrames = (bool) $returnFrames;
    }

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @author  k dot antczak at livedata dot pl
     * @date    2011-04-22 06:08 UTC
     * @link    http://snipplr.com/view/3491/
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public function toXml($data, $rootNodeName = 'root', $xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1)
        {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null)
        {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // loop through the data passed in.
        foreach($data as $key => $value)
        {
            // no numeric keys in our xml please!
            if (is_numeric($key))
            {
                // make string key...
                $key = "unknownNode_". (string) $key;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recrusively call this function
            if (is_array($value))
            {
                $node = $xml->addChild($key);
                // recrusive call.
                $this->toXml($value, $rootNodeName, $node);
            }
            else
            {
                // add single node.
                $value = str_replace('&', '&amp;', print_r($value, true));
                $xml->addChild($key,$value);
            }
        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

    /**
     * @return int
     */
    public function handle()
    {
        $exception = $this->getException();

        $response = array(
            'error' => array(
                'type'    => get_class($exception),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine()
            )
        );

        if($this->addTraceToOutput()) {
            $inspector = $this->getInspector();
            $frames    = $inspector->getFrames();
            $frameData = array();

            foreach($frames as $frame) {
                /** @var Frame $frame */
                $frameData[] = array(
                    'file'     => $frame->getFile(),
                    'line'     => $frame->getLine(),
                    'function' => $frame->getFunction(),
                    'class'    => $frame->getClass(),
                    'args'     => $frame->getArgs()
                );
            }

            $response['error']['trace'] = array_flip($frameData);
        }

        echo $this->toXml($response);

        return Handler::QUIT;
    }
}
