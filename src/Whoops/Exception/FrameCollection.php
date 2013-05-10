<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\Frame;
use IteratorAggregate;

/**
 * Mostly just implements iterator methods, the only
 * notable aspects is that it is read-only, and instantiates
 * Frame objects on demand.
 */
class FrameCollection implements IteratorAggregate
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
     * @return Whoops\Exception\Frame[]
     */
    public function getIterator()
    {
        return $this->frames;
    }
}
