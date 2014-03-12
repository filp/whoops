<?php
namespace Whoops;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
	public function testAutoload()
	{
		$this->assertTrue(class_exists('\Whoops\Module'));
	}
}
