<?php

namespace Whoops\Handler;

use Whoops\TestCase;

class CallbackHandlerTest extends TestCase
{
    public function testSimplifiedBacktrace()
    {
        $handler = new CallbackHandler(function ($exception, $inspector, $run) {
            return debug_backtrace();
        });
        $backtrace = $handler->handle();
        
        foreach ($backtrace as $frame) {
            $this->assertStringNotContains('call_user_func', $frame['function']);
        }
    }
}
