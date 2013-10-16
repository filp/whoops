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
 * Catches an exception and converts it to a JSON
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
	 *
	 * @param array $array
	 * @param string $rootNodeName
	 * @return string
	 */
	public function toXml($array, $rootNodeName = 'root')
	{
		return $this->_toXml($array, new \SimpleXMLElement('<' . $rootElement . ' />'));
	}

	/**
	 *
	 * @param array $array
	 * @param \SimpleXMLElement $xml
	 * @return string
	 */
	private function _toXml($array, $xml)
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$child = $xml->addChild($key);
				$this->_toXml($value, $child);
			}
			else {
				$xml->addChild($key, $value);
			}
		}
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

        $extraTables = array_map(function($table) {
            return $table instanceof \Closure ? $table() : $table;
        }, $this->getDataTables());

		if (count($extraTables) > 0)
		{
			$response['data'] = $extraTables;
		}

		echo $this->toXml($response);

        return Handler::QUIT;
    }
}
