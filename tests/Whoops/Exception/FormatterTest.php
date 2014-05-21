<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;
use Whoops\TestCase;

class FormatterTest extends TestCase
{
	public function testPlain()
	{
		$msg = 'Sample exception message foo';
		$output = Formatter::formatExceptionPlain(new Inspector(new \Exception($msg)));
		$this->assertContains($msg, $output);
		$this->assertContains('Stacktrace', $output);
	}
}
