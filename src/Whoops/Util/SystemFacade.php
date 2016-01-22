<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;

class SystemFacade
{
    /**
     * Turns on output buffering.
     *
     * @return bool
     */
    public function startOutputBuffering()
    {
        return ob_start();
    }

    /**
     * @param callable $handler
     * @param int      $types
     *
     * @return callable|null
     */
    public function setErrorHandler(callable $handler, $types = E_ALL | E_STRICT)
    {
        return set_error_handler($handler, $types);
    }

    /**
     * @param callable $handler
     *
     * @return callable|null
     */
    public function setExceptionHandler(callable $handler)
    {
        return set_exception_handler($handler);
    }

    /**
     * @return void
     */
    public function restoreExceptionHandler()
    {
        restore_exception_handler();
    }

    /**
     * @return void
     */
    public function restoreErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * @param callable $function
     *
     * @return void
     */
    public function registerShutdownFunction(callable $function)
    {
        register_shutdown_function($function);
    }

    /**
     * @return string|false
     */
    public function cleanOutputBuffer()
    {
        return ob_get_clean();
    }

    /**
     * @return int
     */
    public function getOutputBufferLevel()
    {
        return ob_get_level();
    }

    /**
     * @return bool
     */
    public function endOutputBuffering()
    {
        return ob_end_clean();
    }

    /**
     * @return void
     */
    public function flushOutputBuffer()
    {
        flush();
    }

    /**
     * @return int
     */
    public function getErrorReportingLevel()
    {
        return error_reporting();
    }

    /**
     * @return array|null
     */
    public function getLastError()
    {
        return error_get_last();
    }

    /**
     * @param int $httpCode
     *
     * @return int
     */
    public function setHttpResponseCode($httpCode)
    {
        if (function_exists('http_response_code')) {
            return http_response_code($httpCode);
        }

        // http_response_code is added in 5.4.
        // For compatibility with 5.3 we use the third argument in header call
        // First argument must be a real header.
        // If it is empty, PHP will ignore the third argument.
        // If it is invalid, such as a single space, Apache will handle it well,
        // but the PHP development server will hang.
        // Setting a full status line would require us to hardcode
        // string values for all different status code, and detect the protocol.
        // which is an extra error-prone complexity.
        header('X-Ignore-This: 1', true, $httpCode);

        return $httpCode;
    }

    /**
     * @param int $exitStatus
     */
    public function stopExecution($exitStatus)
    {
        exit($exitStatus);
    }
}
