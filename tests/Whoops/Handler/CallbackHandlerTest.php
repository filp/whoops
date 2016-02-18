<?php

namespace Whoops\Handler;

use Whoops\TestCase;
use Whoops\Handler\CallbackHandler;
use Whoops\Exception\Inspector;

class CallbackHandlerTest extends TestCase
{
    public function testSimplifiedBacktrace() {
        $handler = new CallbackHandler(function($exception, $inspector, $run) {
            return debug_backtrace();
        });
        $handler->setInspector(new Inspector(new \Exception()));
        $backtrace = $handler->handle();
        
        foreach($backtrace as $frame) {
            $this->assertNotContains('call_user_func', $frame['function']);
        }
    }
}
