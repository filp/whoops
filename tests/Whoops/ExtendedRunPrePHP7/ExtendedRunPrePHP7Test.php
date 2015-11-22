<?php
namespace Whoops\ExtendedRunPrePHP7;

class Run extends \Whoops\Run {
    // Simulate users having extended the Run
    // If we change the typehint in main Run, this will throw
    public function handleException(\Exception $exception)
    {
        return parent::handleException($exception);
    }
}


class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Whoops\Run::handleExceptionFromPHP
     */
    public function testPHP7ErrorHandles()
    {
        if (!class_exists('Error')) {
            return $this->markTestSkipped('This test is only for PHP 7');
        }

        $run = $this->getRunInstance();

        $run->pushHandler($this->getHandler());

        $run->handleExceptionFromPHP(new \Error());
    }

    public function testPHP7ErrorHandlingDoesNotWrap()
    {
        $run = $this->getRunInstance();

        $test = $this;
        $run->pushHandler($this->getHandler(function ($exception) use ($test) {
            $test->assertEquals($exception->testData, 'foo');
        }));

        $exc = new \Exception();
        $exc->testData = 'foo';
        $run->handleExceptionFromPHP($exc);
    }

    /**
     * Function parameter to handle the actual error
     */
    protected function getHandler($handler = null)
    {
        $handler = $handler ?: function() {};
        return \Mockery::mock('Whoops\\Handler\\Handler')
            ->shouldReceive('setRun')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setInspector')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('setException')
                ->andReturn(null)
            ->mock()

            ->shouldReceive('handle')
                ->andReturnUsing($handler)
            ->mock();
    }

    protected function getRunInstance()
    {
        $run = new Run();
        $run->allowQuit(false);
        return $run;
    }
}
