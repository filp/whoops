<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use Damnit\Exception\Frame;
use \Iterator;
use \Countable;

/**
 * Mostly just implements iterator methods, the only
 * notable aspects is that it is read-only, and instantiates
 * Frame objects on demand.
 */
class FrameIterator implements Iterator, Countable
{
    /**
     * @var array[]
     */
    private $frames;

    /**
     * @return Damnit\Exception\Frame|false
     */
    public function current()
    {
        $current = current($this->frames);
        if($current !== false) {
            return new Frame($current);
        }

        return false;
    }

    /**
     * @param array[]
     */
    public function __construct(array $frames)
    {
        $this->frames = $frames;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    public function next()
    {
        next($this->current);
    }

    public function rewind()
    {
        reset($this->current);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->frames);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->frames);
    }
}
