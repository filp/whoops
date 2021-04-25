<?php
namespace Whoops\Util;

use PHPUnit\Framework\TestCase;

class SystemFacadeTest extends TestCase
{
    /**
     * @var \Mockery\Mock
     */
    public static $runtime;

    /**
     * @var SystemFacade
     */
    private $facade;

    public static function delegate($fn, array $args = [])
    {
        return self::$runtime
            ? call_user_func_array([self::$runtime, $fn], $args)
            : call_user_func_array("\\$fn", $args);
    }

    /**
     * @before
     */
    public function getReady()
    {
        self::$runtime = \Mockery::mock(['ob_start' => true]);
        $this->facade = new SystemFacade();
    }

    /**
     * @after
     */
    public function finishUp()
    {
        self::$runtime = null;
        \Mockery::close();
    }

    public function test_it_delegates_output_buffering_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('ob_start')->once();

        $this->facade->startOutputBuffering();
    }

    public function test_it_delegates_cleaning_output_buffering_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('ob_get_clean')->once();

        $this->facade->cleanOutputBuffer();
    }

    public function test_it_delegates_getting_the_current_buffer_level_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('ob_get_level')->once();

        $this->facade->getOutputBufferLevel();
    }

    public function test_it_delegates_ending_the_current_buffer_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('ob_end_clean')->once();

        $this->facade->endOutputBuffering();
    }

    public function test_it_delegates_flushing_the_current_buffer_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('flush')->once();

        $this->facade->flushOutputBuffer();
    }

    public function test_it_delegates_error_handling_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('set_error_handler')->once();

        $this->facade->setErrorHandler(function(){});
    }

    public function test_it_delegates_error_handling_with_level_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('set_error_handler')->once();

        $this->facade->setErrorHandler(function(){}, E_CORE_ERROR);
    }

    public function test_it_delegates_exception_handling_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('set_exception_handler')->once();

        $this->facade->setExceptionHandler(function(){});
    }

    public function test_it_delegates_restoring_the_exception_handler_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('restore_exception_handler')->once();

        $this->facade->restoreExceptionHandler();
    }

    public function test_it_delegates_restoring_the_error_handler_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('restore_error_handler')->once();

        $this->facade->restoreErrorHandler();
    }

    public function test_it_delegates_registering_a_shutdown_function_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('register_shutdown_function')->once();

        $this->facade->registerShutdownFunction(function(){});
    }

    public function test_it_delegates_error_reporting_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('error_reporting')->once()->withNoArgs();

        $this->facade->getErrorReportingLevel();
    }

    public function test_it_delegates_getting_the_last_error_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('error_get_last')->once()->withNoArgs();

        $this->facade->getLastError();
    }

    public function test_it_delegates_sending_an_http_response_code_to_the_native_implementation()
    {
        self::$runtime->shouldReceive('headers_sent')->once()->withNoArgs();
        self::$runtime->shouldReceive('header_remove')->once()->with('location');
        self::$runtime->shouldReceive('http_response_code')->once()->with(230);

        $this->facade->setHttpResponseCode(230);
    }
}

function ob_start()
{
    return SystemFacadeTest::delegate('ob_start');
}

function ob_get_clean()
{
    return SystemFacadeTest::delegate('ob_get_clean');
}

function ob_get_level()
{
    return SystemFacadeTest::delegate('ob_get_level');
}

function ob_end_clean()
{
    return SystemFacadeTest::delegate('ob_end_clean');
}

function flush()
{
    return SystemFacadeTest::delegate('flush');
}

function set_error_handler(callable $handler, $types = 'use-php-defaults')
{
    // Workaround for PHP 5.5
    if ($types === 'use-php-defaults') {
        $types = E_ALL | E_STRICT;
    }
    return SystemFacadeTest::delegate('set_error_handler', func_get_args());
}

function set_exception_handler(callable $handler)
{
    return SystemFacadeTest::delegate('set_exception_handler', func_get_args());
}

function restore_exception_handler()
{
    return SystemFacadeTest::delegate('restore_exception_handler');
}

function restore_error_handler()
{
    return SystemFacadeTest::delegate('restore_error_handler');
}

function register_shutdown_function()
{
    return SystemFacadeTest::delegate('register_shutdown_function', func_get_args());
}

function error_reporting($level = null)
{
    return SystemFacadeTest::delegate('error_reporting', func_get_args());
}

function error_get_last()
{
    return SystemFacadeTest::delegate('error_get_last', func_get_args());
}

function header_remove($header = null)
{
    return SystemFacadeTest::delegate('header_remove', func_get_args());
}

function headers_sent(&$filename = null, &$line = null)
{
    return SystemFacadeTest::delegate('headers_sent', func_get_args());
}

function http_response_code($code = null)
{
    return SystemFacadeTest::delegate('http_response_code', func_get_args());
}
