<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

use Whoops\TestCase;

class FormatterTest extends TestCase
{
    private $errors;

    protected function setUp() {
        $this->errors = [];
        set_error_handler([$this, 'errorHandler']);
    }

    protected function tearDown()
    {
        restore_error_handler();
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $this->errors[] = compact('errno', 'errstr', 'errfile', 'errline', 'errcontext');
    }

    public function testPlain()
    {
        $msg = 'Sample exception message foo';
        $output = Formatter::formatExceptionPlain(new Inspector(new \Exception($msg)));
        $this->assertContains($msg, $output);
        $this->assertContains('Stacktrace', $output);

        self::assertCount(1, $this->errors);
        self::assertEquals(
            'Plain exception formatter is deprecated and will be removed in next releases. Use Whoops\Handler\PlainTextHandler::generateResponse instead',
            $this->errors[0]['errstr']
        );
    }
}
