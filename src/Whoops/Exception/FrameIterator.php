<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Frame;
use Iterator;
use Countable;

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
     * @return Whoops\Exception\Frame|false
     */
    public function current()
    {
        $current = current($this->frames);
        
        if($current === false) {
            return false;
        }

        if(!$current instanceof Frame) {
            $current = $this->frames[$this->key()] = new Frame($current);
        }

        return $current;
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
        next($this->frames);
    }

    public function rewind()
    {
        reset($this->frames);
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
