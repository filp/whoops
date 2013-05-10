<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\Exception\FrameCollection;
use Whoops\TestCase;
use Mockery as m;

class FrameCollectionTest extends TestCase
{
    /**
     * @return array
     */
    private function getFrameData()
    {
        return array(
            'file'     => __DIR__ . '/../../fixtures/frame.lines-test.php',
            'line'     => 0,
            'function' => 'test',
            'class'    => 'MyClass',
            'args'     => array(true, 'hello')
        );
    }

    /**
     * @param  array $frames
     * @return Whoops\Exception\FrameCollection
     */
    private function getFrameCollectionInstance($frames = null)
    {
        if($frames === null) {
            $self = $this;

            // Get 10 frames
            $frames = array_map(function() use($self) {
                return $self->getFrameData();
            }, range(1, 10));
        }

        return new FrameCollection($frames);
    }

    /**
     * @covers Whoops\Exception\FrameCollection::getIterator
     */
    public function testCollectionIsIterable()
    {
        $frames = $this->getFrameCollectionInstance();
        foreach($frames as $frame) {
            $this->assertInstanceOf('Whoops\\Exception\\Frame', $frame);
        }
    }

    /**
     * @covers Whoops\Exception\FrameCollection::serialize
     * @covers Whoops\Exception\FrameCollection::unserialize
     */
    public function testCollectionIsSerializable()
    {
        $frames           = $this->getFrameCollectionInstance();
        $serializedFrames = serialize($frames);
        $newFrames        = unserialize($serializedFrames);

        foreach($newFrames as $frame) {
            $this->assertInstanceOf('Whoops\\Exception\\Frame', $frame);
        }
    }
}
