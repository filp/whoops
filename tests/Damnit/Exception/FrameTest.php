<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use Damnit\Exception\Frame;
use Damnit\TestCase;
use \Mockery as m;

class FrameTest extends TestCase
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
     * @return Damnit\Exception\Frame
     */
    private function getFrameInstance($data = null)
    {
        if($data === null) {
            $data = $this->getFrameData();
        }

        return new Frame($data);
    }

    /**
     * @covers Damnit\Exception\Frame::getFile
     */
    public function testGetFile()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getFile(), $data['file']);
    }

    /**
     * @covers Damnit\Exception\Frame::getLine
     */
    public function testGetLine()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getLine(), $data['line']);
    }

    /**
     * @covers Damnit\Exception\Frame::getClass
     */
    public function testGetClass()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getClass(), $data['class']);
    }

    /**
     * @covers Damnit\Exception\Frame::getFunction
     */
    public function testGetFunction()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getFunction(), $data['function']);
    }

    /**
     * @covers Damnit\Exception\Frame::getArgs
     */
    public function testGetArgs()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getArgs(), $data['args']);
    }

    /**
     * @covers Damnit\Exception\Frame::getFileContents
     */
    public function testGetFileContents()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $this->assertEquals($frame->getFileContents(), file_get_contents($data['file']));
    }

    /**
     * @covers Damnit\Exception\Frame::getFileLines
     */
    public function testGetFileLines()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $lines = explode("\n", $frame->getFileContents());
        $this->assertEquals($frame->getFileLines(), $lines);
    }

    /**
     * @covers Damnit\Exception\Frame::getFileLines
     */
    public function testGetFileLinesRange()
    {
        $data  = $this->getFrameData();
        $frame = $this->getFrameInstance($data);

        $lines = $frame->getFileLines(1, 3);

        $this->assertEquals($lines[1], '// Line 2');
        $this->assertEquals($lines[2], '// Line 3');
        $this->assertEquals($lines[3], '// Line 4');
    }
}
