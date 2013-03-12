<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use \InvalidArgumentException;

class Frame
{
    /**
     * @var array
     */
    protected $frame;

    /**
     * @var string
     */
    protected $fileContentsCache;

    /**
     * @param array[]
     */
    public function __construct(array $frame)
    {
        $this->frame = $frame;
    }

    /**
     * @return string|null
     */
    public function getFile()
    {
        return !empty($this->frame['file']) ? $this->frame['file'] : null;
    }

    /**
     * @return int|null
     */
    public function getLine()
    {
        return isset($this->frame['line']) ? $this->frame['line'] : null;
    }

    /**
     * @return string|null
     */
    public function getClass()
    {
        return isset($this->frame['class']) ? $this->frame['class'] : null;
    }

    /**
     * @return string|null
     */
    public function getFunction()
    {
        return isset($this->frame['function']) ? $this->frame['function'] : null;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return isset($this->frame['args']) ? (array) $this->frame['args'] : array();
    }

    /**
     * Returns the full contents of the file for this frame,
     * if it's known.
     * @return string|null
     */
    public function getFileContents()
    {
        if($this->fileContentsCache === null && $filePath = $this->getFile()) {
            $this->fileContentsCache = file_get_contents($filePath);
        }

        return $this->fileContentsCache;
    }

    /**
     * Returns the contents of the file for this frame as an
     * array of lines, and optionally as clamped range of lines.
     *
     * @example
     *     $frame->getFileLines(); // => array( 0 => '<?php', 1 => '...', ...)
     * @example
     *     $frame->getFileLines(10, 15); // array( 10 => '...', 11 => '...', ...)
     *
     * @param  int $start
     * @param  int $end
     * @return array|null
     */
    public function getFileLines($start = 0, $end = null)
    {
        if(null !== ($contents = $this->getFileContents())) {
            $lines = explode("\n", $contents);

            // Get a subset of lines from $start to $end
            if($end !== null)
            {
                $start = (int) $start;
                $end   = (int) $end;

                if($end <= $start) {
                    throw new InvalidArgumentException(
                        "\$end($end) cannot be lower or equal to \$start($start)"
                    );
                }

                $lines = array_slice($lines, $start, $end, true);
            }

            return $lines;
        }
    }
}
