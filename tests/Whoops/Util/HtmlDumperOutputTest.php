<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;

use Whoops\TestCase;

class HtmlDumperOutputTest extends TestCase
{
    /**
     * @covers Whoops\Util::__invoke
     * @covers Whoops\Util::getOutput
     */
    public function testOutput()
    {
        $htmlDumperOutput = new HtmlDumperOutput();
        $htmlDumperOutput('first line', 0);
        $htmlDumperOutput('second line', 2);

        $expectedOutput = <<<string
first line
    second line

string;

        $this->assertSame($expectedOutput, $htmlDumperOutput->getOutput());
    }

    /**
     * @covers Whoops\Util::clear
     */
    public function testClear()
    {
        $htmlDumperOutput = new HtmlDumperOutput();
        $htmlDumperOutput('first line', 0);
        $htmlDumperOutput('second line', 2);
        $htmlDumperOutput->clear();

        $this->assertNull($htmlDumperOutput->getOutput());
    }
}
