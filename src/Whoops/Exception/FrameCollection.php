<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Frame;
use IteratorAggregate;
use ArrayIterator;
use Serializable;

/**
 * Mostly just implements iterator methods, the only
 * notable aspects is that it is read-only, and instantiates
 * Frame objects on demand.
 */
class FrameCollection implements IteratorAggregate, Serializable
{
    /**
     * @var array[]
     */
    private $frames;

    /**
     * @param array $frames
     */
    public function __construct(array $frames)
    {
        $this->frames = array_map(function($frame) {
            return new Frame($frame);
        }, $frames);
    }

    /**
     * @see IteratorAggregate::getIterator
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->frames);
    }

    /**
     * @see Serializable::serialize
     * @return string
     */
    public function serialize()
    {
        return serialize($this->frames);
    }

    /**
     * @see Serializable::unserialize
     * @param string $serializedFrames
     */
    public function unserialize($serializedFrames)
    {
        $this->frames = unserialize($serializedFrames);
    }
}
