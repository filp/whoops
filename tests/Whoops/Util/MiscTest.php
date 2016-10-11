<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;

use Whoops\TestCase;

class MiscTest extends TestCase
{
    /**
     * @dataProvider provideTranslateException
     * @param string $expected_output
     * @param int    $exception_code
     */
    public function testTranslateException($expected_output, $exception_code)
    {
        $output = Misc::translateErrorCode($exception_code);
        $this->assertEquals($expected_output, $output);
    }

    public function provideTranslateException()
    {
        return [
            // When passing an error constant value, ensure the error constant
            // is returned.
            ['E_USER_WARNING', E_USER_WARNING],

            // When passing a value not equal to an error constant, ensure
            // E_UNKNOWN is returned.
            ['E_UNKNOWN', 3],
        ];
    }
}
